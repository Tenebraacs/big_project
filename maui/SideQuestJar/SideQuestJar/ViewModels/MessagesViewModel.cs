using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using SideQuestJar.DTOs;
using SideQuestJar.Models;
using SideQuestJar.Services;
using SideQuestJar.Data;
using SideQuestJar;
using Microsoft.EntityFrameworkCore;
using System.Collections.ObjectModel;

namespace SideQuestJar.ViewModels
{
    // MVVM: ViewModel for the unified messaging page (Friend list + Chat room)
    // BFF: Interacts with MessageService and JarService for aggregated data
    public partial class MessagesViewModel : ObservableObject
    {
        private readonly FriendService _friendService;
        private readonly MessageService _messageService;
        private readonly JarService _jarService;
        private readonly GlobalAppState _appState;
        private CancellationTokenSource? _pollingCts;
        private int _localUserId;

        [ObservableProperty] public partial bool IsLoading { get; set; }
        [ObservableProperty] public partial string? ErrorMessage { get; set; }
        
        [ObservableProperty] public partial FriendDto? SelectedFriend { get; set; }
        [ObservableProperty] public partial bool IsLoadingChat { get; set; }
        [ObservableProperty] public partial string MessageContent { get; set; } = string.Empty;
        [ObservableProperty] public partial bool IsSending { get; set; }

        public ObservableCollection<FriendDto> Conversations { get; } = new();
        public ObservableCollection<TimelineItemDto> TimelineItems { get; } = new();
        public ObservableCollection<Jar> MyJars { get; } = new();

        public MessagesViewModel(
            FriendService friendService, 
            MessageService messageService, 
            JarService jarService,
            GlobalAppState appState)
        {
            _friendService = friendService;
            _messageService = messageService;
            _jarService = jarService;
            _appState = appState;
        }

        [RelayCommand]
        public async Task LoadConversationsAsync()
        {
            IsLoading = true;
            ErrorMessage = null;
            Conversations.Clear();

            try
            {
                // Ensure we have the local user ID for IsMe checks and jar ownership
                using (var db = new AppDbContext())
                {
                    _localUserId = await db.Users.Select(u => u.Id).FirstOrDefaultAsync();
                }

                var response = await _friendService.GetFriendsAsync();
                if (response != null)
                {
                    foreach (var friend in response.Friends)
                    {
                        Conversations.Add(friend);
                    }
                }
                
                // If we have conversations and none is selected, select the first one
                if (Conversations.Any() && SelectedFriend == null)
                {
                    await SelectFriendAsync(Conversations.First());
                }
            }
            catch (Exception ex)
            {
                ErrorMessage = "Failed to load conversations.";
                Console.WriteLine($"[MessagesViewModel] Error: {ex.Message}");
            }
            finally
            {
                IsLoading = false;
            }
        }

        [RelayCommand]
        public async Task SelectFriendAsync(FriendDto friend)
        {
            if (SelectedFriend?.Id == friend.Id) return;

            SelectedFriend = friend;
            TimelineItems.Clear();
            await LoadChatAsync();
            StartPolling();
        }

        [RelayCommand]
        public async Task LoadChatAsync()
        {
            if (SelectedFriend == null) return;

            IsLoadingChat = true;

            try
            {
                var messages = await _messageService.GetChatTimelineAsync(SelectedFriend.Id);
                
                // Optimized update to avoid flickering
                if (messages.Count != TimelineItems.Count)
                {
                    TimelineItems.Clear();
                    foreach (var msg in messages)
                    {
                        TimelineItems.Add(msg);
                    }
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[MessagesViewModel] Chat Error: {ex.Message}");
            }
            finally
            {
                IsLoadingChat = false;
            }
        }

        [RelayCommand]
        public async Task SendMessageAsync()
        {
            if (SelectedFriend == null || string.IsNullOrWhiteSpace(MessageContent)) return;

            IsSending = true;
            try
            {
                var success = await _messageService.SendMessageAsync(SelectedFriend.Id, MessageContent);
                if (success != null)
                {
                    MessageContent = string.Empty;
                    await LoadChatAsync(); // Refresh
                }
            }
            finally
            {
                IsSending = false;
            }
        }

        [RelayCommand]
        public async Task LoadMyJarsAsync()
        {
            try 
            {
                MyJars.Clear();
                // Load from local database for instant, offline-first results
                using var db = new AppDbContext();
                var jars = await db.Jars
                    .Where(j => j.CreatedBy == _localUserId && j.Id != AppConstants.MainQuestJarId)
                    .ToListAsync();
                    
                foreach (var jar in jars)
                {
                    MyJars.Add(jar);
                }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[MessagesViewModel] LoadJars Error: {ex.Message}");
            }
        }

        [RelayCommand]
        public async Task InviteToJarAsync(int jarId)
        {
            if (SelectedFriend == null) return;

            var result = await _jarService.SendJarInvitationAsync(jarId, SelectedFriend.Id);
            if (result.IsSuccess)
            {
                await LoadChatAsync(); // Refresh timeline to show invite
            }
            else
            {
                ErrorMessage = result.Message;
            }
        }

        public bool IsMe(int senderId) => senderId == _localUserId;

        private void StartPolling()
        {
            _pollingCts?.Cancel();
            _pollingCts = new CancellationTokenSource();
            _ = PollAsync(_pollingCts.Token);
        }

        private async Task PollAsync(CancellationToken token)
        {
            try 
            {
                while (!token.IsCancellationRequested)
                {
                    await Task.Delay(5000, token);
                    if (SelectedFriend != null && !IsSending && !IsLoadingChat)
                    {
                        var messages = await _messageService.GetChatTimelineAsync(SelectedFriend.Id);
                        if (messages.Count != TimelineItems.Count)
                        {
                            // Run on UI thread if needed, but ObservableCollection usually handles it if properly notified
                            // In Blazor, StateHasChanged handles the UI update.
                            TimelineItems.Clear();
                            foreach (var m in messages) TimelineItems.Add(m);
                        }
                    }
                }
            }
            catch (TaskCanceledException) { }
            catch (Exception ex) { Console.WriteLine($"[MessagesViewModel] Polling error: {ex.Message}"); }
        }

        public void StopPolling() => _pollingCts?.Cancel();
    }
}
