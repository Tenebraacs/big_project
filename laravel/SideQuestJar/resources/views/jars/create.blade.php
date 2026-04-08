@extends('layouts.app')

@section('content')

    <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-4">
            <form action="{{ route( "jars.store") }}" method="POST">
            @csrf
                <div class="card text-center h-100 d-flex flex-column" style="">
                    <div class="card-header">
                        
                        <div class="form-group">
                            <label for="name">JarName</label>
                            <input type="text" name="name" id="name" placeholder="JarName" required value="{{ old('name') }}">
                        </div>
                                    
                    </div>

                    <div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-2">
                        
                        
                        <div class="form-group">
                            <label for="description">JarDescription</label>
                            <input type="text" name="description" id="description" placeholder="JarDescription" value="{{ old('description') }}">
                        </div>

                        <div class="col-10 p-0" style="aspect-ratio: 1/1;">
                            <img src="{{ $currentQuest->picture }}" alt="Jar picture" class="w-100 h-100" style="object-fit: cover;">
                        </div>
                        
                    </div>
                    
                    <div class="card-footer p-0">
                        
                        <div>
                            <button type="submit">CreateJar</button>
                        </div>    
                                        
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
</div>


    <div class="form-container">
        
            
            
            
        
        
    </div>


@endsection