using SideQuestJar.DTOs;
using SideQuestJar.Models;
using System;
using System.Collections.Generic;
using System.Net.Http.Json;
using System.Text;

namespace SideQuestJar.Services
{
    public class JarService
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://127.0.0.1:8000/api/";

        public JarService()
        {
            _httpClient = new HttpClient
            {
                BaseAddress = new Uri(BaseUrl)
            };
            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");
        }

        public async Task<PaginatedResponse<Jar>?> GetJarsAsync(int page = 1)
        {
            try
            {
                var token = await SecureStorage.Default.GetAsync("auth_token");
                if (!string.IsNullOrEmpty(token))
                {
                    _httpClient.DefaultRequestHeaders.Authorization =
                        new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", token);
                }

                var response = await _httpClient.GetAsync($"jars?page={page}");

                if (response.IsSuccessStatusCode)
                {
                    return await response.Content.ReadFromJsonAsync<PaginatedResponse<Jar>>();
                }

                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Error fetching jars: {ex.Message}");
                return null;
            }
        }
    }
}
