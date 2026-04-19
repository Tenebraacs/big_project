@extends('layouts.app')

@section('content')


    <!-- Meg basic kinezettel szorakozok --> 

    <div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 col-xl-4">
            <form action="{{ route( "jars.store") }}" method="POST">
            @csrf
                <div class="card bg-block-blue rounded-5 text-center h-100 d-flex flex-column" style="">
                    <div class="card-header border-0">
                        
                        <div class="row">
                            <label for="name" class="jar-name fs-4">Jar Name</label>
                        </div>
                        <div class="row justify-content-center">
                            <input class="text-center col-6 rounded-2" style="background-color: #eab3a0d5;" required type="text" name="name" id="name" placeholder="JarName" required value="{{ old('name') }}">
                        </div>
                            
                        
                                    
                    </div>

                    <div class="card-body text-center flex-grow-1 d-flex flex-column p-0 mb-2">
                        
                        
                        <div class="row">
                            <label for="description" class="jar-name fs-4">Jar Description</label>
                        </div>
                        <div class="row justify-content-center p-2">
                            <textarea class=" text-center col-8 p-2 rounded-3" style="background-color: #eab3a0d5;"  name="description" id="description" placeholder="JarDescription"></textarea>
                        </div>

                        <div class="row justify-content-center align-items-stretch">
                            <div class="col-10 p-0 m-1" style="aspect-ratio: 1/1;">
                                <img src="https://i.ibb.co/2YvZypwJ/Jar-Quest-Placeholder.png" alt="Jar picture" class="w-100 h-100 rounded-5" style="object-fit: cover;">
                            </div>
                        </div>

                        
                        
                    </div>
                    
                    <div class="card-footer p-0 border-0">
                        
                        <div>
                            <button type="submit" class="w-50 pink-button rounded-pill fs-3 p-1 mb-3">
                                Create Jar
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