<?php

use Livewire\Component;

new class extends Component
{
    public $jars = [];
    public string $title = '';
    public int $start = 0;

    public function mount($jars, $title = '')
    {
        
        $this->jars = $jars;
        $this->title = $title;
    }

    public function getCanSlideProperty()
    {
        return count($this->jars) > 4;
    }

    public function next()
    {
        if(!$this->canSlide) return;


        $count = count($this->jars);
        $this->start = ($this->start + 1) % $count;
    }

    public function previous()
    {
        if(!$this->canSlide) return;

        $count = count($this->jars);
        $this->start = ($this->start - 1 + $count) % $count;
    }

    public function getVisibleJarsProperty()
    {
        $count = count($this->jars);

        if ($count === 0) {
            return collect();
        }

        $visibleCount = min(4, $count);

        return collect(range(0, $visibleCount - 1))
            ->map(fn($i) => $this->jars[($this->start + $i) % $count]);
    }
}
?>

<div class="container-fluid">

    @if ($title)
        <div class="row mb-3">
            <div class="col-12 text-center">
                <h2 class="fw-bold welcome-text">{{ $title }}</h2>
            </div>
        </div>
    @endif

    <div class="row align-items-stretch">

        <!-- Previous button -->
        <div class="col-1 d-flex justify-content-center align-items-center">
            <button wire:click="previous"
                class="btn blue-button rounded-circle fw-bold"
                style="color: #EAB2A0; background-color: #2D4356;"
                @if(!$this->canSlide) disabled style="opacity: 0.4; cursor: not-allowed;" @endif>
                ❮
            </button>
        </div>

        <!-- Cards -->
        <div class="col-10">
            <div class="row g-3 m-1 p-0">
                @foreach ($this->visibleJars as $loopIndex => $jar)
                    <div class="col-12 col-md-6 col-lg-3 mb-5
                        @if($loopIndex >= 1) d-none d-md-block @endif
                        @if($loopIndex >= 2) d-none d-lg-block @endif
                    ">
                        <div class="card bg-block-blue rounded-5 h-100 d-flex flex-column jar-card-border">

                            <div class="card-header border-0 text-center">
                                <h5 class="fw-bold jar-name fs-3 text-truncate mt-2">{{ $jar->name }}</h5>

                                @if ($jar->user)
                                    <p class="mb-0 mt-1 small text-light opacity-75">by</p>
                                    <p class="mb-0 fw-bold" style="color: #EAB2A0; font-size: 1rem;">
                                        &#64;{{ $jar->user->username ?? $jar->user->name ?? 'unknown' }}
                                    </p>
                                @endif
                            </div>

                            <div class="card-body text-center flex-grow-1">
                                @if ($jar->picture_url)
                                    <img src="{{ $jar->picture_url }}" class="img-fluid rounded-4">
                                @else
                                    <p>No image found.</p>
                                @endif
                            </div>

                            <div class="p-2 mt-auto">
                                @auth
                                    <button
                                        class="btn fw-bold fs-5 w-100 rounded-pill blue-button"
                                        onclick="window.location='{{ route('jars.show', $jar) }}'">
                                        Show Jar
                                    </button>
                                @else
                                    <button
                                        wire:click="$dispatchTo('auth.login-modal', 'open-login')"
                                        class="btn fw-bold fs-5 w-100 rounded-pill blue-button">
                                        Show Jar
                                    </button>
                                @endauth
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Next button -->
        <div class="col-1 d-flex justify-content-center align-items-center">
            <button wire:click="next"
                class="btn blue-button rounded-circle fw-bold"
                style="color: #EAB2A0; background-color: #2D4356;"
                @if(!$this->canSlide) disabled style="opacity: 0.4; cursor: not-allowed;" @endif>
                ❯
            </button>
        </div>

    </div>
</div>