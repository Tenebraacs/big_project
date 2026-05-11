using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using Microsoft.JSInterop;
using SideQuestJar.DTOs;
using SideQuestJar.Services;
using System.Text.Json;
using System.Text.Json.Serialization;

namespace SideQuestJar.ViewModels
{
    // MVVM: ViewModel separates all profile business logic from the View
    public partial class ProfileViewModel : ObservableObject
    {
        private readonly AuthService _authService;
        private readonly IJSRuntime _js;

        // ── Constants ──────────────────────────────────────────────────────
        private const string DefaultAvatar = "https://i.ibb.co/689N9hY/default.jpg";
        private const string StorageBase = "http://127.0.0.1:8000/storage/";

        // ── Observable state ─────────────────────────────────────────────────
        [ObservableProperty] private UserProfileResponse? _profileData;
        [ObservableProperty] private bool _isLoading = true;
        [ObservableProperty] private bool _hasError;
        [ObservableProperty] private string? _exceptionMessage;
        [ObservableProperty] private bool _isUploading;
        [ObservableProperty] private string? _successMessage;
        [ObservableProperty] private string? _errorMessage;

        // ── Edit-mode flags ───────────────────────────────────────────────────
        [ObservableProperty] private bool _editUsername;
        [ObservableProperty] private bool _editEmail;
        [ObservableProperty] private bool _editPassword;
        [ObservableProperty] private bool _editPicture;
        [ObservableProperty] private bool _showCropper;

        // ── Form temporaries ──────────────────────────────────────────────────
        [ObservableProperty] private string? _tempUsername;
        [ObservableProperty] private string? _tempEmail;
        [ObservableProperty] private string? _currentPassword;
        [ObservableProperty] private string? _newPassword;
        [ObservableProperty] private string? _confirmPassword;
        [ObservableProperty] private string? _selectedFileName;

        // ── Computed props (single source of truth) ───────────────

        // ── Image URL resolver ────────────────────────────────────────────────
        public static string ResolveProfilePicture(string? raw) =>
            string.IsNullOrEmpty(raw) ? DefaultAvatar
            : raw.StartsWith("http") ? raw
            : StorageBase + raw;

        public string CurrentProfilePictureUrl =>
            ResolveProfilePicture(ProfileData?.User?.ProfilePicture);

        public ProfileViewModel(AuthService authService, IJSRuntime js)
        {
            _authService = authService;
            _js = js;
        }

        // ── Commands ──────────────────────────────────────────────────────────

        [RelayCommand]
        public async Task LoadProfileAsync()
        {
            IsLoading = true;
            HasError = false;
            ExceptionMessage = null;
            SuccessMessage = null;
            ErrorMessage = null;

            try
            {
                ProfileData = await _authService.GetUserProfileAsync();

                if (ProfileData?.User == null)
                {
                    HasError = true;
                    ExceptionMessage = "Profile data or user was null.";
                }
                else
                {
                    // Notify computed props depend on ProfileData
                    OnPropertyChanged(nameof(CurrentProfilePictureUrl));
                    await ResetEditStateAsync();
                }
            }
            catch (Exception ex)
            {
                HasError = true;
                ExceptionMessage = ex.InnerException != null ? $"{ex.Message} -> {ex.InnerException.Message}" : ex.Message;
            }
            finally
            {
                IsLoading = false;
            }
        }

        [RelayCommand]
        public async Task StartEditAsync(string field)
        {
            await ResetEditStateAsync();
            SuccessMessage = null;
            ErrorMessage = null;

            EditUsername = field == "username";
            EditEmail    = field == "email";
            EditPassword = field == "password";
            EditPicture  = field == "picture";
        }

        [RelayCommand]
        public async Task CancelEditAsync()
        {
            await ResetEditStateAsync();
            SuccessMessage = null;
            ErrorMessage = null;
        }

        // Shared internal reset — does not clear feedback messages (callers decide)
        private async Task ResetEditStateAsync()
        {
            if (ProfileData?.User != null)
            {
                TempUsername = ProfileData.User.Username;
                TempEmail    = ProfileData.User.Email;
            }

            EditUsername = false;
            EditEmail    = false;
            EditPassword = false;
            EditPicture  = false;

            CurrentPassword = string.Empty;
            NewPassword     = string.Empty;
            ConfirmPassword = string.Empty;

            if (ShowCropper)
            {
                ShowCropper = false;
                try { await _js.InvokeVoidAsync("destroyCropper"); } catch { }
            }
        }

