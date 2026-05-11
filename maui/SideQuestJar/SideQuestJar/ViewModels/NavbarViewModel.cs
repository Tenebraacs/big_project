using System;
using System.Net.Http;
using System.Net.Http.Json;
using System.Threading.Tasks;
using Microsoft.Maui.Storage;
using SideQuestJar.DTOs;
using System.ComponentModel;
using System.Runtime.CompilerServices;

namespace SideQuestJar.ViewModels
{
    public class NavbarViewModel : INotifyPropertyChanged
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://127.0.0.1:8000/api/";

        private bool _isLoggedIn;
        public bool IsLoggedIn
        {
            get => _isLoggedIn;
            set { _isLoggedIn = value; OnPropertyChanged(); }
        }

        private NavbarDataResponse _navbarData;
        public NavbarDataResponse NavbarData
        {
            get => _navbarData;
            set { _navbarData = value; OnPropertyChanged(); }
        }

        public NavbarViewModel()
        {
            _httpClient = new HttpClient { BaseAddress = new Uri(BaseUrl) };
            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");
        }

        public async Task InitializeAsync()
        {
            var token = await SecureStorage.Default.GetAsync("auth_token");
            if (string.IsNullOrEmpty(token))
            {
                IsLoggedIn = false;
                return;
            }

            _httpClient.DefaultRequestHeaders.Authorization =
                new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", token);

            try
            {
                var response = await _httpClient.GetAsync("user/navbar");
                if (response.IsSuccessStatusCode)
                {
                    NavbarData = await response.Content.ReadFromJsonAsync<NavbarDataResponse>();
                    IsLoggedIn = true;
                    return;
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"Navbar Init HTTP Error: {ex.Message}");
            }

            // Fallback to local SQLite user if offline
            try
            {
                using (var db = new SideQuestJar.Data.AppDbContext())
                {
                    var user = db.Users.FirstOrDefault();
                    if (user != null)
                    {
                        NavbarData = new NavbarDataResponse
                        {
                            User = new NavbarUser
                            {
                                Id = user.Id,
                                Username = user.Username,
                                ProfilePictureUrl = user.ProfilePicture,
                                IsAdmin = user.IsAdmin != 0
                            },
                            Streak = 0,
                            TodayIndex = (int)DateTime.Now.DayOfWeek - 1,
                            FilledIndexes = new List<int>()
                        };
                        IsLoggedIn = true;
                        return;
                    }
                }
            }
            catch (Exception dbEx)
            {
                Console.WriteLine($"Navbar Init DB Error: {dbEx.Message}");
            }

            IsLoggedIn = false;
        }

        public event PropertyChangedEventHandler PropertyChanged;
        protected void OnPropertyChanged([CallerMemberName] string propertyName = null)
        {
            PropertyChanged?.Invoke(this, new PropertyChangedEventArgs(propertyName));
        }
    }
}
