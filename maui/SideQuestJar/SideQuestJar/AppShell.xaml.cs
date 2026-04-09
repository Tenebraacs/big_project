using SideQuestJar.Views;

namespace SideQuestJar
{
    public partial class AppShell : Shell
    {
        public AppShell()
        {
            InitializeComponent();

            // Ez egy 'elrejtett oldal', nem része az oldal fő navigációjának (DynamicPage)
            Routing.RegisterRoute(nameof(JarDetailsPage), typeof(JarDetailsPage));
        }
    }
}
