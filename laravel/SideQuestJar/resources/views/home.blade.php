@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="card">
                <div class="card-header text-center fw-bold">{{ __("Today's adventure") }}</div>

                <div class="card-body text-center">
                    
                    
                        @if ($jar && $jar->picture)
                        <img src="{{ $jar->picture }}" alt="Jar picture" class="img-fluid d-block mx-auto">
                        @else
                            <h2>No image found.</h2>
                        @endif
                    

                    
                    @auth
                        {{ __('You are logged in!') }}
                    @else
                        {{ __('You are not logged in!') }}
                    @endauth
                    
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
