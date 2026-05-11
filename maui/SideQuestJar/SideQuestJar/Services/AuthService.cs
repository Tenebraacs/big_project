using SideQuestJar.Data;
using SideQuestJar.DTOs;
using SideQuestJar.Models;
using SideQuestJar;
using Microsoft.Data.Sqlite;
using Microsoft.EntityFrameworkCore;
using System;
using System.Collections.Generic;
using System.Net.Http.Json;
using System.Text;
using System.Text.Json;
using System.Text.Json.Serialization;
using System.Linq;

namespace SideQuestJar.Services
{
    public class AuthService
    {
        private readonly HttpClient _httpClient;

        private const string BaseUrl = "http://127.0.0.1:8000/api/";
        private readonly JsonSerializerOptions _jsonOptions;

        /// <summary>Set after <see cref="LoginAsync"/> when login fails; surfaces API/JSON/SQLite errors in the UI.</summary>
        public string? LastLoginError { get; private set; }

        private static readonly JsonSerializerOptions AuthJsonDeserializeOptions = new()
        {
            PropertyNameCaseInsensitive = true,
            NumberHandling = JsonNumberHandling.AllowReadingFromString
        };

        public AuthService()
        {
            _httpClient = new HttpClient
            {
                BaseAddress = new Uri(BaseUrl)
            };

            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");

            string userAgent = $"{AppInfo.Current.Name} MAUI - ({DeviceInfo.Current.Platform}; {DeviceInfo.Current.VersionString})";
            _httpClient.DefaultRequestHeaders.Add("User-Agent", userAgent);

            _jsonOptions = new JsonSerializerOptions
            {
                PropertyNameCaseInsensitive = true,
                PropertyNamingPolicy = JsonNamingPolicy.SnakeCaseLower,
                Converters = { new JsonStringEnumConverter(JsonNamingPolicy.SnakeCaseLower) }
            };
        }


        // Login
        public async Task<bool> LoginAsync(string email, string password)
        {
            LastLoginError = null;
            try
            {
                string deviceId = $"{DeviceInfo.Current.Platform} - {DeviceInfo.Current.Name}";

                var loginData = new Dictionary<string, string>
                {
                    ["email"] = email,
                    ["password"] = password,
                    ["device_id"] = deviceId
                };

                var response = await _httpClient.PostAsJsonAsync("login", loginData);
                var json = await response.Content.ReadAsStringAsync();

                if (!response.IsSuccessStatusCode)
                {
                    var extracted = TryExtractLaravelMessage(json);
                    LastLoginError = extracted
                        ?? ((int)response.StatusCode == 418
                            ? "Invalid email or password."
                            : $"Login failed ({(int)response.StatusCode}).");
                    return false;
                }

                AuthResponse? result;
                try
                {
                    result = JsonSerializer.Deserialize<AuthResponse>(json, AuthJsonDeserializeOptions);
                }
                catch (JsonException ex)
                {
                    LastLoginError = $"Could not read server login data ({ex.Message}).";
                    Console.WriteLine($"Login JSON parse error. Body (truncated): {TruncateForLog(json)}");
                    return false;
                }

                if (result?.Token is not { Length: > 0 } || result.User is null)
                {
                    LastLoginError = "Login response was missing token or user.";
                    Console.WriteLine($"Login unexpected shape. Body (truncated): {TruncateForLog(json)}");
                    return false;
                }

                await SecureStorage.Default.SetAsync("auth_token", result.Token);

                using (var db = new AppDbContext())
                {
                    await ClearLocalIdentityCacheAsync(db);

                    var user = new User
                    {
                        Id = result.User.Id,
                        Username = result.User.Username,
                        Nickname = result.User.Nickname,
                        Fullname = result.User.Fullname,
                        Email = result.User.Email,
                        ProfilePicture = result.User.ProfilePicture ?? string.Empty,
                        BirthDate = result.User.BirthDate,
                        IsAdmin = result.User.IsAdmin
                    };

                    db.Users.Add(user);
                    await db.SaveChangesAsync();
                }

                return true;
            }
            catch (Exception ex)
            {
                LastLoginError = $"Login error: {ex.Message}";
                Console.WriteLine($"Login error: {ex}");
                return false;
            }
        }

        private static string TruncateForLog(string s, int max = 800) =>
            string.IsNullOrEmpty(s) ? "(empty)" : (s.Length <= max ? s : s[..max] + "…");

        private static string? TryExtractLaravelMessage(string json)
        {
            try
            {
                using var doc = JsonDocument.Parse(json);
                var root = doc.RootElement;
                if (root.TryGetProperty("msg", out var msgEl))
                    return msgEl.GetString();
                if (root.TryGetProperty("message", out var messageEl) && messageEl.ValueKind == JsonValueKind.String)
                    return messageEl.GetString();
            }
            catch { /* ignore */ }
            return null;
        }

        /// <summary>
        /// Clears session rows that reference <see cref="User"/> before removing users.
        /// Older DB files may lack newer tables (EnsureCreated does not migrate); on &quot;no such table&quot; we recreate the schema.
        /// </summary>
        private static async Task ClearLocalIdentityCacheAsync(AppDbContext db)
        {
            try
            {
                db.Friends.RemoveRange(db.Friends);
                db.Messages.RemoveRange(db.Messages);
                db.JarUsers.RemoveRange(db.JarUsers);
                db.QuestCompletions.RemoveRange(db.QuestCompletions);
                db.Likes.RemoveRange(db.Likes);
                db.Libraries.RemoveRange(db.Libraries);
                db.Users.RemoveRange(db.Users);
                await db.SaveChangesAsync();
            }
            catch (Exception ex) when (IsSqliteNoSuchTable(ex))
            {
                db.ChangeTracker.Clear();
                await db.Database.EnsureDeletedAsync();
                await db.Database.EnsureCreatedAsync();
            }
        }

        private static bool IsSqliteNoSuchTable(Exception? ex)
        {
            while (ex != null)
            {
                if (ex is SqliteException sx &&
                    sx.Message.Contains("no such table", StringComparison.OrdinalIgnoreCase))
                    return true;
                ex = ex.InnerException;
            }
            return false;
        }


        // Register
        public async Task<(bool IsSuccess, string SummaryMessage, Dictionary<string, string[]>? FieldErrors)> RegisterAsync(string username, string birthDate, string firstname, string lastname, string email, string password, string passwordConfirmation)
        {
            try
            {
                var registerData = new
                {
                    username = username,
                    birth_date = birthDate,
                    first_name = firstname,
                    last_name = lastname,
                    email = email,
                    password = password,
                    password_confirmation = passwordConfirmation
                };

                var response = await _httpClient.PostAsJsonAsync("register", registerData);

                if (response.IsSuccessStatusCode)
                {
                    return (true, string.Empty, null);
                }

                var errorResult = await response.Content.ReadFromJsonAsync<ValidationErrorResponse>();

                if (errorResult?.Errors != null && errorResult.Errors.Count > 0)
                {
                    return (false, errorResult.Message ?? "Validation error", errorResult.Errors);
                }
                return (false, errorResult?.Message ?? "An unknown error occurred during registration.", null);
            }
            catch (Exception ex)
            {
                return (false, $"Connection Error: {ex.Message}", null);
            }
        }

        // Logout
        public async Task<bool> LogoutAsync()
        {
            try
            {
                var token = await SecureStorage.Default.GetAsync("auth_token");

                if (!string.IsNullOrEmpty(token))
                {
                    _httpClient.DefaultRequestHeaders.Authorization =
                        new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", token);

                    await _httpClient.PostAsync("logout", null);
                }
                SecureStorage.Default.Remove("auth_token");

                using (var db = new AppDbContext())
                {
                    await ClearLocalIdentityCacheAsync(db);
                }

                return true;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Logout error: {ex.Message}");
                SecureStorage.Default.Remove("auth_token");

                using (var db = new AppDbContext())
                {
                    await ClearLocalIdentityCacheAsync(db);
                }

                return true;
            }
        }


        // Auto-Login, ellenőrzéssel a token érvényességére
        public async Task<bool> HasValidSessionAsync()
        {
            var token = await SecureStorage.Default.GetAsync("auth_token");
            if (string.IsNullOrEmpty(token))
                return false;

            _httpClient.DefaultRequestHeaders.Authorization =
                new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", token);

            try
            {
                var response = await _httpClient.GetAsync("user");

                if (response.StatusCode == System.Net.HttpStatusCode.Unauthorized)
                {
                    SecureStorage.Default.Remove("auth_token");
                    using (var db = new AppDbContext())
                    {
                        await ClearLocalIdentityCacheAsync(db);
                    }
                    return false;
                }
            }
            catch (HttpRequestException)
            {
                Console.WriteLine("No internet connection. Falling back to local offline cache.");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error validating session: {ex.Message}");
            }

            using (var db = new AppDbContext())
            {
                var user = db.Users.FirstOrDefault();
                if (user == null)
                    return false;
            }

            return true;
        }

