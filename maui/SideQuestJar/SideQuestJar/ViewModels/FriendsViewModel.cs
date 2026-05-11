using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using SideQuestJar.DTOs;
using SideQuestJar.Services;
using System;
using System.Collections.Generic;
using System.Threading;
using System.Threading.Tasks;

namespace SideQuestJar.ViewModels
{
    // MVVM: all friends page business logic lives here; Friends.razor only handles lifecycle wiring.
    public partial class FriendsViewModel : ObservableObject
    {
        private readonly FriendService _friendService;

        private const string DefaultAvatar  = "https://i.ibb.co/689N9hY/default.jpg";
        private const string StorageBase    = "http://127.0.0.1:8000/storage/";

        // ── Loading / error state ─────────────────────────────────────────────
        [ObservableProperty] public partial bool IsLoading    { get; set; } = true;
        [ObservableProperty] public partial bool HasError     { get; set; }
        [ObservableProperty] public partial bool IsOffline    { get; set; }
        [ObservableProperty] public partial string? ExceptionMessage { get; set; }

        // ── Action feedback ───────────────────────────────────────────────────
        [ObservableProperty] public partial string? StatusMessage   { get; set; }
        [ObservableProperty] public partial bool    IsStatusSuccess { get; set; }

        // ── Friends lists ─────────────────────────────────────────────────────
        [ObservableProperty] public partial List<FriendDto> Friends         { get; set; } = new();
        [ObservableProperty] public partial List<FriendDto> PendingReceived { get; set; } = new();
        [ObservableProperty] public partial List<FriendDto> PendingSent     { get; set; } = new();

        // ── Add-friend search ─────────────────────────────────────────────────
        [ObservableProperty] public partial string? SearchQuery       { get; set; }
        [ObservableProperty] public partial bool    IsSearching       { get; set; }
        [ObservableProperty] public partial bool    ShowDropdown      { get; set; }
        [ObservableProperty] public partial List<UserSearchResult> SearchResults { get; set; } = new();

        // Debounce: cancel the previous search timer each time the user types.
        private CancellationTokenSource? _searchCts;

        public FriendsViewModel(FriendService friendService)
        {
            _friendService = friendService;
        }

        // ── Shared image URL resolver (mirrors ProfileViewModel) ──────────────
        public static string ResolveAvatar(string? raw) =>
            string.IsNullOrEmpty(raw) ? DefaultAvatar
            : raw.StartsWith("http")  ? raw
            : StorageBase + raw;

        // ── Commands ──────────────────────────────────────────────────────────

        [RelayCommand]
        public async Task LoadFriendsAsync()
        {
            IsLoading    = true;
            HasError     = false;
            IsOffline    = false;
            StatusMessage = null;

            try
            {
                var data = await _friendService.GetFriendsAsync();

                if (data == null)
                {
                    HasError = true;
                    ExceptionMessage = "Could not load friends data.";
                }
                else
                {
                    Friends         = data.Friends;
                    PendingReceived = data.PendingReceived;
                    PendingSent     = data.PendingSent;
                }
            }
            catch (Exception ex)
            {
                HasError         = true;
                ExceptionMessage = ex.Message;
            }
            finally
            {
                IsLoading = false;
            }
        }

        // Triggers on every keystroke from the search input (debounced 350 ms)
        [RelayCommand]
        public async Task SearchUsersAsync(string query)
        {
            _searchCts?.Cancel();
            _searchCts = new CancellationTokenSource();
            var token = _searchCts.Token;

            ShowDropdown = false;

            if (string.IsNullOrWhiteSpace(query) || query.Length < 2)
            {
                SearchResults = new();
                return;
            }

            try
            {
                // Debounce: wait 350 ms before firing the search request
                await Task.Delay(350, token);

                IsSearching = true;
                var results = await _friendService.SearchUsersAsync(query);
                SearchResults = results;
                ShowDropdown  = results.Count > 0;
            }
            catch (OperationCanceledException)
            {
                // user typed again — ignore this result
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[FriendsVM] Search error: {ex.Message}");
            }
            finally
            {
                IsSearching = false;
            }
        }

        // User taps an autocomplete suggestion — populate the input and hide the dropdown
        [RelayCommand]
        public void SelectSearchResult(UserSearchResult user)
        {
            SearchQuery  = user.Username;
            ShowDropdown = false;
            SearchResults = new();
        }

        // User presses "Add" — send the request using whatever is in SearchQuery
        [RelayCommand]
        public async Task SendFriendRequestAsync()
        {
            var username = SearchQuery?.Trim();
            if (string.IsNullOrEmpty(username)) return;

            StatusMessage = null;
            var (isSuccess, message) = await _friendService.SendFriendRequestAsync(username);

            IsStatusSuccess = isSuccess;
            StatusMessage   = message;
            ShowDropdown    = false;

            if (isSuccess)
            {
                SearchQuery = string.Empty;
                // Reload to reflect the newly-sent pending entry
                await LoadFriendsAsync();
            }
        }

        [RelayCommand]
        public async Task AcceptFriendRequestAsync(int userId)
        {
            StatusMessage = null;
            var (isSuccess, message) = await _friendService.AcceptFriendRequestAsync(userId);
            IsStatusSuccess = isSuccess;
            StatusMessage   = message;

            if (isSuccess) await LoadFriendsAsync();
        }

        [RelayCommand]
        public async Task DeclineFriendRequestAsync(int userId)
        {
            StatusMessage = null;
            var (isSuccess, message) = await _friendService.DeclineFriendRequestAsync(userId);
            IsStatusSuccess = isSuccess;
            StatusMessage   = message;

            if (isSuccess) await LoadFriendsAsync();
        }

        [RelayCommand]
        public async Task RemoveFriendAsync(int userId)
        {
            StatusMessage = null;
            var (isSuccess, message) = await _friendService.RemoveFriendAsync(userId);
            IsStatusSuccess = isSuccess;
            StatusMessage   = message;

            if (isSuccess) await LoadFriendsAsync();
        }

        // Hide the dropdown when the user clicks outside (called from @code in the View)
        public void CloseDropdown() => ShowDropdown = false;
    }
}
