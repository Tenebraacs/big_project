@extends('layouts.app')

@section('content')
<style>
    /* Custom Scrollbar for chat and friends list */
    .chat-scroll::-webkit-scrollbar { width: 8px; }
    .chat-scroll::-webkit-scrollbar-track { background: transparent; }
    .chat-scroll::-webkit-scrollbar-thumb { background-color: #EAB2A0; border-radius: 10px; }
    .chat-scroll::-webkit-scrollbar-thumb:hover { background-color: #A76F6F; }

    /* Invite modal */
    .invite-modal-backdrop {
        display: none;
        position: fixed; inset: 0; z-index: 1050;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        align-items: center; justify-content: center;
    }
    .invite-modal-backdrop.open { display: flex; }
    .invite-modal {
        background: #1e2a3a;
        border: 1px solid rgba(234,178,160,0.2);
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        width: 100%; max-width: 460px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        animation: modalSlideIn 0.25s ease;
    }
    @keyframes modalSlideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to   { transform: translateY(0);     opacity: 1; }
    }
    .invite-jar-option {
        cursor: pointer;
        padding: 0.35rem 0.75rem;
        border-radius: 10px;
        border: 1px solid transparent;
        transition: all 0.2s;
        background: rgba(234,178,160,0.08);
    }
    .invite-jar-option:hover,
    .invite-jar-option.selected { border-color: #EAB2A0; background: rgba(234,178,160,0.18); }
    .invite-jar-option .jar-name {
        overflow: hidden; white-space: nowrap; text-overflow: ellipsis; min-width: 0;
    }

    /* Invite sent bubble in chat */
    .chat-invite-bubble {
        align-self: flex-end;
        max-width: 340px;
        background: linear-gradient(135deg, #A76F6F 0%, #7a4f6d 100%);
        border-radius: 18px 18px 4px 18px;
        padding: 0.65rem 0.9rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        animation: bubbleIn 0.3s cubic-bezier(.34,1.56,.64,1) both;
    }
    @keyframes bubbleIn {
        from { transform: scale(0.7) translateY(10px); opacity: 0; }
        to   { transform: scale(1)   translateY(0);    opacity: 1; }
    }
    .chat-invite-bubble .invite-jar-thumb {
        width: 36px; height: 36px; border-radius: 50%; object-fit: cover;
        border: 2px solid rgba(255,255,255,0.3);
    }
    .chat-invite-bubble .invite-label {
        font-size: 0.7rem; text-transform: uppercase; letter-spacing: .08em;
        color: rgba(255,255,255,0.6); margin-bottom: 2px;
    }
    .chat-invite-bubble .invite-jar-title {
        font-size: 0.92rem; font-weight: 600; color: #fff;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }

    /* Pending invitations panel */
    .inv-card {
        background: rgba(234,178,160,0.1);
        border: 1px solid rgba(234,178,160,0.25);
        border-radius: 14px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
    }
    .inv-badge {
        background: #e74c3c; color: #fff;
        font-size: 0.7rem; border-radius: 50%;
        width: 18px; height: 18px;
        display: inline-flex; align-items: center; justify-content: center;
        vertical-align: top; margin-left: 4px;
    }

    /* Custom Mobile Friend Selector */
    .mobile-friend-dropdown .dropdown-toggle::after {
        display: none;
    }
    .mobile-friend-dropdown .dropdown-menu {
        background: #1e2a3a;
        border: 1px solid rgba(234,178,160,0.3);
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        padding: 8px;
        backdrop-filter: blur(10px);
    }
    .mobile-friend-dropdown .dropdown-item {
        color: rgba(255,255,255,0.9);
        border-radius: 12px;
        padding: 8px 12px;
        transition: all 0.2s;
    }
    .mobile-friend-dropdown .dropdown-item:hover {
        background: rgba(234,178,160,0.15);
        color: #fff;
    }
    .mobile-friend-dropdown .dropdown-item.active {
        background: #A76F6F;
        color: #fff;
    }
</style>

<div class="container text-light mt-2" style="max-width: 1200px; height: calc(100vh - 200px); min-height: 400px;">
    <div class="row justify-content-center h-100 g-2">

        {{-- Friends sidebar --}}
        <div class="col-12 col-md-3 d-none d-md-flex flex-column h-100 gap-2">



            {{-- Friends list --}}
            <div class="card bg-block-blue rounded-4 p-3 border-0 flex-grow-1 overflow-auto chat-scroll">
                <h4 class="jar-name text-center mb-3">Friends</h4>
                <div class="list-group">
                    @foreach($users as $friend)
                        <a href="{{ route('chat.show', $friend) }}"
                           class="list-group-item list-group-item-action bg-transparent text-light border-0 d-flex align-items-center mb-2 {{ $user->id === $friend->id ? 'active' : '' }}"
                           style="background-color: {{ $user->id === $friend->id ? '#A76F6F' : '#eab3a0d5' }} !important; border-radius: 15px;">
                            <img src="{{ $friend->profile_picture_url }}" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;">
                            <span class="jar-name fs-5">{{ $friend->username }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Chat panel --}}
        <div class="col-12 col-md-9 d-flex flex-column h-100">
            <div class="card bg-block-blue rounded-4 border-0 d-flex flex-column flex-grow-1" style="height:100%;">

                {{-- Header --}}
                <div class="card-header border-0 pb-0 pt-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex flex-column gap-1">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ $user->profile_picture_url }}" class="rounded-circle" style="width:50px;height:50px;object-fit:cover;">
                            <a href="{{ route('users.show', $user->username) }}" class="text-light text-decoration-none">
                                <h3 class="jar-name m-0">{{ $user->username }}</h3>
                            </a>
                        </div>
                        {{-- Mobile Friend Selector --}}
                        <div class="d-md-none mt-1 mobile-friend-dropdown">
                            <div class="dropdown">
                                <button class="btn btn-sm text-light border-0 rounded-pill d-flex align-items-center justify-content-between px-3 w-100" 
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false"
                                        style="background: rgba(234,178,160,0.18); border: 1px solid rgba(234,178,160,0.15) !important;">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-people-fill small opacity-75"></i>
                                        <span class="small fw-semibold">Switch Chat</span>
                                    </div>
                                    <i class="bi bi-chevron-expand small opacity-50"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark w-100">
                                    @foreach($users as $friend)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 {{ $user->id === $friend->id ? 'active' : '' }}" 
                                               href="{{ route('chat.show', $friend) }}">
                                                <img src="{{ $friend->profile_picture_url }}" class="rounded-circle" style="width:24px;height:24px;object-fit:cover;">
                                                <span>{{ $friend->username }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- Invite to Jar button --}}
                    @php
                        $invitableJars = $myJars->filter(fn($j) => !in_array($j->id, $inviteeJarIds));
                    @endphp
                    @if($myJars->count())
                    <button id="open-invite-modal" class="btn btn-sm pink-button rounded-pill px-3"
                            title="Invite {{ $user->username }} to a Jar"
                            {{ $invitableJars->isEmpty() ? 'data-all-joined=true' : '' }}>
                        <i class="bi bi-envelope-plus me-1"></i> Invite to Jar
                    </button>
                    @endif
                </div>

                {{-- Messages --}}
                <div class="card-body overflow-auto chat-scroll d-flex flex-column gap-2" id="chat-box" style="flex-grow:1;"></div>

                {{-- Input --}}
                <div class="card-footer border-0 pb-3 pt-2">
                    <form id="chat-form" class="d-flex gap-2">
                        @csrf
                        <input type="text" id="message-input" class="form-control rounded-pill p-2 px-3 border-0"
                               placeholder="Type a message..." style="background-color:#eab3a0d5;" autocomplete="off" required>
                        <button type="submit" class="btn pink-button rounded-circle d-flex align-items-center justify-content-center"
                                style="width:40px;height:40px;">
                            <span style="font-size:1rem;">&#10148;</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- ===== Invite to Jar Modal ===== --}}