        private async Task SetAuthorizationHeaderAsync()
        {
            var token = await SecureStorage.Default.GetAsync("auth_token");
            if (!string.IsNullOrEmpty(token))
            {
                _httpClient.DefaultRequestHeaders.Authorization =
                    new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", token);
            }
        }

        public async Task<UserProfileResponse?> GetUserProfileAsync()
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync("user/profile");

                if (response.IsSuccessStatusCode)
                {
                    var data = await response.Content.ReadFromJsonAsync<UserProfileResponse>(_jsonOptions);
                    if (data?.User != null)
                    {
                        if (data.MyJars != null)
                            data.MyJars = data.MyJars.Where(j => j.Id != AppConstants.MainQuestJarId).ToList();
                        if (data.JoinedJars != null)
                            data.JoinedJars = data.JoinedJars.Where(j => j.Id != AppConstants.MainQuestJarId).ToList();

                        using var db = new AppDbContext();
                        try
                        {
                            var existing = db.Users.Find(data.User.Id);
                            if (existing != null)
                            {
                                existing.Username = data.User.Username ?? existing.Username;
                                existing.Email = data.User.Email ?? existing.Email;
                                existing.ProfilePicture = data.User.ProfilePicture ?? existing.ProfilePicture;
                                existing.IsAdmin = data.User.IsAdmin;
                                existing.BirthDate = data.User.BirthDate;
                                existing.Nickname = data.User.Nickname ?? existing.Nickname;
                                existing.Fullname = data.User.Fullname ?? existing.Fullname;
                                db.Users.Update(existing);
                            }
                            else
                            {
                                db.Users.Add(data.User);
                            }

                            var allJars = new List<Jar>();
                            if (data.MyJars != null) allJars.AddRange(data.MyJars);
                            if (data.JoinedJars != null) allJars.AddRange(data.JoinedJars);

                            var uniqueJars = allJars.GroupBy(j => j.Id).Select(g => g.First()).ToList();

                            foreach (var jar in uniqueJars)
                            {
                                var existingJar = db.Jars.Find(jar.Id);
                                if (existingJar != null)
                                {
                                    existingJar.Name = jar.Name;
                                    existingJar.Description = jar.Description;
                                    existingJar.Picture = jar.Picture;
                                    existingJar.PictureUrl = jar.PictureUrl;
                                    existingJar.Likes = jar.Likes;
                                    existingJar.Type = jar.Type;
                                    existingJar.CreatedBy = jar.CreatedBy;
                                    existingJar.CreatorUsername = jar.User?.Username ?? jar.CreatorUsername;
                                    db.Jars.Update(existingJar);
                                }
                                else
                                {
                                    jar.CreatorUsername = jar.User?.Username ?? "unknown";
                                    db.Jars.Add(jar);
                                }
                            }
                            await db.SaveChangesAsync();
                        }
                        catch (Exception cacheEx)
                        {
                            Console.WriteLine($"[AuthService] Error caching profile: {cacheEx.Message}");
                            if (cacheEx.InnerException != null)
                                Console.WriteLine($"[AuthService] Inner: {cacheEx.InnerException.Message}");
                        }
                    }

                    return data;
                }
            }
            catch (HttpRequestException ex)
            {
                Console.WriteLine($"Network error fetching profile: {ex.Message}");
                // Offline Fallback
                try
                {
                    using var db = new AppDbContext();
                    var user = db.Users.FirstOrDefault();
                    if (user != null)
                    {
                        var myJars = db.Jars.Where(j => j.CreatedBy == user.Id && j.Id != AppConstants.MainQuestJarId).ToList();
                        var joinedJarIds = db.JarUsers.Where(ju => ju.UserId == user.Id).Select(ju => ju.JarId).ToList();
                        var joinedJars = db.Jars.Where(j =>
                            joinedJarIds.Contains(j.Id) && j.CreatedBy != user.Id && j.Id != AppConstants.MainQuestJarId).ToList();

                        return new UserProfileResponse
                        {
                            User = user,
                            MyJars = myJars,
                            JoinedJars = joinedJars
                        };
                    }
                }
                catch { }
                throw; // Re-throw if offline fallback also fails or wasn't applicable
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error fetching profile: {ex.Message}");
                throw;
            }

