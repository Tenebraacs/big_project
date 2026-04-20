@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center mb-3">
            <div class="col-10">
                <div class="card rounded-5 bg-block-blue m-3">
                    <div class="text-center card-header border-0">

                        <div class="row">
                            <div class="col-4">
                                @if ($user->profile_picture)
                                    <img src="{{ $user->profile_picture }}" class="img-fluid rounded-circle m-1">
                                @else
                                    <p>No image found.</p>
                                @endif
                            </div>
                            <div class="col-8">
                                <h1 class="fw-bold jar-name display-5 m-2">{{ $user->username }}</h1>

                                <div class="progress" role="progressbar" aria-label="XP Bar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" style="width: 25%"></div>
                                </div>
                                <p class="jar-name text-start m-1">100/500</p>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection