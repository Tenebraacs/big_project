@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center ">
        <div class="col-12 col-md-10 col-lg-8 col-xl-4">
            
            <div class="card text-center bg-block-blue rounded-5 h-100 d-flex flex-column">
                
                
                <div class="card-header border-0">
                    
                    @if ($jar)
                        <h1 class="jar-name fs-1 fw-bold">{{ $jar->name }}</h1>
                        @if($jar->description)
                            <h2 class="jar-name fs-2">{{ $jar->description }}</h2>
                        @else
                            <h2 class="jar-name fs-2">No current description found</h2>
                        @endif
                    @else
                        <h1 class="jar-name fs-1 fw-bold">No Usable Adventure Found</h1>  
                    @endif
                                
                </div>

                <livewire:show-jar :jar="$jar" :quest="$quest ?? null" />

                
                @auth
                <div class="card-footer p-0 border-0">
                    
                    
                    @if($jar->created_by === auth()->id() || auth()->user()->is_admin) <!--Lehet policy alapon-->
                        <a href="{{ route('jars.quests.create', ['jar' => $jar->id]) }}" class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1 ">
                            Add New Quest
                        </a>
                        <form action="{{ route('jars.destroy', $jar) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-50 border-0 btn btn-outline-danger rounded-pill fs-3  p-0 m-1">
                                Delete Jar
                            </button>
                        </form>
                    @else
                        @if ($jar->users()->where('user_id', auth()->id())->exists())
                            <form action="{{ route('jars.leave', $jar) }}" method="POST" class="w-100">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1 ">
                                    Leave Jar
                                </button>
                            </form>
                        @else
                            <form action="{{ route('jars.join', $jar) }}" method="POST" class="w-100">
                                @csrf
                                <button type="submit" class="btn fw-medium pink-button rounded-pill w-50 fs-3 p-0 m-1 ">
                                    Join Jar
                                </button>
                            </form>
                        @endif
                    @endif
                    
                        
                                       
                </div>
                @endauth
            </div>
        </div>
    </div>
</div>
    


@endsection