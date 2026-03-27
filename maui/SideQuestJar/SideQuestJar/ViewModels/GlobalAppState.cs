using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using SideQuestJar.Services;
using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.ViewModels
{
    public partial class GlobalAppState : ObservableObject
    {
        private readonly AuthService _authService;

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
