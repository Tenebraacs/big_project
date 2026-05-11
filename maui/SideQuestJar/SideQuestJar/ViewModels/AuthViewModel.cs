using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using SideQuestJar.Services;
using System;
using System.Collections.Generic;
using System.Text;
using System.Globalization;

namespace SideQuestJar.ViewModels
{
    public partial class AuthViewModel : ObservableObject
    {
        private readonly AuthService _authService;
        public GlobalAppState AppState { get; }


        // Login/Register-hez szükséges mezők
        [ObservableProperty] public partial string Email { get; set; } = string.Empty;
        [ObservableProperty] public partial string Password { get; set; } = string.Empty;
        [ObservableProperty] public partial string GenericErrorMessage { get; set; } = string.Empty; // Általános hibaüzenet (pl. hálózati hiba)

        // Register-hez szükséges mezők
        [ObservableProperty] public partial string Username { get; set; } = string.Empty;
        [ObservableProperty] public partial DateTime BirthDate { get; set; } = DateTime.Now;
        [ObservableProperty] public partial string FirstName { get; set; } = string.Empty;
        [ObservableProperty] public partial string LastName { get; set; } = string.Empty;
        [ObservableProperty] public partial string PasswordConfirmation { get; set; } = string.Empty;

        // Register hiba üzenetek
        [ObservableProperty] public partial string UsernameError { get; set; } = string.Empty;
        [ObservableProperty] public partial string EmailError { get; set; } = string.Empty;
        [ObservableProperty] public partial string BirthDateError { get; set; } = string.Empty;
        [ObservableProperty] public partial string PasswordError { get; set; } = string.Empty;

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
                await AppState.RefreshUserAsync();
                await Shell.Current.GoToAsync("//HomePage");
            }
            else
            {
                GenericErrorMessage = string.IsNullOrEmpty(_authService.LastLoginError)
                    ? "Invalid email or password."
                    : _authService.LastLoginError;
            }
        }
        

        [RelayCommand]
        private async Task PerformRegisterAsync()
        {
            ClearAllErrors();

            var (IsSuccess, SummaryMessage, FieldErrors) = await _authService.RegisterAsync(Username, BirthDate.ToString("yyyy-MM-dd", CultureInfo.InvariantCulture), FirstName, LastName, Email, Password, PasswordConfirmation);

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
                if (FieldErrors.ContainsKey("birth_date"))
                    BirthDateError = FieldErrors["birth_date"][0];
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
