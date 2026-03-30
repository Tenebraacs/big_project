@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-10">
            <div class="card">
                <div class="card-header text-center ">
                    
                    @if ($mainJar)
                        <h1 class="fs-1 fw-bold">{{ $mainJar->name }}</h1>
                    @else
                        <h1 class="fs-1 fw-bold">No Usable Adventure Found</h1>                      
                    @endif
                    
                </div>

                <div class="card-body text-center">
                    
                    
                    
                    @if ($mainContent)
                        <p>{{ $mainContent->description }}</p>
                    
                        @if ($mainContent->picture)
                        <img src="{{ $mainContent->picture }}" alt="Jar picture" class="img-fluid d-block mx-auto">
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
