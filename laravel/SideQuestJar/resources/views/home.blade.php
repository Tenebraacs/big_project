@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row justify-content-center mb-3">
        <div class="col-10">
            <div class="card rounded-5 bg-block-blue m-3">
                <div class="text-center card-header border-0">
                    @if ($mainJar)
                        <h1 class="fw-bold display-3 welcome-text m-3">{{ $mainJar->name }}</h1>
                    @else
                        <h1 class="fs-1 fw-bold welcome-text">No Usable Adventure Found</h1>
                    @endif
                </div>

                <div class="card-body text-center">
                    <div class="row">
                        @if ($mainQuest)
                            <!-- Left side -->
                            <div class="col-sm-8 text-center align-self-start d-flex justify-content-center">
                                <p class="main-jar-desc fs-1 text-center">{{ $mainQuest->description }}</p>
                            </div>

                            @if ($mainQuest->picture)
                                <!-- Right side -->
                                <div class="col-sm-4">
                                <!--Image-->
                                    <div style="aspect-ratio: 1/1;">
                                        <img src="{{ $mainQuest->picture }}" alt="Jar picture" class="img-fluid w-100 h-100 rounded-5" style="object-fit: cover;">
                                    </div>

                                    <!-- TODO: Itt kap a felhasználó XP-t a megcsinált quest-ért -->
                                    <button class="btn blue-button w-75 justify-center m-2 rounded-pill">
                                        {{ __('Claim') }}
                                    </button>
                                </div>
                            @else
                                <h2>No image found.</h2>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <livewire:jar-carousel :jars="$jars" />

</div>
@endsection