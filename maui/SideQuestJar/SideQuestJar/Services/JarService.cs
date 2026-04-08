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

        public JarService()
        {
            _httpClient = new HttpClient { BaseAddress = new Uri(BaseUrl) };
            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");
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
                    var jsonOptions = new JsonSerializerOptions
                    {
                        PropertyNameCaseInsensitive = true,
                        PropertyNamingPolicy = JsonNamingPolicy.SnakeCaseLower,
                        // This converter automatically maps "official_jar" to JarType.OfficialJar
                        Converters = { new JsonStringEnumConverter(JsonNamingPolicy.SnakeCaseLower) }
                    };
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
    }
}