        // Invoked from Razor when InputFile fires — sets up the cropper
        public async Task HandleFileSelectedAsync(byte[] bytes, string contentType, string fileName)
        {
            if (bytes.Length > 4_096_000)
            {
                ErrorMessage = "Image is too large. Maximum size is 4 MB.";
                return;
            }

            SelectedFileName = fileName;
            var dataUrl = $"data:{contentType};base64,{Convert.ToBase64String(bytes)}";

            ShowCropper = true;
            
            // Wait for Blazor to flush the DOM update so <img id="cropImage"> exists
            await Task.Delay(100);
            
            // JSInterop: call initCropper defined in index.html
            await _js.InvokeVoidAsync("initCropper", "cropImage", dataUrl);
        }

        [RelayCommand]
        public async Task SavePictureAsync()
        {
            if (!ShowCropper)
            {
                ErrorMessage = "Please choose an image first.";
                return;
            }

            IsUploading = true;
            ErrorMessage = null;
            SuccessMessage = null;

            try
            {
                // JSInterop: getCroppedImageBase64 defined in index.html
                var base64 = await _js.InvokeAsync<string>("getCroppedImageBase64", 0.85);
                if (string.IsNullOrEmpty(base64))
                {
                    ErrorMessage = "Could not read the cropped image.";
                    return;
                }

                var imageBytes = Convert.FromBase64String(base64);
                var result = await _authService.UpdateProfileAsync(null, null, imageBytes, SelectedFileName ?? "profile.jpg");

                if (result.IsSuccess)
                {
                    SuccessMessage = "Profile picture updated successfully!";
                    await LoadProfileAsync();
                }
                else
                {
                    ErrorMessage = result.Message;
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = $"An error occurred: {ex.Message}";
            }
            finally
            {
                IsUploading = false;
                if (SuccessMessage != null)
                    await ResetEditStateAsync();
            }
        }

        [RelayCommand]
        public async Task SaveUsernameAsync()
        {
            if (string.IsNullOrWhiteSpace(TempUsername))
            {
                ErrorMessage = "Username is required.";
                return;
            }

            IsUploading = true;
            ErrorMessage = null;
            SuccessMessage = null;

            try
            {
                // Only send username — email stays untouched
                var result = await _authService.UpdateProfileAsync(TempUsername, null, null, null);
                if (result.IsSuccess)
                {
                    SuccessMessage = "Username updated successfully!";
                    await LoadProfileAsync();
                }
                else
                {
                    ErrorMessage = result.Message;
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = $"An error occurred: {ex.Message}";
            }
            finally
            {
                IsUploading = false;
            }
        }

        [RelayCommand]
        public async Task SaveEmailAsync()
        {
            if (string.IsNullOrWhiteSpace(TempEmail))
            {
                ErrorMessage = "Email is required.";
                return;
            }

            IsUploading = true;
            ErrorMessage = null;
            SuccessMessage = null;

            try
            {
                // Only send email — username stays untouched
                var result = await _authService.UpdateProfileAsync(null, TempEmail, null, null);
                if (result.IsSuccess)
                {
                    SuccessMessage = "Email updated successfully!";
                    await LoadProfileAsync();
                }
                else
                {
                    ErrorMessage = result.Message;
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = $"An error occurred: {ex.Message}";
            }
            finally
            {
                IsUploading = false;
            }
        }

        [RelayCommand]
        public async Task SavePasswordAsync()
        {
            // Fix: validate all three fields, not just two
            if (string.IsNullOrWhiteSpace(CurrentPassword) ||
                string.IsNullOrWhiteSpace(NewPassword) ||
                string.IsNullOrWhiteSpace(ConfirmPassword))
            {
                ErrorMessage = "Please fill in all password fields.";
                return;
            }

            if (NewPassword != ConfirmPassword)
            {
                ErrorMessage = "New passwords do not match.";
                return;
            }

            IsUploading = true;
            ErrorMessage = null;
            SuccessMessage = null;

            try
            {
                var result = await _authService.UpdatePasswordAsync(CurrentPassword, NewPassword, ConfirmPassword);
                if (result.IsSuccess)
                {
                    SuccessMessage = "Password updated successfully!";
                    await ResetEditStateAsync();
                }
                else
                {
                    ErrorMessage = result.Message;
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = $"An error occurred: {ex.Message}";
            }
            finally
            {
                IsUploading = false;
            }
        }
    }
}
