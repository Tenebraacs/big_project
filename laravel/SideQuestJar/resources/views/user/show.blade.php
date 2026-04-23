@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mb-3">
            <div class="col-10">
                <div class="card rounded-5 bg-block-blue m-3">
                    <div class="text-center card-body border-0">

                        <div class="row">
                            <div class="col-5 col-md-3 ">
                                <div class="ratio ratio-1x1 m-2">
                                @if ($user->profile_picture)
                                    <img src="{{ $user->profile_picture }}" class="img-fluid rounded-circle">
                                @else
                                    <p>No image found.</p>
                                @endif
                                </div>
                            </div>
                            <div class="col-7 col-md-9">
                                <h1 class="fw-bold jar-name display-5 m-2" style="font-size: clamp(1.5rem, 5vw, 3rem);">{{ $user->username }}</h1>

                                <!--Levels-->
                                <div class="rounded-4 m-2 d-flex justify-content-center align-items-center mx-auto" style="background-color: #A76F6F;  max-height: 10vh; max-width: 5vw;">
                                    <p class="jar-name fs-2 m-0">1</p>
                                </div>
                                <!--XP-->
                                <div class="progress m-1" role="progressbar" aria-label="XP Bar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" style="width: 25%"></div>
                                </div>
                                <p class="jar-name text-start m-1">100/500</p>

                            </div>
                        </div>
                        <div class="row m-2">
                            <div class="col-12 col-lg-3 col-xl-5">
                                <!--Username-->
                                <div>
                                    <label for="username" class="profile-label fs-4 d-flex align-self-start m-1 mb-0">Username</label>
                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <p class="profile-label fs-5 fw-light text-white d-flex align-self-start m-2 mt-0">{{ $user->username }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn edit-button fw-medium rounded-pill text-end d-flex align-self-start">Edit</button>
                                            </div>
                                </div>
                                 <!--E-mail-->
                                <div>
                                    <label for="email" class="profile-label fs-4 d-flex align-self-start m-1 mb-0">E-mail</label>
                                        <div class="row">
                                            <div class="col-12 col-md-8">
                                                <p class="profile-label fs-5 fw-light text-truncate text-white d-flex align-self-center m-2 mt-0 ">{{ $user->email }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <button class="btn edit-button fw-medium rounded-pill text-end d-flex align-self-start">Edit</button>
                                            </div>
                                </div>
                            </div>
                                <!--Password-->
                                    <div>
                                        <label for="password" class="profile-label fs-4 d-flex align-self-start m-1 mb-0">Password</label>
                                            <div class="row">
                                                <div class="col-12 col-md-8">
                                                    <p class="profile-label fs-5 fw-light text-white text-truncate d-flex align-self-start m-2 mt-0">***********</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <button class="btn edit-button fw-medium rounded-pill text-end d-flex align-self-start">Edit</button>
                                                </div>
                                    </div>
                        </div>

                    </div>
                </div>
                <div class="col-12 col-lg-9 col-xl-7">
                    <div class="d-felx align-items-start">
                <!--Jars-->
                    <button class="parallelogram rounded-5 m-2 w-75 h-75" style="max-width: 100vw; max-height: 75vh;">
                        <div class="row">
                            <div class="col-0 col-md-5">
                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png" alt="Jars" class="img-fluid rounded-4 m-1 d-none d-md-block" style="height: 14vh; width: 17vw;">
                            </div>
                            <div class="col-12 col-md-7">
                                <p class="jar-name display-5 text-center m-2" style="transform: skew(20deg); font-size: clamp(1.5rem, 5vw, 3rem);">JARS</p>
                            </div>
                        </div>
                    </button>
                <!--Lirbary-->
                     <button class="parallelogram rounded-5 m-2 w-75 h-75" style="max-width: 100vw; max-height: 75vh;">
                        <div class="row">
                            <div class="col-0 col-md-5">
                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png" alt="Library" class="img-fluid rounded-4 m-1 d-none d-md-block" style="height: 14vh; width: 17vw;">
                            </div>
                            <div class="col-12 col-md-7">
                                <p class="jar-name display-5 text-center m-2" style="transform: skew(20deg); font-size: clamp(1.5rem, 5vw, 3rem);">LIBRARY</p>
                            </div>
                        </div>
                    </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection