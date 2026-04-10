@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-10">
            <div class="card rounded-5 bg-block-blue">
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
                        <!--Left side-->
                        <div class="col-sm-8 text-center">
                            <p class="main-jar-desc fs-2">{{ $mainQuest->description }}</p>
                        </div>

                        @if ($mainQuest->picture)
                        <!--Right side-->
                        <div class="col-sm-4">
                            <img src="{{ $mainQuest->picture }}" alt="Jar picture" class="img-fluid cropped-img w-60 rounded-5">
                            
                            <!--TODO: Itt kap a felhasználó XP-t a megcsinált quest-ért-->
                            <button class="btn blue-button w-75 justify-center m-2 rounded-pill">
                                {{ __('Claim') }}
                            </button>
                        </div>

                        @else
                            <h2>No image found.</h2>
                        @endif
                    @endif
                        </div>

                <!--TODO: Itt kap a felhasználó XP-t a megcsinált quest-ért-->
                  
                </div>
               
            </div>

            
        </div>
    </div>
    <div class="row">
        @foreach ($jars as $jar)
            <div class="col-md-3 md-4">
                <div class="card h-100">

                    <div class="card-header text-center">
                        <h5 class="fw-bold">{{ $jar->name }}</h5>

                    </div>

                    <div class="card-body text-center">
                        
                        @if ($jar)
                            

                            @if ($jar->picture)
                                <img src="{{ $jar->picture }}" class="img-fluid">
                            @else
                                <p>No image found.</p>
                            @endif
                        @else
                            <p>No content available.</p>
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


@endsection