<div class="invite-modal-backdrop" id="invite-modal-backdrop">
    <div class="invite-modal text-light">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="jar-name m-0"><i class="bi bi-envelope-plus me-2"></i>Invite {{ $user->username }} to a Jar</h5>
            <button class="btn btn-sm text-light" id="close-invite-modal" style="font-size:1.4rem;line-height:1;">&times;</button>
        </div>

        <p class="text-muted small mb-2">Select one of your jars below:</p>

        <div class="d-flex flex-column gap-1 mb-3" id="jar-list" style="max-height: 340px; overflow-y: auto;">
            @forelse($myJars->filter(fn($j) => !in_array($j->id, $inviteeJarIds)) as $jar)
                <div class="invite-jar-option d-flex align-items-center gap-2"
                     data-jar-id="{{ $jar->id }}" onclick="selectJar(this)">
                    <img src="{{ $jar->picture_url }}" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;">
                    <span class="jar-name" style="font-size:0.9rem;">{{ $jar->name }}</span>
                </div>
            @empty
                <div class="text-center py-4" style="color: rgba(234,178,160,0.6);">
                    <i class="bi bi-check2-circle mb-2" style="font-size:2.5rem; display: block; color: #EAB2A0;"></i>
                    <p class="small mt-2 mb-0">{{ $user->username }} is already in all your jars!</p>
                </div>
            @endforelse
        </div>

        <div id="invite-feedback" class="mb-2" style="min-height:24px;"></div>

        <div class="d-flex gap-2 justify-content-end">
            <button class="btn btn-secondary rounded-pill px-4" id="close-invite-modal-2">Cancel</button>
            <button class="btn pink-button rounded-pill px-4" id="send-invite-btn" disabled>Send Invite</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Chat ──────────────────────────────────────────────
    const chatBox      = document.getElementById('chat-box');
    const chatForm     = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    const fetchUrl     = "{{ route('chat.fetch', $user) }}";
    const storeUrl     = "{{ route('chat.store', $user) }}";
    const authId       = {{ auth()->id() }};
    let isScrolledToBottom = true;

    chatBox.addEventListener('scroll', () => {
        isScrolledToBottom = chatBox.scrollHeight - chatBox.clientHeight <= chatBox.scrollTop + 10;
    });

    function fetchMessages() {
        fetch(fetchUrl)
            .then(r => r.json())
            .then(messages => {
                let newHtml = '';
                messages.forEach(msg => {
                    const isMine = msg.sender_id === authId;
                    if (msg.type === 'invite') {
                        const bgStyle = isMine 
                            ? 'background: linear-gradient(135deg, #A76F6F 0%, #7a4f6d 100%);' 
                            : 'background: #50585f;';

                        let actionHtml = '';
                        // Backend uses updated_at for expiration logic. ISO strings are UTC.
                        const inviteDate = new Date(msg.updated_at);
                        const isExpired = (Date.now() - inviteDate.getTime()) > 5 * 60 * 1000;
                        
                        if (msg.status === 'pending') {
                            if (isExpired) {
                                actionHtml = `<div class="mt-2 text-warning small fw-bold">Expired</div>`;
                            } else {
                                if (!isMine) {
                                    actionHtml = `
                                        <div class="mt-2 d-flex gap-2">
                                            <button class="btn btn-sm pink-button rounded-pill px-3" onclick="acceptJarInvite(${msg.id})">Accept</button>
                                            <button class="btn btn-sm btn-secondary rounded-pill px-3" onclick="declineJarInvite(${msg.id})">Decline</button>
                                        </div>`;
                                } else {
                                    actionHtml = `<div class="mt-2 text-light opacity-75 small">Pending</div>`;
                                }
                            }
                        } else if (msg.status === 'accepted') {
                            actionHtml = `<div class="mt-2 text-success small fw-bold">Accepted</div>`;
                        } else if (msg.status === 'declined') {
                            actionHtml = `<div class="mt-2 text-danger small fw-bold">Declined</div>`;
                        }

                        newHtml += `
                            <div class="chat-invite-bubble mb-1 ${isMine ? 'align-self-end' : 'align-self-start'}" style="${bgStyle} animation: none;">
                                <div class="invite-label"><i class="bi bi-envelope-fill me-1"></i> Jar Invite ${isMine ? 'Sent' : 'Received'}</div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    ${msg.jar_thumb ? `<img src="${msg.jar_thumb}" class="invite-jar-thumb" alt="">` : ''}
                                    <div class="invite-jar-title">${msg.jar_name}</div>
                                </div>
                                <div style="font-size:0.75rem; color:rgba(255,255,255,0.55); margin-top:4px;">
                                    ${msg.content}
                                </div>
                                ${actionHtml}
                            </div>`;
                    } else {
                        newHtml += `
                            <div class="message ${isMine ? 'align-self-end' : 'align-self-start'} py-2 px-3 mb-1 text-light shadow-sm"
                                 style="max-width:75%;background-color:${isMine ? '#A76F6F' : '#6c757d'};border-radius:15px;">
                                 ${msg.content}
                            </div>`;
                    }
                });

                if (chatBox.dataset.lastHtml !== newHtml) {
                    chatBox.innerHTML = newHtml;
                    chatBox.dataset.lastHtml = newHtml;
                    if (isScrolledToBottom) chatBox.scrollTop = chatBox.scrollHeight;
                }
            });
    }

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const content = messageInput.value;
        if (!content) return;
        fetch(storeUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: JSON.stringify({ content })
        }).then(r => r.json()).then(() => {
            messageInput.value = '';
            isScrolledToBottom = true;
            fetchMessages();
        });
    });

    fetchMessages();
    setInterval(fetchMessages, 3000);

    const jarInvitesBaseUrl = "{{ url('/jar-invitations') }}";

    window.acceptJarInvite = function(id) {
        console.log('Accepting invite:', id);
        fetch(`${jarInvitesBaseUrl}/${id}/accept`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value 
            }
        }).then(r => r.json()).then(data => {
            console.log('Accept response:', data);
            if(data.error) alert(data.error);
            fetchMessages();
        }).catch(err => console.error('Accept error:', err));
    };

    window.declineJarInvite = function(id) {
        console.log('Declining invite:', id);
        fetch(`${jarInvitesBaseUrl}/${id}/decline`, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value 
            }
        }).then(r => r.json()).then(data => {
            console.log('Decline response:', data);
            if(data.error) alert(data.error);
            fetchMessages();
        }).catch(err => console.error('Decline error:', err));
    };

    // ── Invite Modal ──────────────────────────────────────
    const backdrop       = document.getElementById('invite-modal-backdrop');
    const openBtn        = document.getElementById('open-invite-modal');
    const closeBtn       = document.getElementById('close-invite-modal');
    const closeBtn2      = document.getElementById('close-invite-modal-2');
    const sendBtn        = document.getElementById('send-invite-btn');
    const inviteFeedback = document.getElementById('invite-feedback');
    let selectedJarId    = null;

    if (openBtn) openBtn.addEventListener('click', () => { backdrop.classList.add('open'); inviteFeedback.innerHTML = ''; });
    [closeBtn, closeBtn2].forEach(b => b && b.addEventListener('click', () => {
        backdrop.classList.remove('open');
        clearSelection();
    }));
    backdrop.addEventListener('click', e => { if (e.target === backdrop) { backdrop.classList.remove('open'); clearSelection(); } });

    window.selectJar = function (el) {
        document.querySelectorAll('.invite-jar-option').forEach(o => o.classList.remove('selected'));
        el.classList.add('selected');
        selectedJarId = el.dataset.jarId;
        sendBtn.disabled = false;
        inviteFeedback.innerHTML = '';
    };

    function clearSelection() {
        document.querySelectorAll('.invite-jar-option').forEach(o => o.classList.remove('selected'));
        selectedJarId = null;
        sendBtn.disabled = true;
    }

    sendBtn.addEventListener('click', function () {
        if (!selectedJarId) return;
        sendBtn.disabled = true;
        sendBtn.textContent = 'Sending…';

        fetch("{{ route('jar-invitations.store') }}", {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value 
            },
            body: JSON.stringify({ jar_id: selectedJarId, invitee_id: {{ $user->id }} })
        })
        .then(async r => {
            if (!r.ok) {
                const err = await r.json().catch(() => ({}));
                throw new Error(err.error || err.message || 'Server error');
            }
            return r.json();
        })
        .then(data => {
            if (data.message) {
                // Grab jar details from the selected option before hiding it
                const sentOption = document.querySelector(`.invite-jar-option[data-jar-id="${selectedJarId}"]`);
                const jarName  = sentOption ? sentOption.querySelector('.jar-name')?.textContent.trim() : 'a Jar';
                const jarThumb = sentOption ? sentOption.querySelector('img')?.src : '';

                // Close modal
                backdrop.classList.remove('open');

                // Inject invite bubble into chat box
                const bubble = document.createElement('div');
                bubble.className = 'chat-invite-bubble mb-1';
                bubble.innerHTML = `
                    <div class="invite-label"><i class="bi bi-envelope-fill me-1"></i> Jar Invite Sent</div>
                    <div class="d-flex align-items-center gap-2 mt-1">
                        ${jarThumb ? `<img src="${jarThumb}" class="invite-jar-thumb" alt="">` : ''}
                        <div class="invite-jar-title">${jarName}</div>
                    </div>
                    <div style="font-size:0.75rem; color:rgba(255,255,255,0.55); margin-top:4px;">
                        Invite sent to {{ $user->username }}
                    </div>`;
                chatBox.appendChild(bubble);
                chatBox.scrollTop = chatBox.scrollHeight;

                // Hide the jar from modal list
                if (sentOption) sentOption.style.display = 'none';
                clearSelection();

                // If no visible jar options remain, show empty state
                const remaining = document.querySelectorAll('.invite-jar-option:not([style*="display: none"])');
                if (remaining.length === 0) {
                    document.getElementById('jar-list').innerHTML = `
                        <div class="text-center py-4" style="color: rgba(234,178,160,0.6);">
                            <i class="bi bi-check2-circle mb-2" style="font-size:2.5rem; display: block; color: #EAB2A0;"></i>
                            <p class="small mt-2 mb-0">{{ $user->username }} is now in all your jars!</p>
                        </div>`;
                    sendBtn.disabled = true;
                }
            } else if (data.error) {
                inviteFeedback.innerHTML = `<div class="text-danger small">${data.error}</div>`;
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send Invite';
            }
        })
        .catch(err => {
            console.error('Send invite error:', err);
            inviteFeedback.innerHTML = `<div class="text-danger small">Error: ${err.message}</div>`;
            sendBtn.disabled = false;
            sendBtn.textContent = 'Send Invite';
        });
    });

});
</script>
@endsection
