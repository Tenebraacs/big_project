@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 col-xl-4">
            <div class="card text-center h-100 d-flex flex-column" style="">
                <div class="card-header">
                    
                    @if ($jar)
                        <h1 class="fs-1 fw-bold">{{ $jar->name }}</h1>
                        @if($jar->description)
                            <h2>{{ $jar->description }}</h2>
                        @else
                            <h2>No current description found</h2>
                        @endif
                    @else
                        <h1 class="fs-1 fw-bold">No Usable Adventure Found</h1>  
                    @endif
                         
                    
                </div>

                <div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-2">
                    
                    
                    <div class=" rounded-3 p-2 mb-3 ">
                        <div class="card-header rounded-3">
                            @if($currentQuest)
                            <h3>{{ $currentQuest->title }}</h3>
                                @if ($currentQuest->description)
                                    <p class="fs-5">{{ $currentQuest->description }}</p>
                                @else
                                    <p class="fs-5">No description yet so just figure it out</p>
                                @endif
                            @else
                            <h3>No Quests inside the jar</h3>
                            @endif
                        </div>
                    </div>
                    @if ($currentQuest)
                        
                    
                        <div class="container-fluid">
                            <div class="row justify-content-center align-items-stretch">
                                <a class="col-1 d-flex align-items-center justify-content-center text-decoration-none text-dark fs-1" href="{{ route('jars.show', ['jar' => $jar->id, 'quest' => $previousQuest->id]) }}" >

                                        ❮
                                            
                                </a>
                                
                                <div class="col-10 p-0" style="aspect-ratio: 1/1;">
                                    <img src="{{ $currentQuest->picture }}" alt="Jar picture" class="w-100 h-100" style="object-fit: cover;">
                                </div>

                                <a class="col-1 d-flex align-items-center justify-content-center text-decoration-none text-dark fs-1" href="{{ route('jars.show', ['jar' => $jar->id, 'quest' => $nextQuest->id]) }}" >

                                        ❯
 
                                </a>

                                
                            </div>
                            
                        </div>
                    @endif
                    
                </div>
                @auth
                <div class="card-footer">
             
                        <p class="fs-3 mb-0">Join Jar</p>
                                       
                </div>
                @endauth
            </div>
        </div>
    </div>
</div>
    


@endsection