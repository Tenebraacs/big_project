@extends('layouts.app')

@section('content')

    <div class="form-container">
        <form action="{{ route( "jars.store") }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">JarName</label>
                <input type="text" name="name" id="name" placeholder="JarName" required value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label for="description">JarDescription</label>
                <input type="text" name="description" id="description" placeholder="JarDescription" value="{{ old('description') }}">
            </div>
            <div>
                <button type="submit">CreateJar</button>
            </div>
        </form>
        
    </div>


@endsection