@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mb-3">
            <div class="col-10">
                <div class="card rounded-5 bg-block-blue m-3">
                    <div class="text-center card-body border-0">

                        {{-- Header --}}
                        <livewire:user.profile-header />

                        <div class="row m-2">
                            <div class="col-12 col-lg-3 col-xl-5">
                                {{-- Profile editor --}}
                                <livewire:user.profile-editor />
                            </div>

                            <div class="col-12 col-lg-9 col-xl-7">
                                <div class="d-flex align-items-start">

                                    <!-- JARS -->
                                    <button 
                                            class="parallelogram rounded-5 m-2 w-75 h-75"
                                            onclick="document.getElementById('my-jars').scrollIntoView({behavior: 'smooth'})"
                                        >
                                        <div class="row">
                                            <div class="col-0 col-md-5">
                                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png"
                                                     class="img-fluid rounded-4 m-1 d-none d-md-block"
                                                     style="height: 14vh; width: 17vw;">
                                            </div>
                                            <div class="col-12 col-md-7">
                                                <p class="jar-name display-5 text-center m-2"
                                                   style="transform: skew(20deg); font-size: clamp(1.5rem, 5vw, 3rem);">
                                                    JARS
                                                </p>
                                            </div>
                                        </div>
                                    </button>

                                    <!-- LIBRARY -->
                                    <button 
                                            class="parallelogram rounded-5 m-2 w-75 h-75"
                                            onclick="document.getElementById('joined-jars').scrollIntoView({ behavior: 'smooth' })"
                                        >
                                        <div class="row">
                                            <div class="col-0 col-md-5">
                                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png"
                                                     class="img-fluid rounded-4 m-1 d-none d-md-block"
                                                     style="height: 14vh; width: 17vw;">
                                            </div>
                                            <div class="col-12 col-md-7">
                                                <p class="jar-name display-5 text-center m-2"
                                                   style="transform: skew(20deg); font-size: clamp(1.5rem, 5vw, 3rem);">
                                                    LIBRARY
                                                </p>
                                            </div>
                                        </div>
                                    </button>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

   
    <div id="my-jars" class="container-fluid mt-5">
        @if($myJars->isEmpty())
            <p class="text-center text-light">You don’t have any jars yet.</p>
        @else
            <livewire:jar-carousel 
                :jars="$myJars"
                title="My Jars"
                wire:key="profile-my-jars-carousel"
            />
        @endif
    </div>

    <div id="joined-jars" class="container-fluid mt-5">
        @if($joinedJars->isEmpty())
            <p class="text-center text-light">You haven’t joined any jars yet.</p>
        @else
            <livewire:jar-carousel 
                :jars="$joinedJars"
                title="Joined Jars"
                wire:key="profile-joined-jars-carousel"
            />
        @endif
    </div>

@endsection