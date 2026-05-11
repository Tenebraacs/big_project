using Microsoft.Extensions.DependencyInjection;
using SideQuestJar.Services;
using SideQuestJar.ViewModels;

namespace SideQuestJar
{
    public partial class App : Application
    {
        private readonly GlobalAppState _appstate;
        private readonly AuthService _authService;

        public App(AuthService authService, GlobalAppState appState)
        {
            InitializeComponent();

            _appstate = appState;
            _authService = authService;
        }

        protected override Window CreateWindow(IActivationState? activationState)
        {
            var appShell = new AppShell();

            var window = new Window(appShell);

            return window;
        }
    }
}