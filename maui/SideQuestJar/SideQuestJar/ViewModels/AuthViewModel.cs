using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using SideQuestJar.Services;
using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.ViewModels
{
    public partial class AuthViewModel : ObservableObject
    {
        private readonly AuthService _authService;
        public GlobalAppState AppState { get; }


        // Login/Register-hez szükséges mezők
        [ObservableProperty] public partial string Email { get; set; }
        [ObservableProperty] public partial string Password { get; set; }
        [ObservableProperty] public partial string GenericErrorMessage { get; set; } // Általános hibaüzenet (pl. hálózati hiba)

        // Register-hez szükséges mezők
        [ObservableProperty] public partial string Username { get; set; }
        [ObservableProperty] public partial string FirstName { get; set; }
        [ObservableProperty] public partial string LastName { get; set; }
        [ObservableProperty] public partial string PasswordConfirmation { get; set; }

        // Register hiba üzenetek
        [ObservableProperty] public partial string UsernameError { get; set; }
        [ObservableProperty] public partial string EmailError { get; set; }
        [ObservableProperty] public partial string BirthDateError { get; set; }
        [ObservableProperty] public partial string PasswordError { get; set; }

        // Konstruktor
        public AuthViewModel(AuthService authService, GlobalAppState appState)
        {
            _authService = authService;
            AppState = appState;
        }

        [RelayCommand]
        private async Task PerformLoginAsync()
        {
            ClearAllErrors();

            if (string.IsNullOrWhiteSpace(Email) || string.IsNullOrWhiteSpace(Password))
            {
                GenericErrorMessage = "Please fill in all fields.";
                return;
            }

            bool isSuccess = await _authService.LoginAsync(Email, Password);

            if (isSuccess)
            {
                AppState.IsLoggedIn = true;
                await Shell.Current.GoToAsync("//HomePage");

            }
            else
            {
                GenericErrorMessage = "Invalid email or password.";
            }
        }

        [RelayCommand]
        private async Task PerformRegisterAsync()
        {
            ClearAllErrors();

            var (IsSuccess, SummaryMessage, FieldErrors) = await _authService.RegisterAsync(Username, FirstName, LastName, Email, Password, PasswordConfirmation);

            if (IsSuccess)
            {
                AppState.IsLoginViewActive = true;
                AppState.IsRegisterViewActive = false;
                GenericErrorMessage = "Registration successful! Please log in.";
            }
            else if (FieldErrors != null)
            {
                if (FieldErrors.ContainsKey("username"))
                    UsernameError = FieldErrors["username"][0];

                if (FieldErrors.ContainsKey("email"))
                    EmailError = FieldErrors["email"][0];

                if (FieldErrors.ContainsKey("password"))
                    PasswordError = FieldErrors["password"][0];
            }
            else
            {
                GenericErrorMessage = SummaryMessage;
            }
        }

        private void ClearAllErrors()
        {
            GenericErrorMessage = string.Empty;
            UsernameError = string.Empty;
            EmailError = string.Empty;
            BirthDateError = string.Empty;
            PasswordError = string.Empty;
        }
    }
}
