using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using SideQuestJar.Services;
using SideQuestJar.Data;
using Microsoft.EntityFrameworkCore;
using System;
using System.Collections.Generic;
using System.Text;

using SideQuestJar.Models;

namespace SideQuestJar.ViewModels
{
    public partial class GlobalAppState : ObservableObject
    {
        private readonly AuthService _authService;

        [ObservableProperty]
        public partial User? CurrentUser { get; set; }


        [ObservableProperty]
        [NotifyPropertyChangedFor(nameof(IsLoggedOut))]
        public partial bool IsLoggedIn { get; set; } = false;

        public bool IsLoggedOut => !IsLoggedIn;

        [ObservableProperty]
        public partial bool IsLoginViewActive { get; set; } = true;

        [ObservableProperty]
        public partial bool IsRegisterViewActive { get; set; } = false;

        public GlobalAppState(AuthService authService)
        {
            _authService = authService;
        }

        [RelayCommand]
        private async Task ShowLoginView()
        {
            IsLoginViewActive = true;
            IsRegisterViewActive = false;
            await Shell.Current.GoToAsync("//AuthPage");
        }

        [RelayCommand]
        private async Task ShowRegisterView()
        {
            IsLoginViewActive = false;
            IsRegisterViewActive = true;
            await Shell.Current.GoToAsync("//AuthPage");
        }

        [RelayCommand]
        public async Task RefreshUserAsync()
        {
            try
            {
                using var db = new AppDbContext();
                CurrentUser = await db.Users.FirstOrDefaultAsync();
                IsLoggedIn = CurrentUser != null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[GlobalAppState] RefreshUser error: {ex.Message}");
            }
        }

        [RelayCommand]
        private async Task LogoutAsync()
        {
            await _authService.LogoutAsync();
            IsLoggedIn = false;
            await Shell.Current.GoToAsync("//AuthPage");
        }

        [RelayCommand]
        private async Task GoHomeAsync()
        {
            await Shell.Current.GoToAsync("//HomePage");
        }
    }
}
