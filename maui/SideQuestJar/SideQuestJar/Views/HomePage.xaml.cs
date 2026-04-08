using SideQuestJar.ViewModels;
using System.Collections;

namespace SideQuestJar.Views;

public partial class HomePage : ContentPage
{
    private readonly HomeViewModel _viewModel;

    // A single dictionary tracks the scroll index for ANY carousel we add to the page
    private readonly Dictionary<CollectionView, int> _carouselIndexes = new();

    private CancellationTokenSource? _resizeCts;

    public static readonly BindableProperty CalculatedCardWidthProperty =
        BindableProperty.Create(nameof(CalculatedCardWidth), typeof(double), typeof(HomePage), 160.0);

    public double CalculatedCardWidth
    {
        get => (double)GetValue(CalculatedCardWidthProperty);
        set => SetValue(CalculatedCardWidthProperty, value);
    }

    public HomePage(HomeViewModel viewModel)
    {
        InitializeComponent();
        _viewModel = viewModel;
        BindingContext = _viewModel;

        // Hook into the native handlers to disable the scroll wheel
        PopularCarousel.HandlerChanged += OnCarouselHandlerChanged;
        RecommendedCarousel.HandlerChanged += OnCarouselHandlerChanged;
    }

    private void OnCarouselHandlerChanged(object? sender, EventArgs e)
    {
#if WINDOWS
        // Reach into the native Windows App SDK (WinUI 3) underlying the MAUI CollectionView
        if (sender is CollectionView cv && cv.Handler?.PlatformView is Microsoft.UI.Xaml.UIElement platformView)
        {
            // Intercept the physical mouse wheel event
            platformView.PointerWheelChanged += (s, args) =>
            {
                // Swallowing the event completely stops the CollectionView from scrolling
                args.Handled = true; 
            };
        }
#endif
    }

    protected override void OnAppearing()
    {
        base.OnAppearing();
        if (!_viewModel.LoadDiscoverDataCommand.IsRunning)
        {
            _viewModel.LoadDiscoverDataCommand.Execute(null);
        }
    }

    // --- Window Resizing & Layout Math ---
    protected override void OnSizeAllocated(double width, double height)
    {
        base.OnSizeAllocated(width, height);

        if (width <= 0) return;

        _resizeCts?.Cancel();
        _resizeCts = new CancellationTokenSource();
        var token = _resizeCts.Token;

        Task.Delay(100, token).ContinueWith(t =>
        {
            if (token.IsCancellationRequested) return;

            MainThread.BeginInvokeOnMainThread(() =>
            {
                // Exact math to prevent right-side clipping for exactly 5 cards:
                // 100px (Page Padding: 50 + 50)
                // 140px (Grid Columns: 50 Arrow + 20 Gap + 20 Gap + 50 Arrow)
                // 80px  (Carousel ItemSpacing: 4 gaps * 20px)
                // 10px  (Safety Buffer for Windows rendering borders)
                // Total static space = 330px
                double availableWidth = width - 330;

                // Math.Floor ensures we round DOWN, preventing fractional pixel overflow
                CalculatedCardWidth = Math.Max(120, Math.Floor(availableWidth / 5));
            });
        }, TaskScheduler.Default);
    }

    // --- Unified Scroll Logic ---
    private void ScrollLeft_Clicked(object sender, EventArgs e)
    {
        // We injected the CollectionView into the Button's BindingContext in XAML
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

            // Extract the total item count safely from the CollectionView's ItemsSource
            int maxItems = 0;
            if (carousel.ItemsSource is ICollection collection) maxItems = collection.Count;

            // Stop scrolling if the last 5 items are currently visible on the screen
            if (currentIndex < maxItems - 5)
            {
                currentIndex++;
                _carouselIndexes[carousel] = currentIndex;
                carousel.ScrollTo(currentIndex, position: ScrollToPosition.Start, animate: true);
            }
        }
    }
}