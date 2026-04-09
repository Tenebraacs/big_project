using CommunityToolkit.Mvvm.ComponentModel;
using CommunityToolkit.Mvvm.Input;
using SideQuestJar.Data;
using SideQuestJar.Models;
using SideQuestJar.Services;
using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Text;

namespace SideQuestJar.ViewModels
{
    [QueryProperty(nameof(JarId), "JarId")]
    public partial class JarDetailsViewModel : ObservableObject
    {
        private readonly JarService _jarService;
        private readonly AppDbContext _dbContext;

        [ObservableProperty]
        public partial int JarId { get; set; }

        [ObservableProperty]
        public partial Jar? CurrentJar { get; set; }

        [ObservableProperty]
        public partial bool IsJoined { get; set; }

        [ObservableProperty]
        public partial string GenericErrorMessage { get; set; } = string.Empty;

        public ObservableCollection<JarQuest> Quests { get; } = new();

        public JarDetailsViewModel(JarService jarService, AppDbContext dbContext)
        {
            _jarService = jarService;
            _dbContext = dbContext;
        }

        partial void OnJarIdChanged(int value)
        {
            _ = LoadJarDetailsAsync();
        }

        [RelayCommand]
        private async Task LoadJarDetailsAsync()
        {
            GenericErrorMessage = string.Empty;

            try
            {
                var details = await _jarService.GetJarDetailsAsync(JarId);

                if (details != null)
                {
                    CurrentJar = details.Jar;
                    IsJoined = details.IsJoined;

                    Quests.Clear();
                    if (details.Quests != null)
                    {
                        foreach (var quest in details.Quests)
                        {
                            Quests.Add(quest);
                        }
                    }
                }
                else
                {
                    GenericErrorMessage = "Could not load the Jar details. Please check your connection and try again.";
                }
            }
            catch (HttpRequestException)
            {
                GenericErrorMessage = "Network error: Unable to reach the server. Please check your internet connection.";
            }
            catch (Exception)
            {
                GenericErrorMessage = "An unexpected error occurred while loading the Jar.";
            }
        }

        [RelayCommand]
        private async Task JoinJarAsync()
        {
            if (CurrentJar == null || IsJoined) return;

            GenericErrorMessage = string.Empty;

            try
            {
                var (isSuccess, message) = await _jarService.JoinJarAsync(JarId);

                if (isSuccess)
                {
                    IsJoined = true;
                    GenericErrorMessage = message;
                }
                else
                {
                    GenericErrorMessage = message;
                }
            }
            catch (HttpRequestException)
            {
                GenericErrorMessage = "Network error: Could not connect to the server to join the Jar.";
            }
            catch (Exception)
            {
                GenericErrorMessage = "An unexpected error occurred while trying to join the Jar.";
            }
        }
    }
}
