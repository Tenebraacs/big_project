@extends('layouts.app')

@section('content')
<div class="container-fluid text-light mt-4 h-100">
    <div class="row justify-content-center" style="height: 80vh;">
        <div class="col-md-3 d-none d-md-block">
            <div class="card bg-block-blue rounded-4 p-3 border-0 h-100 overflow-auto">
                <h4 class="jar-name text-center mb-3">Friends</h4>
                <div class="list-group">
                    @foreach($users as $friend)
                        <a href="{{ route('chat.show', $friend) }}" class="list-group-item list-group-item-action bg-transparent text-light border-0 d-flex align-items-center mb-2 {{ $user->id === $friend->id ? 'active' : '' }}" style="background-color: {{ $user->id === $friend->id ? '#A76F6F' : '#eab3a0d5' }} !important; border-radius: 15px;">
                            <img src="{{ $friend->profile_picture_url }}" class="rounded-circle me-2" style="width: 35px; height: 35px; object-fit: cover;">
                            <span class="jar-name fs-5">{{ $friend->username }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-md-9 d-flex flex-column">
            <div class="card bg-block-blue rounded-4 border-0 d-flex flex-column flex-grow-1" style="height: 100%;">
                <div class="card-header border-0 pb-0 pt-3 d-flex align-items-center">
                    <img src="{{ $user->profile_picture_url }}" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                    <a href="{{ route('users.show', $user->username) }}" class="text-light text-decoration-none">
                        <h3 class="jar-name m-0">{{ $user->username }}</h3>
                    </a>
                </div>
                <div class="card-body overflow-auto d-flex flex-column gap-2" id="chat-box" style="flex-grow: 1;">
                    <!-- Messages will be injected here via JS -->
                </div>
                <div class="card-footer border-0 pb-3 pt-0">
                    <form id="chat-form" class="d-flex gap-2">
                        @csrf
                        <input type="text" id="message-input" class="form-control rounded-pill p-3 border-0" placeholder="Type a message..." style="background-color: #eab3a0d5;" autocomplete="off" required>
                        <button type="submit" class="btn pink-button rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <span style="font-size: 1.2rem;">&#10148;</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chatBox = document.getElementById('chat-box');
        const chatForm = document.getElementById('chat-form');
        const messageInput = document.getElementById('message-input');
        const fetchUrl = "{{ route('chat.fetch', $user) }}";
        const storeUrl = "{{ route('chat.store', $user) }}";
        const authId = {{ auth()->id() }};
        let isScrolledToBottom = true;

        chatBox.addEventListener('scroll', () => {
            isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 10;
        });

        function fetchMessages() {
            fetch(fetchUrl)
                .then(res => res.json())
                .then(messages => {
                    chatBox.innerHTML = '';
                    messages.forEach(msg => {
                        const isMine = msg.sender_id === authId;
                        const alignClass = isMine ? 'align-self-end' : 'align-self-start';

                        chatBox.innerHTML += `
                            <div class="message ${alignClass} p-3 mb-1 text-light shadow-sm" style="max-width: 75%; background-color: ${isMine ? '#A76F6F' : '#6c757d'}; border-radius: 15px;">
                                ${msg.content}
                            </div>
                        `;
                    });
                    if (isScrolledToBottom) {
                        chatBox.scrollTop = chatBox.scrollHeight;
                    }
                });
        }

        chatForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const content = messageInput.value;
            if (!content) return;

            fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({ content })
            }).then(res => res.json())
              .then(data => {
                  messageInput.value = '';
                  isScrolledToBottom = true;
                  fetchMessages();
              });
        });

        fetchMessages();
        setInterval(fetchMessages, 3000); // Poll every 3 seconds
    });
</script>
@endsection
