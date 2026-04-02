@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5 col-xl-4">
            <div class="card text-center h-100 d-flex flex-column" style="min-height: 600px;">
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

                <div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-0">
                    
                    
                    <div class=" rounded-3 p-2 mb-3 ">
                        <div class="card-header rounded-3">
                            @if($jar->quests->first()->title)
                            <h3>{{ $jar->quests->first()->title }}</h3>
                                @if ($jar->quests->first()->description)
                                    <p class="fs-5">{{ $jar->quests->first()->description }}</p>
                                @else
                                    
                                @endif
                            @else
                            <h3>No Quests inside the jar</h3>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        <div style="width: 400px; max-width: 400px; aspect-ratio: 1/1;">
                            <img src="{{ $jar->quests->first()->picture }}" alt="Jar picture" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                        
                    </div>
                    
                </div>
                
                    <div class="card-footer">
                        <p class="fs-3 mb-0">Join Jar</p>
                    </div>
                
                
               
            </div>

            
        </div>
    </div>
</div>
    


@endsection