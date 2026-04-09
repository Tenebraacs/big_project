using SideQuestJar.DTOs;
using SideQuestJar.Models;
using System;
using System.Collections.Generic;
using System.Net.Http.Json;
using System.Text;
using System.Text.Json;
using System.Text.Json.Serialization;

namespace SideQuestJar.Services
{
    public class JarService
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://127.0.0.1:8000/api/";
        private readonly JsonSerializerOptions jsonOptions;

        public JarService()
        {
            _httpClient = new HttpClient { BaseAddress = new Uri(BaseUrl) };

            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");

            jsonOptions = new JsonSerializerOptions
            {
                PropertyNameCaseInsensitive = true,
                PropertyNamingPolicy = JsonNamingPolicy.SnakeCaseLower,
                // This converter automatically maps "official_jar" to JarType.OfficialJar
                Converters = { new JsonStringEnumConverter(JsonNamingPolicy.SnakeCaseLower) }
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

        // Ezzel lehet majd végtelenül görgetni a jarokat a HomePage-en, egyenlre sehol nincs meghívva
        public async Task<PaginatedResponse<Jar>?> GetJarsAsync(int page = 1)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync($"jars?page={page}");

                if (response.IsSuccessStatusCode)
                    return await response.Content.ReadFromJsonAsync<PaginatedResponse<Jar>>();

                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error fetching paginated jars: {ex.Message}");
                return null;
            }
        }

        public async Task<DiscoverResponse?> GetDiscoverDataAsync()
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync("discover");

                if (response.IsSuccessStatusCode)
                {
                    
                    return await response.Content.ReadFromJsonAsync<DiscoverResponse>(jsonOptions);
                }

                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error fetching discover feed data: {ex.Message}");
                return null;
            }
        }

        public async Task<JarDetailsResponse?> GetJarDetailsAsync(int jarId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();

                var response = await _httpClient.GetAsync($"jars/{jarId}");

                if (response.IsSuccessStatusCode)
                {
                    return await response.Content.ReadFromJsonAsync<JarDetailsResponse>(jsonOptions);
                }

                Console.WriteLine($"[JarService] API returned {response.StatusCode} for Jar {jarId}");
                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error fetching jar details: {ex.Message}");
                return null;
            }
        }

        public async Task<(bool IsSuccess, string Message)> JoinJarAsync(int jarId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();

                var response = await _httpClient.PostAsync($"jars/{jarId}/join", null);

                if (response.IsSuccessStatusCode)
                {
                    return (true, "Successfully joined the jar.");
                }

                if (response.StatusCode == System.Net.HttpStatusCode.Conflict)
                {
                    return (true, "You are already a member of this jar.");
                }

                return (false, "Could not join the jar at this time.");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error joining jar: {ex.Message}");
                return (false, "A network error occurred while trying to join.");
            }
        }
    }
}
