using SideQuestJar.DTOs;
using System;
using System.Collections.Generic;
using System.Net.Http.Json;
using System.Text;

namespace SideQuestJar.Services
{
    public class AuthService
    {
        private readonly HttpClient _httpClient;

        private const string BaseUrl = "http://127.0.0.1:8000/api/";

        public AuthService()
        {
            _httpClient = new HttpClient
            {
                BaseAddress = new Uri(BaseUrl)
            };

            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");

            string userAgent = $"{AppInfo.Current.Name} MAUI - ({DeviceInfo.Current.Platform}; {DeviceInfo.Current.VersionString})";
            _httpClient.DefaultRequestHeaders.Add("User-Agent", userAgent);
        }


        // Login
        public async Task<bool> LoginAsync(string email, string password)
        {
            try
            {
                string deviceId = $"{DeviceInfo.Current.Platform} - {DeviceInfo.Current.Name}";

                var loginData = new
                {
                    email,
                    password,
                    deviceId
                };

                var response = await _httpClient.PostAsJsonAsync("login", loginData);

                if (response.IsSuccessStatusCode)
                {
                    var result = await response.Content.ReadFromJsonAsync<AuthResponse>();

                    if (result?.Token != null)
                    {
                        await SecureStorage.Default.SetAsync("auth_token", result.Token);
                        return true;
                    }
                }
                return false;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Login error: {ex.Message}");
                return false;
            }
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
                return true;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Logout error: {ex.Message}");
                SecureStorage.Default.Remove("auth_token");
                return true;
            }
        }
    }
}
