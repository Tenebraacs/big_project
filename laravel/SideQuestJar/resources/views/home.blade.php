@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="card">
                <div class="card-header text-center ">
                    
                    @if ($jar)
                        <h1 class="fs-1 fw-bold">{{ $jar->name }}</h1>
                    @else
                        <h1 class="fs-1 fw-bold">No Usable Adventure Found</h1>                      
                    @endif
                    
                </div>

                <div class="card-body text-center">
                    
                    <!--
                        @if ($jar && $jar->picture)
                        <img src="{{ $jar->piccontentture }}" alt="Jar picture" class="img-fluid d-block mx-auto">
                        @else
                            <h2>No image found.</h2>
                        @endif


                        @auth
                        <p>{{ __('You are logged in!') }}</p>
                        @else
                        {{ __('You are not logged in!') }}
                        @endauth

                    -->
                    
                    @if ($content)
                        <p>{{ $content->description }}</p>
                    
                        @if ($content->picture)
                        <img src="{{ $content->picture }}" alt="Jar picture" class="img-fluid d-block mx-auto mjar-img-size">
                        @else
                            <h2>No image found.</h2>
                        @endif
                    @endif
                        
                    
                    
                    
                    
                    
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
