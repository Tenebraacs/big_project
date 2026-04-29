@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mb-3">
            <div class="col-10">
                <div class="card rounded-5 bg-block-blue m-3">
                    <div class="text-center card-body border-0">

                        {{-- ✅ Livewire header: big avatar + big username + xp/level --}}
                        <livewire:user.profile-header />

                        <div class="row m-2">
                            <div class="col-12 col-lg-3 col-xl-5">
                                {{-- ✅ Livewire editor: edit username/email/password/picture --}}
                                <livewire:user.profile-editor />
                            </div>

                            <div class="col-12 col-lg-9 col-xl-7">
                                <div class="d-felx align-items-start">
                                    <!--Jars-->
                                    <button class="parallelogram rounded-5 m-2 w-75 h-75"
                                            style="max-width: 100vw; max-height: 75vh;">
                                        <div class="row">
                                            <div class="col-0 col-md-5">
                                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png" alt="Jars"
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

                                    <!--Library-->
                                    <button class="parallelogram rounded-5 m-2 w-75 h-75"
                                            style="max-width: 100vw; max-height: 75vh;">
                                        <div class="row">
                                            <div class="col-0 col-md-5">
                                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png" alt="Library"
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
@endsection