            return null;
        }

        public async Task<(bool IsSuccess, string Message)> UpdateProfileAsync(string? username, string? email, byte[]? profilePicture, string? pictureFileName)
        {
            try
            {
                await SetAuthorizationHeaderAsync();

                var form = new MultipartFormDataContent();
                if (!string.IsNullOrEmpty(username))
                    form.Add(new StringContent(username), "username");
                if (!string.IsNullOrEmpty(email))
                    form.Add(new StringContent(email), "email");

                if (profilePicture != null && profilePicture.Length > 0)
                {
                    var imgContent = new ByteArrayContent(profilePicture);
                    imgContent.Headers.ContentType = new System.Net.Http.Headers.MediaTypeHeaderValue("image/jpeg");
                    form.Add(imgContent, "profile_picture", pictureFileName ?? "profile.jpg");
                }

                var response = await _httpClient.PostAsync("user/profile/update", form);

                if (response.IsSuccessStatusCode)
                {
                    // BFF: parse the { user } the API returns and sync the local SQLite cache
                    try
                    {
                        var json = await response.Content.ReadAsStringAsync();
                        using var doc = JsonDocument.Parse(json);
                        if (doc.RootElement.TryGetProperty("user", out var userEl))
                        {
                            var updatedUser = JsonSerializer.Deserialize<User>(userEl.GetRawText(), _jsonOptions);
                            if (updatedUser != null)
                            {
                                using var db = new AppDbContext();
                                var existing = db.Users.Find(updatedUser.Id);
                                if (existing != null)
                                {
                                    existing.Username       = updatedUser.Username;
                                    existing.Email          = updatedUser.Email;
                                    existing.ProfilePicture = updatedUser.ProfilePicture;
                                    existing.IsAdmin        = updatedUser.IsAdmin;
                                    existing.BirthDate      = updatedUser.BirthDate;
                                    db.Users.Update(existing);
                                    await db.SaveChangesAsync();
                                }
                            }
                        }
                    }
                    catch (Exception syncEx)
                    {
                        Console.WriteLine($"SQLite sync after profile update failed: {syncEx.Message}");
                    }

                    return (true, "Profile updated successfully.");
                }

                // Try to surface a meaningful API error message
                var errorBody = await response.Content.ReadAsStringAsync();
                try
                {
                    using var errDoc = JsonDocument.Parse(errorBody);
                    if (errDoc.RootElement.TryGetProperty("message", out var msg))
                        return (false, msg.GetString() ?? "Failed to update profile.");
                }
                catch { }

                return (false, "Failed to update profile.");
            }
            catch (HttpRequestException)
            {
                return (false, "No internet connection.");
            }
            catch (Exception ex)
            {
                return (false, $"Error updating profile: {ex.Message}");
            }
        }

        public async Task<(bool IsSuccess, string Message)> UpdatePasswordAsync(string currentPassword, string newPassword, string newPasswordConfirmation)
        {
            try
            {
                await SetAuthorizationHeaderAsync();

                var payload = new
                {
                    current_password = currentPassword,
                    new_password = newPassword,
                    new_password_confirmation = newPasswordConfirmation
                };

                var response = await _httpClient.PostAsJsonAsync("user/profile/password", payload);

                if (response.IsSuccessStatusCode)
                {
                    return (true, "Password updated successfully.");
                }

                // Parse Laravel JSON error body for a clean user-facing message
                var errorStr = await response.Content.ReadAsStringAsync();
                try
                {
                    using var doc = JsonDocument.Parse(errorStr);
                    if (doc.RootElement.TryGetProperty("message", out var msg))
                        return (false, msg.GetString() ?? "Failed to update password.");
                }
                catch { }

                return (false, "Failed to update password.");
            }
            catch (HttpRequestException)
            {
                return (false, "No internet connection.");
            }
            catch (Exception ex)
            {
                return (false, $"Error updating password: {ex.Message}");
            }
        }
    }
}
