<?php

use Livewire\Component;

new class extends Component
{
    public $jars = [];
    public string $title = '';
    public int $start = 0;

    /**
     * Safe default for first paint (mobile).
     * JS will update to 2/3/4 on larger screens.
     */
    public int $visibleCount = 1;

    protected $listeners = [
        'jar-visible-count-changed' => 'setVisibleCount',
    ];

    public function mount($jars, $title = '')
    {
        $this->jars = $jars;
        $this->title = $title;
    }

    public function setVisibleCount(int $count): void
    {
        $this->visibleCount = max(1, min(4, $count));
        $this->clampStart();
    }

    private function clampStart(): void
    {
        $count = count($this->jars);

        if ($count === 0) {
            $this->start = 0;
            return;
        }

        $maxStart = max(0, $count - $this->visibleCount);

        if ($this->start > $maxStart) {
            $this->start = $maxStart;
        }
    }

    public function getCanGoPreviousProperty(): bool
    {
        return $this->start > 0;
    }

    public function getCanGoNextProperty(): bool
    {
        $count = count($this->jars);
        $maxStart = max(0, $count - $this->visibleCount);

        return $this->start < $maxStart;
    }

    public function getCanSlideProperty(): bool
    {
        return count($this->jars) > $this->visibleCount;
    }

    public function next(): void
    {
        if (!$this->canSlide) return;

        $count = count($this->jars);
        if ($count === 0) return;

        $maxStart = max(0, $count - $this->visibleCount);

        if ($this->start < $maxStart) {
            $this->start++;
        }
    }

    public function previous(): void
    {
        if (!$this->canSlide) return;

        if ($this->start > 0) {
            $this->start--;
        }
    }

    public function getVisibleJarsProperty()
    {
        $count = count($this->jars);

        if ($count === 0) {
            return collect();
        }

        $visibleCount = min($this->visibleCount, $count);

        return collect($this->jars)
            ->slice($this->start, $visibleCount)
            ->values();
    }
}
?>

<div class="container-fluid jar-carousel-root"
     wire:init="$dispatch('jar-carousel:request-visible-count')">

    @if ($title)
        <div class="row mb-3">
            <div class="col-12 text-center">
                <h2 class="fw-bold welcome-text">{{ $title }}</h2>
            </div>
        </div>
    @endif

    <div class="row align-items-stretch">
        <!-- LEFT arrow (md+) -->
        <div class="col-1 d-none d-md-flex justify-content-center align-items-center">
            <button
                wire:click="previous"
                class="btn blue-button rounded-circle fw-bold"
                style="color:#EAB2A0; background-color:#2D4356;"
                @if(!$this->canGoPrevious) disabled style="opacity:.4; cursor:not-allowed;" @endif
                aria-label="Previous jars"
            >
                ❮
            </button>
        </div>

        <!-- CARDS -->
        <div class="col-12 col-md-10">
            <div class="row g-3 m-1 p-0 justify-content-center">
                @foreach ($this->visibleJars as $loopIndex => $jar)
                    <div class="col-12 col-md-6 col-lg-3 mb-5
                        @if($loopIndex >= 1) d-none d-md-block @endif
                        @if($loopIndex >= 2) d-none d-lg-block @endif
                        @if($loopIndex >= 3) d-none d-xl-block @endif
                    ">
                        <div class="card bg-block-blue rounded-5 h-100 d-flex flex-column jar-card-border">

                            <div class="card-header border-0 text-center">
                                <h5 class="fw-bold jar-name fs-3 text-truncate mt-2">{{ $jar->name }}</h5>

                                @if ($jar->user)
                                    <p class="mb-0 mt-1 small text-light opacity-75">by</p>
                                    <p class="mb-0 fw-bold" style="font-size: 1rem;">
                                        @if($jar->user?->username)
                                            <a
                                                href="{{ route('users.show', ['username' => $jar->user->username]) }}"
                                                class="text-decoration-none"
                                                style="color:#EAB2A0;"
                                            >
                                                &#64;{{ $jar->user->username }}
                                            </a>
                                        @else
                                            <span style="color:#EAB2A0;">&#64;unknown</span>
                                        @endif
                                    </p>
                                @endif
                            </div>

                            <div class="card-body text-center flex-grow-1">
                                @if ($jar->picture_url)
                                    <img src="{{ $jar->picture_url }}" class="img-fluid rounded-4" alt="Jar image">
                                @else
                                    <p class="text-light mb-0">No image found.</p>
                                @endif
                            </div>

                            <div class="px-1 pb-3 pt-2 mt-auto d-flex justify-content-center">
                                @auth
                                    <button
                                        class="btn home-claim-button m-2 rounded-pill blue-button"
                                        onclick="window.location='{{ route('jars.show', $jar) }}'">
                                        Show Jar
                                    </button>
                                @else
                                    <button
                                        wire:click="$dispatchTo('auth.login-modal', 'open-login')"
                                        class="btn home-claim-button m-2 rounded-pill blue-button">
                                        Show Jar
                                    </button>
                                @endauth
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            <!-- MOBILE arrows -->
            <div class="d-flex d-md-none justify-content-between px-4 pb-3">
                <button
                    wire:click="previous"
                    class="btn blue-button rounded-circle fw-bold"
                    style="color:#EAB2A0; background-color:#2D4356;"
                    @if(!$this->canGoPrevious) disabled style="opacity:.4; cursor:not-allowed;" @endif
                    aria-label="Previous jars"
                >
                    ❮
                </button>

                <button
                    wire:click="next"
                    class="btn blue-button rounded-circle fw-bold"
                    style="color:#EAB2A0; background-color:#2D4356;"
                    @if(!$this->canGoNext) disabled style="opacity:.4; cursor:not-allowed;" @endif
                    aria-label="Next jars"
                >
                    ❯
                </button>
            </div>
        </div>

        <!-- RIGHT arrow (md+) -->
        <div class="col-1 d-none d-md-flex justify-content-center align-items-center">
            <button
                wire:click="next"
                class="btn blue-button rounded-circle fw-bold"
                style="color:#EAB2A0; background-color:#2D4356;"
                @if(!$this->canGoNext) disabled style="opacity:.4; cursor:not-allowed;" @endif
                aria-label="Next jars"
            >
                ❯
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
(() => {
    const computeJarVisibleCount = () => {
        // Bootstrap breakpoints:
        // < md: 1
        // md: 2
        // lg: 3
        // xl+: 4
        if (window.matchMedia('(min-width: 1200px)').matches) return 4;
        if (window.matchMedia('(min-width: 992px)').matches) return 3;
        if (window.matchMedia('(min-width: 768px)').matches) return 2;
        return 1;
    };

    const dispatchCount = () => {
        if (!window.Livewire?.dispatch) return;
        Livewire.dispatch('jar-visible-count-changed', [computeJarVisibleCount()]);
    };

    window.addEventListener('jar-carousel:request-visible-count', dispatchCount);

    // Run once on load too (safe)
    dispatchCount();

    let t;
    window.addEventListener('resize', () => {
        clearTimeout(t);
        t = setTimeout(dispatchCount, 100);
    });
})();
</script>
@endpush