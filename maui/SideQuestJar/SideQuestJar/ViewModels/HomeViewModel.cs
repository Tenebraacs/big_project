using CommunityToolkit.Mvvm.ComponentModel;
using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.ViewModels
{
    public partial class HomeViewModel : ObservableObject
    {
        public GlobalAppState AppState { get; }

        public HomeViewModel(GlobalAppState appState)
        {
            AppState = appState;
        }
    }
}
