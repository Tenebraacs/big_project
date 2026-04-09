using SideQuestJar.ViewModels;
using System.Collections;

namespace SideQuestJar.Views;

public partial class HomePage : ContentPage
{
    private readonly HomeViewModel _viewModel;
    private readonly Dictionary<CollectionView, int> _carouselIndexes = new();
    private const int MaxVisibleCards = 5;

    public HomePage(HomeViewModel viewModel)
    {
        InitializeComponent();
        _viewModel = viewModel;
        BindingContext = _viewModel;

        PopularCarousel.HandlerChanged += OnCarouselHandlerChanged;
        RecommendedCarousel.HandlerChanged += OnCarouselHandlerChanged;
    }

    protected override void OnAppearing()
    {
        base.OnAppearing();
        if (!_viewModel.LoadDiscoverDataCommand.IsRunning)
        {
            _viewModel.LoadDiscoverDataCommand.Execute(null);
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
        if (sender is not Microsoft.UI.Xaml.FrameworkElement platformView)
        {
            return;
        }

        var scrollViewer = FindVisualChild<Microsoft.UI.Xaml.Controls.ScrollViewer>(platformView);
        if (scrollViewer is null)
        {
            return;
        }

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
            if (child is T typedChild)
            {
                return typedChild;
            }

            var result = FindVisualChild<T>(child);
            if (result is not null)
            {
                return result;
            }
        }

        return null;
    }
#endif

    private void ScrollLeft_Clicked(object sender, EventArgs e)
    {
        if (sender is Button btn && btn.BindingContext is CollectionView carousel)
        {
            int currentIndex = _carouselIndexes.GetValueOrDefault(carousel, 0);
            if (currentIndex > 0)
            {
                currentIndex--;
                _carouselIndexes[carousel] = currentIndex;
                carousel.ScrollTo(currentIndex, position: ScrollToPosition.Start, animate: true);
            }
        }
    }

    private void ScrollRight_Clicked(object sender, EventArgs e)
    {
        if (sender is Button btn && btn.BindingContext is CollectionView carousel)
        {
            int currentIndex = _carouselIndexes.GetValueOrDefault(carousel, 0);
            int maxItems = carousel.ItemsSource is ICollection collection ? collection.Count : 0;
            int maxStartIndex = Math.Max(0, maxItems - MaxVisibleCards);

            if (currentIndex < maxStartIndex)
            {
                currentIndex++;
                _carouselIndexes[carousel] = currentIndex;
                carousel.ScrollTo(currentIndex, position: ScrollToPosition.Start, animate: true);
            }
        }
    }
}