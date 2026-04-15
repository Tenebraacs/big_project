<?php

use Livewire\Component;

new class extends Component
{
    public $jars;
    public int $start = 0;
    public int $perPage = 4;

    public function next()
    {
        $count = count($this->jars);

        if($count === 0) return;

        $this->start = ($this->start + 1) % $count;
    }

    public function previous()
    {
        $count = count($this->jars);

        if($count === 0) return;

        $this->start = ($this->start - 1 + $count) % $count;
    }

    public function getVisibleJarsProperty()
    {
        $jars = collect($this->jars);
        $count = $jars->count();

        if($count === 0) return collect();

        return collect(range(0, $this->perPage -1))
            ->map(function ($i) use($jars, $count) {
                return $jars[($this->start + $i) % $count];
            });
    }

};
?>

<div class="container-fluid">
    <div class="row align-items-stretch">

        <div class="col-1 d-flex justify-content-center align-items-center">
            <button
                wire:click="previous"
                class="btn btn-secondary rounded-circle"      
            >
                ❮
            </button>
        </div>

        <div class="col-10">
            <div class="row">
                @foreach ($this->visibleJars as $jar)
                    <div class="col-md-3">
                        <div class="card h-100">

                            <div class="card-header text-center">
                                <h5 class="fw-bold">{{ $jar->name }}</h5>
                            </div>

                            <div class="card-body text-center">
                                @if ($jar->picture)
                                    <img src="{{ $jar->picture }}" class="img-fluid">
                                @else
                                    <p>No image found.</p>
                                @endif
                            </div>

                            <a href="{{ route('jars.show', $jar) }}" class="text-decoration-none text-dark">
                                <div class="card-footer d-flex justify-content-center align-items-center">
                                    <p class="fw-bold fs-5 mb-0">Show Jar</p>
                                </div>
                            </a>

                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-1 d-flex justify-content-center align-items-center">
            <button
                wire:click="next"
                class="btn btn-secondary rounded-circle"
            >
                ❯
            </button>
        </div>

    </div>
</div>