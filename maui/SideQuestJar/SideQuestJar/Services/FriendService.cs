using SideQuestJar.Data;
using SideQuestJar.DTOs;
using SideQuestJar.Models;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http.Json;
using System.Text.Json;
using System.Text.Json.Serialization;

namespace SideQuestJar.Services
{
    public class FriendService
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://127.0.0.1:8000/api/";
        private readonly JsonSerializerOptions _jsonOptions;

        public FriendService()
        {
            _httpClient = new HttpClient { BaseAddress = new Uri(BaseUrl) };
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

        // ── Auth header helper (same pattern as AuthService / JarService) ──────
        private async Task SetAuthorizationHeaderAsync()
        {
            var token = await SecureStorage.Default.GetAsync("auth_token");
            if (!string.IsNullOrEmpty(token))
                _httpClient.DefaultRequestHeaders.Authorization =
                    new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", token);
        }

        // ── Get friends page data (BFF: friends + pending both directions) ─────
        public async Task<FriendsResponse?> GetFriendsAsync()
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync("friends");

                if (response.IsSuccessStatusCode)
                {
                    var data = await response.Content.ReadFromJsonAsync<FriendsResponse>(_jsonOptions);
                    if (data != null) await CacheFriendsAsync(data);
                    return data;
                }
                return null;
            }
            catch (HttpRequestException)
            {
                // Offline fallback
                Console.WriteLine("[FriendService] Offline — loading friends from SQLite.");
                return await GetOfflineFriendsAsync();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[FriendService] GetFriends error: {ex.Message}");
                return null;
            }
        }

        // ── User search for the autocomplete dropdown ─────────────────────────
        public async Task<List<UserSearchResult>> SearchUsersAsync(string query)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync(
                    $"users/search?query={Uri.EscapeDataString(query)}");

                if (response.IsSuccessStatusCode)
                {
                    var results = await response.Content.ReadFromJsonAsync<List<UserSearchResult>>(_jsonOptions);
                    return results ?? new List<UserSearchResult>();
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[FriendService] SearchUsers error: {ex.Message}");
            }
            return new List<UserSearchResult>();
        }

        // ── Send friend request by username ───────────────────────────────────
        public async Task<(bool IsSuccess, string Message)> SendFriendRequestAsync(string username)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var payload = new { username };
                var response = await _httpClient.PostAsJsonAsync("friends/add", payload);

                var body = await response.Content.ReadAsStringAsync();
                var msg = ExtractMessage(body) ?? (response.IsSuccessStatusCode ? "Friend request sent." : "Failed to send request.");

                return (response.IsSuccessStatusCode, msg);
            }
            catch (HttpRequestException)
            {
                return (false, "No internet connection.");
            }
            catch (Exception ex)
            {
                return (false, $"Error: {ex.Message}");
            }
        }

        // ── Accept a pending request ───────────────────────────────────────────
        public async Task<(bool IsSuccess, string Message)> AcceptFriendRequestAsync(int userId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.PostAsync($"friends/{userId}/accept", null);

                // Optimistic: update local cache immediately
                await UpdateLocalFriendStatusAsync(userId, "accepted");

                var msg = ExtractMessage(await response.Content.ReadAsStringAsync())
                          ?? (response.IsSuccessStatusCode ? "Friend request accepted." : "Failed to accept.");
                return (response.IsSuccessStatusCode, msg);
            }
            catch (HttpRequestException)
            {
                // Optimistic local update still applied above
                await UpdateLocalFriendStatusAsync(userId, "accepted");
                return (true, "Accepted locally — will sync when online.");
            }
            catch (Exception ex)
            {
                return (false, $"Error: {ex.Message}");
            }
        }

        // ── Decline a pending request ─────────────────────────────────────────
        public async Task<(bool IsSuccess, string Message)> DeclineFriendRequestAsync(int userId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.PostAsync($"friends/{userId}/decline", null);

                // Optimistic: remove from local cache
                await RemoveLocalFriendEntryAsync(userId);

                var msg = ExtractMessage(await response.Content.ReadAsStringAsync())
                          ?? (response.IsSuccessStatusCode ? "Friend request declined." : "Failed to decline.");
                return (response.IsSuccessStatusCode, msg);
            }
            catch (HttpRequestException)
            {
                await RemoveLocalFriendEntryAsync(userId);
                return (true, "Declined locally — will sync when online.");
            }
            catch (Exception ex)
            {
                return (false, $"Error: {ex.Message}");
            }
        }

        // ── Remove an existing friend ──────────────────────────────────────────
        public async Task<(bool IsSuccess, string Message)> RemoveFriendAsync(int userId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.DeleteAsync($"friends/{userId}");

                await RemoveLocalFriendEntryAsync(userId);

                var msg = ExtractMessage(await response.Content.ReadAsStringAsync())
                          ?? (response.IsSuccessStatusCode ? "Friend removed." : "Failed to remove friend.");
                return (response.IsSuccessStatusCode, msg);
            }
            catch (HttpRequestException)
            {
                await RemoveLocalFriendEntryAsync(userId);
                return (true, "Removed locally — will sync when online.");
            }
            catch (Exception ex)
            {
                return (false, $"Error: {ex.Message}");
            }
        }

        // ── SQLite cache helpers ───────────────────────────────────────────────

        // Repository: write all three lists into the FriendEntry table for this user.
        private async Task CacheFriendsAsync(FriendsResponse data)
        {
            try
            {
                using var db = new AppDbContext();
                var localUserId = db.Users.Select(u => u.Id).FirstOrDefault();
                if (localUserId == 0) return;

                // Wipe stale entries for this user and rewrite from API response
                var stale = db.Friends.Where(f => f.LocalUserId == localUserId).ToList();
                db.Friends.RemoveRange(stale);

                foreach (var f in data.Friends)
                    db.Friends.Add(ToEntry(f, localUserId, "accepted"));

                foreach (var f in data.PendingReceived)
                    db.Friends.Add(ToEntry(f, localUserId, "pending_received"));

                foreach (var f in data.PendingSent)
                    db.Friends.Add(ToEntry(f, localUserId, "pending_sent"));

                await db.SaveChangesAsync();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[FriendService] CacheFriends error: {ex.Message}");
            }
        }

        private static FriendEntry ToEntry(FriendDto dto, int localUserId, string status) => new()
        {
            Id            = dto.Id,
            LocalUserId   = localUserId,
            Username      = dto.Username,
            ProfilePicture = dto.ProfilePicture,
            Status        = status
        };

        // Repository: read from SQLite and rebuild the FriendsResponse shape.
        private async Task<FriendsResponse?> GetOfflineFriendsAsync()
        {
            try
            {
                using var db = new AppDbContext();
                var localUserId = db.Users.Select(u => u.Id).FirstOrDefault();
                var all = db.Friends.Where(f => f.LocalUserId == localUserId).ToList();

                return new FriendsResponse
                {
                    Friends         = all.Where(f => f.Status == "accepted").Select(ToDto).ToList(),
                    PendingReceived = all.Where(f => f.Status == "pending_received").Select(ToDto).ToList(),
                    PendingSent     = all.Where(f => f.Status == "pending_sent").Select(ToDto).ToList(),
                };
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[FriendService] GetOfflineFriends error: {ex.Message}");
                return null;
            }
        }

        private static FriendDto ToDto(FriendEntry e) => new()
        {
            Id             = e.Id,
            Username       = e.Username,
            ProfilePicture = e.ProfilePicture
        };

        private async Task UpdateLocalFriendStatusAsync(int userId, string newStatus)
        {
            try
            {
                using var db = new AppDbContext();
                var localUserId = db.Users.Select(u => u.Id).FirstOrDefault();
                var entry = db.Friends.FirstOrDefault(f => f.Id == userId && f.LocalUserId == localUserId);
                if (entry != null)
                {
                    entry.Status = newStatus;
                    db.Friends.Update(entry);
                    await db.SaveChangesAsync();
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[FriendService] UpdateLocalStatus error: {ex.Message}");
            }
        }

        private async Task RemoveLocalFriendEntryAsync(int userId)
        {
            try
            {
                using var db = new AppDbContext();
                var localUserId = db.Users.Select(u => u.Id).FirstOrDefault();
                var entry = db.Friends.FirstOrDefault(f => f.Id == userId && f.LocalUserId == localUserId);
                if (entry != null)
                {
                    db.Friends.Remove(entry);
                    await db.SaveChangesAsync();
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[FriendService] RemoveLocalEntry error: {ex.Message}");
            }
        }

        // ── Utility ───────────────────────────────────────────────────────────
        private static string? ExtractMessage(string json)
        {
            try
            {
                using var doc = JsonDocument.Parse(json);
                return doc.RootElement.TryGetProperty("message", out var m) ? m.GetString() : null;
            }
            catch { return null; }
        }
    }
}
