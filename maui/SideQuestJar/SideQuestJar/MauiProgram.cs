using Microsoft.Extensions.Logging;
using SideQuestJar.Data;
using SideQuestJar.Services;
using SideQuestJar.ViewModels;
using SideQuestJar.Views;
using Blazorise;
using Blazorise.Bootstrap5;
using Blazorise.Icons.Bootstrap;
using Microsoft.AspNetCore.Components.Authorization;

namespace SideQuestJar
{
    public static class MauiProgram
    {
        public static MauiApp CreateMauiApp()
        {
            var builder = MauiApp.CreateBuilder();
            builder
                .UseMauiApp<App>()
                .ConfigureFonts(fonts =>
                {
                    fonts.AddFont("OpenSans-Regular.ttf", "OpenSansRegular");
                    fonts.AddFont("OpenSans-Semibold.ttf", "OpenSansSemibold");
                });

            // 1. Add Blazor WebView support
            builder.Services.AddMauiBlazorWebView();

#if DEBUG
            // This enables hitting F12 to inspect the HTML/CSS while debugging!
            builder.Services.AddBlazorWebViewDeveloperTools();
            builder.Logging.AddDebug();
#endif

            // 2. Add Blazorise Services
            // 1. Add Blazorise with Bootstrap Icons
            builder.Services
                .AddBlazorise(options => {
                    options.Immediate = true;
                })
                .AddBootstrap5Providers()
                .AddBootstrapIcons(); // Swapped!

            // 2. Add Authorization UI capabilities
            builder.Services.AddAuthorizationCore();

            builder.Services.AddDbContext<AppDbContext>();
            builder.Services.AddSingleton<AuthService>();
            builder.Services.AddSingleton<JarService>();
            builder.Services.AddSingleton<FriendService>();
            builder.Services.AddSingleton<MessageService>();
            builder.Services.AddSingleton<AdminService>();
            builder.Services.AddSingleton<GlobalAppState>();

            // Note: We register ViewModels so we can inject them into .razor files
            builder.Services.AddTransient<AuthViewModel>();
            builder.Services.AddTransient<HomeViewModel>();
            builder.Services.AddTransient<ProfileViewModel>(); // MVVM
            builder.Services.AddTransient<FriendsViewModel>(); // MVVM
            builder.Services.AddTransient<MessagesViewModel>(); // MVVM
            builder.Services.AddTransient<AdminViewModel>(); // MVVM
            builder.Services.AddSingleton<NavbarViewModel>();

            return builder.Build();
        }
    }
}
