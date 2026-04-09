using SideQuestJar.ViewModels;

namespace SideQuestJar.Views;

public partial class JarDetailsPage : ContentPage
{
    private readonly JarDetailsViewModel _viewModel;
    private int _currentIndex = 0;

    public JarDetailsPage(JarDetailsViewModel viewModel)
    {
        InitializeComponent();
        _viewModel = viewModel;
        BindingContext = _viewModel;

        QuestsCarousel.HandlerChanged += OnCarouselHandlerChanged;
    }

    private void ScrollLeft_Clicked(object sender, EventArgs e)
    {
        if (_currentIndex > 0)
        {
            _currentIndex--;
            QuestsCarousel.ScrollTo(_currentIndex, position: ScrollToPosition.Center, animate: true);
        }
    }

    private void ScrollRight_Clicked(object sender, EventArgs e)
    {
        if (_viewModel.Quests.Count == 0) return;

        if (_currentIndex < _viewModel.Quests.Count - 1)
        {
            _currentIndex++;
            QuestsCarousel.ScrollTo(_currentIndex, position: ScrollToPosition.Center, animate: true);
        }
    }

    private void OnCarouselHandlerChanged(object? sender, EventArgs e)
    {
#if WINDOWS
        if (sender is CollectionView cv && cv.Handler?.PlatformView is Microsoft.UI.Xaml.FrameworkElement platformView)
        {
            var wheelHandler = new Microsoft.UI.Xaml.Input.PointerEventHandler(OnPointerWheelChanged);
            platformView.RemoveHandler(Microsoft.UI.Xaml.UIElement.PointerWheelChangedEvent, wheelHandler);
            platformView.AddHandler(Microsoft.UI.Xaml.UIElement.PointerWheelChangedEvent, wheelHandler, true);

            platformView.Loaded -= OnPlatformViewLoaded;
            platformView.Loaded += OnPlatformViewLoaded;
        }
#endif
    }

#if WINDOWS
    private static void OnPlatformViewLoaded(object sender, Microsoft.UI.Xaml.RoutedEventArgs e)
    {
        if (sender is not Microsoft.UI.Xaml.FrameworkElement platformView) return;

        var scrollViewer = FindVisualChild<Microsoft.UI.Xaml.Controls.ScrollViewer>(platformView);
        if (scrollViewer is null) return;

        scrollViewer.HorizontalScrollMode = Microsoft.UI.Xaml.Controls.ScrollMode.Disabled;
        scrollViewer.HorizontalScrollBarVisibility = Microsoft.UI.Xaml.Controls.ScrollBarVisibility.Hidden;

        var wheelHandler = new Microsoft.UI.Xaml.Input.PointerEventHandler(OnPointerWheelChanged);
        scrollViewer.RemoveHandler(Microsoft.UI.Xaml.UIElement.PointerWheelChangedEvent, wheelHandler);
        scrollViewer.AddHandler(Microsoft.UI.Xaml.UIElement.PointerWheelChangedEvent, wheelHandler, true);
    }

    private static void OnPointerWheelChanged(object sender, Microsoft.UI.Xaml.Input.PointerRoutedEventArgs e)
    {
        e.Handled = true;
    }

    private static T? FindVisualChild<T>(Microsoft.UI.Xaml.DependencyObject parent) where T : Microsoft.UI.Xaml.DependencyObject
    {
        for (int i = 0; i < Microsoft.UI.Xaml.Media.VisualTreeHelper.GetChildrenCount(parent); i++)
        {
            var child = Microsoft.UI.Xaml.Media.VisualTreeHelper.GetChild(parent, i);
            if (child is T typedChild) return typedChild;

            var result = FindVisualChild<T>(child);
            if (result is not null) return result;
        }
        return null;
    }
#endif
}