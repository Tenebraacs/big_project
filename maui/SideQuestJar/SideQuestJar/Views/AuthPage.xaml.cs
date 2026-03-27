using SideQuestJar.ViewModels;

namespace SideQuestJar.Views;

public partial class AuthPage : ContentPage
{
	public AuthPage(AuthViewModel viewModel)
	{
		InitializeComponent();

		BindingContext = viewModel;
    }
}