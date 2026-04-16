<?php

use Livewire\Component;

new class extends Component
{
    public $jars = [];
    public int $start = 0;

    public function mount($jars)
    {
        $this->jars = $jars;
    }

    public function next()
    {
        $count = count($this->jars);
        if ($count === 0) return;

        $this->start = ($this->start + 1) % $count;
    }

    public function previous()
    {
        $count = count($this->jars);
        if ($count === 0) return;

        $this->start = ($this->start - 1 + $count) % $count;
    }

    public function getVisibleJarsProperty()
    {
        $count = count($this->jars);
        if ($count === 0) return collect();

        return collect(range(0, 3))
            ->map(fn($i) => $this->jars[($this->start + $i) % $count]);
    }
}
?>

<div class="container-fluid">
    <div class="row align-items-stretch">

        <!-- Previous button -->
        <div class="col-1 d-flex justify-content-center align-items-center">
            <button wire:click="previous"
                class="btn blue-button rounded-circle fw-bold"
                style="color: #EAB2A0; background-color: #2D4356;">
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
                        <div class="card bg-block-blue rounded-5 h-100">

                            <div class="card-header border-0 text-center">
                                <h5 class="fw-bold jar-name fs-3 text-truncate m-2">{{ $jar->name }}</h5>
                            </div>

                            <div class="card-body text-center">
                                @if ($jar->picture)
                                    <img src="{{ $jar->picture }}" class="img-fluid rounded-4">
                                @else
                                    <p>No image found.</p>
                                @endif
                            </div>

                            <a href="{{ route('jars.show', $jar) }}" class="text-decoration-none text-dark">
                                <div class="card-footer border-0 d-flex justify-content-center align-items-center">
                                    <p class="btn fw-bold fs-5 mb-0 w-100 rounded-pill blue-button">Show Jar</p>
                                </div>
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Next button -->
        <div class="col-1 d-flex justify-content-center align-items-center">
            <button wire:click="next"
                class="btn blue-button rounded-circle fw-bold"
                style="color: #EAB2A0; background-color: #2D4356;">
                ❯
            </button>
        </div>

    </div>
</div>