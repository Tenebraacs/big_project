@extends('layouts.app')

@section('content')


    <!-- Meg basic kinezettel szorakozok --> 

    <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-4">
            <form action="{{ route( "jars.quests.update", ['jar' => $jar->id, 'jarQuest' => $jarQuest->id]) }}" method="POST">
            @csrf
            @method('PUT')
                <div class="card text-center h-100 d-flex flex-column bg-block-blue rounded-5" style="">
                    

                    <div class="card-header border-0">
                        
                        <h1 class="fs-1 jar-name">{{ $jar->name }}</h1>

                        @if($jar->description)
                            <h2 class="jar-name fs-3">{{ $jar->description }}</h2>
                        @else
                            <h2 class="jar-name fs-3">No current description found</h2>
                        @endif
                            
                        
                                    
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger m-2">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-2">
                        
                        
                        <div class="row p-1">
                            <label for="title" class="jar-name fs-4">Quest Title</label>
                        </div>
                        <div class="row justify-content-center">
                            <input class="text-center col-8 p-1 rounded-3" style="background-color: #eab3a0d5;" required type="text" name="title" id="title" value="{{ old('title', $jarQuest->title) }}" placeholder="QuestTitle" value="{{ old('title') }}">
                        </div>

                        <div class="row p-1">
                            <label for="description" class="jar-name fs-4">Quest Description</label>
                        </div>
                        <div class="row justify-content-center pb-2">
                            <textarea class=" text-center col-8 p-2 rounded-3" style="background-color: #eab3a0d5;" name="description" id="description" placeholder="QuestDescription">{{ old('description', $jarQuest->description) }}</textarea>
                        </div>

                        <div class="row justify-content-center align-items-stretch">
                            <div class="col-10 p-0 " style="aspect-ratio: 1/1;">
                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png" alt="Jar picture" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                        </div>

                        
                        
                    </div>
                    
                    <div class="card-footer p-0 border-0">
                        
                        <div>
                            <button type="submit" class="btn w-50 h-100 pink-button rounded-pill fs-3 p-0 m-2">
                                Update Quest
                            </button>
                        </div>    
                                        
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>





@endsection