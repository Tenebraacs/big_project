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
        padding: 2rem;
        width: 100%; max-width: 480px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        animation: modalSlideIn 0.25s ease;
    }
    @keyframes modalSlideIn {
        from { transform: translateY(-20px); opacity: 0; }
        to   { transform: translateY(0);     opacity: 1; }
    }
    .invite-jar-option {
        cursor: pointer;
        padding: 0.6rem 0.9rem;
        border-radius: 12px;
        border: 1px solid transparent;
        transition: all 0.2s;
        background: rgba(234,178,160,0.08);
    }
    .invite-jar-option:hover,
    .invite-jar-option.selected { border-color: #EAB2A0; background: rgba(234,178,160,0.18); }

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
</style>

<div class="container text-light mt-2" style="max-width: 1200px; height: calc(100vh - 200px); min-height: 400px;">
    <div class="row justify-content-center h-100 g-2">

        {{-- Friends sidebar --}}
        <div class="col-12 col-md-3 d-none d-md-flex flex-column h-100 gap-2">

            {{-- Pending jar invitations panel --}}
            <div class="card bg-block-blue rounded-4 p-3 border-0" id="inv-panel" style="display:none!important;">
                <h6 class="jar-name mb-2">
                    Jar Invites
                    <span class="inv-badge" id="inv-count">0</span>
                </h6>
                <div id="inv-list" style="max-height:200px; overflow-y:auto;"></div>
            </div>

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
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ $user->profile_picture_url }}" class="rounded-circle" style="width:50px;height:50px;object-fit:cover;">
                        <a href="{{ route('users.show', $user->username) }}" class="text-light text-decoration-none">
                            <h3 class="jar-name m-0">{{ $user->username }}</h3>
                        </a>
                    </div>

                    {{-- Invite to Jar button --}}
                    @if($myJars->count())
                    <button id="open-invite-modal" class="btn btn-sm pink-button rounded-pill px-3"
                            title="Invite {{ $user->username }} to a Jar">
                        🏺 Invite to Jar
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
            <h5 class="jar-name m-0">🏺 Invite {{ $user->username }} to a Jar</h5>
            <button class="btn btn-sm text-light" id="close-invite-modal" style="font-size:1.4rem;line-height:1;">&times;</button>
        </div>

        <p class="text-muted small mb-3">Select one of your jars below:</p>

        <div class="d-flex flex-column gap-2 mb-4" id="jar-list">
            @foreach($myJars as $jar)
                <div class="invite-jar-option d-flex align-items-center gap-2"
                     data-jar-id="{{ $jar->id }}" onclick="selectJar(this)">
                    <img src="{{ $jar->picture_url }}" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                    <span class="jar-name">{{ $jar->name }}</span>
                </div>
            @endforeach
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
                chatBox.innerHTML = '';
                messages.forEach(msg => {
                    const isMine = msg.sender_id === authId;
                    chatBox.innerHTML += `
                        <div class="message ${isMine ? 'align-self-end' : 'align-self-start'} py-2 px-3 mb-1 text-light shadow-sm"
                             style="max-width:75%;background-color:${isMine ? '#A76F6F' : '#6c757d'};border-radius:15px;">
                            ${msg.content}
                        </div>`;
                });
                if (isScrolledToBottom) chatBox.scrollTop = chatBox.scrollHeight;
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

    // ── Pending Invitations ───────────────────────────────
    const pendingUrl = "{{ route('jar-invitations.pending') }}";
    const invPanel   = document.getElementById('inv-panel');
    const invList    = document.getElementById('inv-list');
    const invCount   = document.getElementById('inv-count');

    function fetchPendingInvitations() {
        fetch(pendingUrl)
            .then(r => r.json())
            .then(invitations => {
                if (invitations.length === 0) {
                    invPanel.style.setProperty('display', 'none', 'important');
                    return;
                }
                invPanel.style.setProperty('display', 'block', 'important');
                invCount.textContent = invitations.length;
                invList.innerHTML = '';
                invitations.forEach(inv => {
                    const acceptUrl  = `/jar-invitations/${inv.id}/accept`;
                    const declineUrl = `/jar-invitations/${inv.id}/decline`;
                    invList.innerHTML += `
                        <div class="inv-card">
                            <div class="small mb-1">
                                <strong>${inv.inviter.username}</strong> invited you to
                                <strong>${inv.jar.name}</strong>
                            </div>
                            <div class="d-flex gap-2 mt-1">
                                <form method="POST" action="${acceptUrl}" style="display:inline;">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button class="btn btn-sm pink-button rounded-pill px-3">Accept</button>
                                </form>
                                <form method="POST" action="${declineUrl}" style="display:inline;">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button class="btn btn-sm btn-secondary rounded-pill px-3">Decline</button>
                                </form>
                            </div>
                        </div>`;
                });
            });
    }

    fetchPendingInvitations();
    setInterval(fetchPendingInvitations, 10000);

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
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
            body: JSON.stringify({ jar_id: selectedJarId, invitee_id: {{ $user->id }} })
        })
        .then(r => r.json())
        .then(data => {
            if (data.message) {
                inviteFeedback.innerHTML = `<span class="text-success small">✅ ${data.message}</span>`;
                clearSelection();
            } else {
                inviteFeedback.innerHTML = `<span class="text-danger small">⚠️ ${data.error ?? 'Something went wrong.'}</span>`;
                sendBtn.disabled = false;
            }
            sendBtn.textContent = 'Send Invite';
        })
        .catch(() => {
            inviteFeedback.innerHTML = `<span class="text-danger small">⚠️ Network error.</span>`;
            sendBtn.disabled = false;
            sendBtn.textContent = 'Send Invite';
        });
    });

});
</script>
@endsection
