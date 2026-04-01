using SideQuestJar.ViewModels;

namespace SideQuestJar.Views;

public partial class HomePage : ContentPage
{
	public HomePage(HomeViewModel viewModel)
	{
		InitializeComponent();
		BindingContext = viewModel;
    }
}