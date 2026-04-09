using Microsoft.Extensions.Logging;
using SideQuestJar.Data;
using SideQuestJar.Services;
using SideQuestJar.ViewModels;
using SideQuestJar.Views;

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

            builder.Services.AddDbContext<AppDbContext>();

            builder.Services.AddSingleton<AuthService>();
            builder.Services.AddSingleton<GlobalAppState>();
            builder.Services.AddSingleton<JarService>();

            // Nyitva marad a memóriában de ez később problémás lehet (logout, aggresszív refresh az újratöltésnél)
            builder.Services.AddSingleton<HomePage>();
            builder.Services.AddSingleton<HomeViewModel>();

            builder.Services.AddTransient<AuthViewModel>();
            builder.Services.AddTransient<AuthPage>();
            builder.Services.AddTransient<JarDetailsPage>();
            builder.Services.AddTransient<JarDetailsViewModel>();

#if DEBUG
            builder.Logging.AddDebug();
#endif

            return builder.Build();
        }
    }
}
