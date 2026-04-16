@extends('layouts.app')

@section('content')


    <!-- Meg basic kinezettel szorakozok --> 

    <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-4">
            <form action="{{ route( "jars.update", $jar) }}" method="POST">
            @csrf
            @method('PUT')
                <div class="card text-center h-100 d-flex flex-column" style="">
                    <div class="card-header">
                        
                        <div class="row">
                            <label for="name">JarName</label>
                        </div>
                        <div class="row justify-content-center">
                            <input class="text-center col-6" required type="text" name="name" id="name" placeholder="JarName" value="{{ $jar->name }}" required value="{{ old('name') }}">
                        </div>
                            
                        
                                    
                    </div>

                    <div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-2">
                        
                        
                        <div class="row">
                            <label for="description">JarDescription</label>
                        </div>
                        <div class="row justify-content-center p-2">
                            <textarea class=" text-center col-8 p-2" name="description" id="description" placeholder="JarDescription">{{ $jar->description }}</textarea>
                        </div>

                        <div class="row justify-content-center align-items-stretch">
                            <div class="col-10 p-0 " style="aspect-ratio: 1/1;">
                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png" alt="Jar picture" class="w-100 h-100" style="object-fit: cover;">
                            </div>
                        </div>

                        
                        
                    </div>
                    
                    <div class="card-footer p-0">
                        
                        <div>
                            <button type="submit" class="w-100 h-100 border-0 bg-transparent fs-3 p-0 m-1">
                                UpdateJar
                            </button>
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