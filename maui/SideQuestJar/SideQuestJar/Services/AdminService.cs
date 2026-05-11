using SideQuestJar.Models;
using System;
using System.Net.Http;
using System.Text.Json;
using System.Text.Json.Serialization;
using System.Threading.Tasks;

namespace SideQuestJar.Services
{
    public class AdminService
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://127.0.0.1:8000/api/";
        private readonly JsonSerializerOptions _jsonOptions;

        /// <summary>Populated when <see cref="GetUsersAsync"/> fails (HTTP error, JSON parse, etc.).</summary>
        public string? LastUsersLoadError { get; private set; }

        public AdminService()
        {
            _httpClient = new HttpClient { BaseAddress = new Uri(BaseUrl) };
            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");

            // Explicit JsonPropertyName on DTOs matches Laravel keys; avoid extra naming policy conflicts.
            _jsonOptions = new JsonSerializerOptions
            {
                PropertyNameCaseInsensitive = true,
                NumberHandling = JsonNumberHandling.AllowReadingFromString
            };
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

        public async Task<AdminUserResponse?> GetUsersAsync(int page = 1, string search = "")
        {
            LastUsersLoadError = null;
            try
            {
                await SetAuthorizationHeaderAsync();
                var query = $"admin/users?page={page}";
                if (!string.IsNullOrWhiteSpace(search))
                {
                    query += $"&search={Uri.EscapeDataString(search)}";
                }
                
                var response = await _httpClient.GetAsync(query);
                var json = await response.Content.ReadAsStringAsync();

                if (!response.IsSuccessStatusCode)
                {
                    LastUsersLoadError = TryExtractLaravelMessage(json)
                        ?? $"Could not load users (HTTP {(int)response.StatusCode}).";
                    Console.WriteLine($"[AdminService] GET admin/users HTTP {(int)response.StatusCode}: {Truncate(json)}");
                    return null;
                }

                try
                {
                    var parsed = JsonSerializer.Deserialize<AdminUserResponse>(json, _jsonOptions);
                    if (parsed == null)
                    {
                        LastUsersLoadError = "Empty admin users response.";
                        return null;
                    }

                    return parsed;
                }
                catch (JsonException jex)
                {
                    LastUsersLoadError = $"Invalid response from server: {jex.Message}";
                    Console.WriteLine($"[AdminService] Admin users JSON parse failed: {jex.Message}. Body: {Truncate(json)}");
                    return null;
                }
            }
            catch (Exception ex)
            {
                LastUsersLoadError = ex.Message;
                Console.WriteLine($"[AdminService] Error fetching admin users: {ex}");
                return null;
            }
        }

        private static string? TryExtractLaravelMessage(string json)
        {
            try
            {
                using var doc = JsonDocument.Parse(json);
                var root = doc.RootElement;
                if (root.TryGetProperty("message", out var msg) && msg.ValueKind == JsonValueKind.String)
                    return msg.GetString();
            }
            catch { /* ignore */ }
            return null;
        }

        public async Task<bool> ToggleAdminAsync(int userId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.PostAsync($"admin/users/{userId}/toggle-admin", null);
                return response.IsSuccessStatusCode;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[AdminService] Error toggling admin status: {ex.Message}");
                return false;
            }
        }

        public async Task<bool> DeleteUserAsync(int userId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.DeleteAsync($"admin/users/{userId}");
                return response.IsSuccessStatusCode;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[AdminService] Error deleting user: {ex.Message}");
                return false;
            }
        }

        private static string Truncate(string? s, int max = 600)
            => string.IsNullOrEmpty(s) ? "(empty)" : (s.Length <= max ? s : s[..max] + "…");
    }
}
