@extends('layouts.app')

@section('content')
<div class="container-fluid text-light mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card bg-block-blue rounded-4 p-4 border-0 mb-4">
                <h2 class="jar-name text-center mb-4">Add Friend</h2>
                <form action="{{ route('friends.add') }}" method="POST" class="position-relative" id="addFriendForm">
                    @csrf
                    <div class="d-flex gap-2">
                        <input type="text" name="username" id="usernameInput" autocomplete="off" class="form-control bg-dark text-light border-0 rounded-pill px-3" placeholder="Enter username..." required>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">Add</button>
                    </div>
                    <div id="suggestions" class="list-group position-absolute w-100 mt-1 shadow-lg d-none" style="z-index: 1000; max-height: 200px; overflow-y: auto;"></div>
                </form>
                @if(session('error'))
                    <div class="text-danger mt-2 px-3 small">{{ session('error') }}</div>
                @endif
                @if(session('success'))
                    <div class="text-success mt-2 px-3 small">{{ session('success') }}</div>
                @endif
            </div>

            <style>
                #suggestions .list-group-item {
                    background-color: #1a1a2e;
                    border: 1px solid #2d2d44;
                    color: white;
                    cursor: pointer;
                    transition: background-color 0.2s;
                }
                #suggestions .list-group-item:hover {
                    background-color: #2d2d44;
                }
                #suggestions img {
                    width: 30px;
                    height: 30px;
                    object-fit: cover;
                }
            </style>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const input = document.getElementById('usernameInput');
                    const suggestions = document.getElementById('suggestions');
                    let debounceTimer;

                    input.addEventListener('input', function() {
                        clearTimeout(debounceTimer);
                        const query = this.value;

                        if (query.length < 2) {
                            suggestions.classList.add('d-none');
                            return;
                        }

                        debounceTimer = setTimeout(() => {
                            fetch(`{{ route('users.search') }}?query=${encodeURIComponent(query)}`)
                                .then(response => response.json())
                                .then(data => {
                                    suggestions.innerHTML = '';
                                    if (data.length > 0) {
                                        data.forEach(user => {
                                            const item = document.createElement('div');
                                            item.className = 'list-group-item d-flex align-items-center gap-2';
                                            item.innerHTML = `
                                                <img src="${user.profile_picture_url}" class="rounded-circle">
                                                <span>${user.username}</span>
                                            `;
                                            item.addEventListener('click', () => {
                                                input.value = user.username;
                                                suggestions.classList.add('d-none');
                                            });
                                            suggestions.appendChild(item);
                                        });
                                        suggestions.classList.remove('d-none');
                                    } else {
                                        suggestions.classList.add('d-none');
                                    }
                                });
                        }, 300);
                    });

                    document.addEventListener('click', function(e) {
                        if (!input.contains(e.target) && !suggestions.contains(e.target)) {
                            suggestions.classList.add('d-none');
                        }
                    });
                });
            </script>

            <div class="card bg-block-blue rounded-4 p-4 border-0 mb-4">
                <h2 class="jar-name text-center mb-4">Pending Requests</h2>
                @if($pendingRequests->isEmpty())
                    <p class="text-center">No pending friend requests.</p>
                @else
                    <ul class="list-group list-group-flush bg-transparent">
                        @foreach($pendingRequests as $req)
                            <li class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center border-0">
                                <a href="{{ route('users.show', $req->user->username) }}" class="text-light text-decoration-none d-flex align-items-center">
                                    <img src="{{ $req->user->profile_picture_url }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="fs-5">{{ $req->user->username }}</span>
                                </a>
                                <div class="d-flex gap-2">
                                    <form action="{{ route('friends.accept', $req->user) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-success rounded-pill px-3">Accept</button>
                                    </form>
                                    <form action="{{ route('friends.decline', $req->user) }}" method="POST">
                                        @csrf
                                        <button class="btn btn-sm btn-danger rounded-pill px-3">Decline</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="card bg-block-blue rounded-4 p-4 border-0 mb-4">
                <h2 class="jar-name text-center mb-4">Sent Requests</h2>
                @if($sentRequests->isEmpty())
                    <p class="text-center">No sent requests.</p>
                @else
                    <ul class="list-group list-group-flush bg-transparent">
                        @foreach($sentRequests as $req)
                            <li class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center border-0">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $req->friend->profile_picture_url }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="fs-5">{{ $req->friend->username }}</span>
                                </div>
                                <span class="badge bg-secondary rounded-pill">Pending</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="card bg-block-blue rounded-4 p-4 border-0 mb-4">
                <h2 class="jar-name text-center mb-4">My Friends</h2>
                @if($friends->isEmpty())
                    <p class="text-center">You have no friends yet.</p>
                @else
                    <ul class="list-group list-group-flush bg-transparent">
                        @foreach($friends as $friend)
                            <li class="list-group-item bg-transparent text-light d-flex justify-content-between align-items-center border-0">
                                <a href="{{ route('users.show', $friend->username) }}" class="text-light text-decoration-none d-flex align-items-center">
                                    <img src="{{ $friend->profile_picture_url }}" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    <span class="fs-5">{{ $friend->username }}</span>
                                </a>
                                <div>
                                    <a href="{{ route('chat.show', $friend) }}" class="btn btn-sm btn-info rounded-pill px-3 text-white">Message</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
