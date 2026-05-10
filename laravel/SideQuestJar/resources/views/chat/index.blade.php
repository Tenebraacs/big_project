@extends('layouts.app')

@section('content')
<div class="container text-light mt-2" style="max-width: 1200px;">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8">
            <div class="card bg-block-blue rounded-4 p-4 border-0 mb-4">
                <h2 class="jar-name text-center mb-4">My Chats</h2>
                @if($users->isEmpty())
                    <p class="text-center">You have no friends to chat with.</p>
                @else
                    <div class="list-group">
                        @foreach($users as $friend)
                            <a href="{{ route('chat.show', $friend) }}" class="list-group-item list-group-item-action bg-transparent text-light border-0 d-flex align-items-center mb-2" style="background-color: #eab3a0d5 !important; border-radius: 15px;">
                                <img src="{{ $friend->profile_picture_url }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                <h5 class="mb-0 jar-name fs-4">{{ $friend->username }}</h5>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
