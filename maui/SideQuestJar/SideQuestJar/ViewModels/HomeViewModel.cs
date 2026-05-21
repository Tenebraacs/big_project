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
    public partial class HomeViewModel : ObservableObject
    {
        public GlobalAppState AppState { get; }
        private readonly AppDbContext _dbContext;
        private readonly JarService _jarService;

        [ObservableProperty] public partial User? CurrentUser { get; set; }
        [ObservableProperty] public partial Jar? MainJar { get; set; }
        [ObservableProperty] public partial JarQuest? MainQuest { get; set; }

        public ObservableCollection<Jar> PopularJars { get; } = new();
        public ObservableCollection<Jar> RecommendedJars { get; } = new();

        public HomeViewModel(GlobalAppState appState, AppDbContext dbContext, JarService jarService)
        {
            AppState = appState;
            _dbContext = dbContext;
            _jarService = jarService;
        }

        [RelayCommand]
        private async Task LoadDiscoverDataAsync()
        {
            CurrentUser = _dbContext.Users.FirstOrDefault();

            var dashboardData = await _jarService.GetDiscoverDataAsync();
            if (dashboardData != null)
            {
                MainJar = dashboardData.MainJar;
                MainQuest = dashboardData.MainQuest;

                PopularJars.Clear();
                foreach (var jar in dashboardData.PopularJars) PopularJars.Add(jar);

                RecommendedJars.Clear();
                foreach (var jar in dashboardData.RecommendedJars) RecommendedJars.Add(jar);
            }
        }

        [RelayCommand]
        private Task NavigateToJarDetailsAsync(Jar? selectedJar)
        {
            // Navigation is handled by Blazor's router (JarDetails.razor)
            return Task.CompletedTask;
        }
    }
}
