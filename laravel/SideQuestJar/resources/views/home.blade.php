@extends('layouts.app')

@section('content')
<div class="container-fluid">

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
                    
                    
                    
                    @if ($mainQuest)
                        <p>{{ $mainQuest->description }}</p>
                    
                        @if ($mainQuest->picture)
                        <img src="{{ $mainQuest->picture }}" alt="Jar picture" class="img-fluid d-block mx-auto">
                        @else
                            <h2>No image found.</h2>
                        @endif
                    @endif
                        
                  
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
                        

                        @if ($jar->quests)
                            <p>{{ $jar->quests->first()->description}}</p>

                            @if ($jar->quests->first()->picture)
                                <img src="{{ $jar->quests->first()->picture }}" class="img-fluid">
                            @else
                                <p>No image found.</p>
                            @endif
                        @else
                            <p>No content available.</p>
                        @endif
                    </div>

                </div>
            </div>             
        @endforeach
    </div>
    

</div>


@endsection
