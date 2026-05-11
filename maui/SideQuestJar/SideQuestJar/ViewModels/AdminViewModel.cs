using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using SideQuestJar.Models;
using SideQuestJar.Services;
using System.Collections.ObjectModel;

namespace SideQuestJar.ViewModels
{
    public partial class AdminViewModel : ObservableObject
    {
        private readonly AdminService _adminService;
        private readonly NavbarViewModel _navbarViewModel;

        [ObservableProperty] private bool _isLoading;
        [ObservableProperty] private string? _errorMessage;
        [ObservableProperty] private string? _successMessage;
        [ObservableProperty] private string _searchQuery = string.Empty;
        [ObservableProperty] private AdminUserResponse? _usersResponse;
        
        public ObservableCollection<AdminUserDto> Users { get; } = new();

        public AdminViewModel(AdminService adminService, NavbarViewModel navbarViewModel)
        {
            _adminService = adminService;
            _navbarViewModel = navbarViewModel;
        }

        [RelayCommand]
        public async Task LoadUsersAsync(int page = 1)
        {
            IsLoading = true;
            ErrorMessage = null;
            try
            {
                var response = await _adminService.GetUsersAsync(page, SearchQuery);
                if (response != null)
                {
                    UsersResponse = response;
                    Users.Clear();
                    foreach (var user in response.Data)
                    {
                        Users.Add(user);
                    }
                }
                else
                {
                    ErrorMessage = _adminService.LastUsersLoadError ?? "Failed to load users.";
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = $"An error occurred: {ex.Message}";
            }
            finally
            {
                IsLoading = false;
            }
        }

        [RelayCommand]
        public async Task SearchAsync()
        {
            await LoadUsersAsync(1);
        }

        [RelayCommand]
        public async Task ToggleAdminAsync(AdminUserDto user)
        {
            if (user.Id == _navbarViewModel.NavbarData?.User?.Id)
            {
                ErrorMessage = "You cannot remove your own admin status.";
                return;
            }

            ErrorMessage = null;
            SuccessMessage = null;
            try
            {
                var success = await _adminService.ToggleAdminAsync(user.Id);
                if (success)
                {
                    user.IsAdmin = !user.IsAdmin;
                    SuccessMessage = $"Admin status for {user.Username} updated.";
                    // Trigger property change for the list to refresh
                    OnPropertyChanged(nameof(Users));
                }
                else
                {
                    ErrorMessage = "Failed to update admin status.";
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = $"An error occurred: {ex.Message}";
            }
        }

        [RelayCommand]
        public async Task DeleteUserAsync(AdminUserDto user)
        {
            if (user.Id == _navbarViewModel.NavbarData?.User?.Id)
            {
                ErrorMessage = "You cannot delete yourself.";
                return;
            }

            ErrorMessage = null;
            SuccessMessage = null;
            try
            {
                var success = await _adminService.DeleteUserAsync(user.Id);
                if (success)
                {
                    Users.Remove(user);
                    SuccessMessage = $"User {user.Username} deleted.";
                }
                else
                {
                    ErrorMessage = "Failed to delete user.";
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = $"An error occurred: {ex.Message}";
            }
        }
    }
}
