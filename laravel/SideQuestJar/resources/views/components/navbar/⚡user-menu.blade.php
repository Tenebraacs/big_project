<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    protected $listeners = [
        'profile-updated' => '$refresh',
    ];

    public function getUserProperty()
    {
        return Auth::user()?->fresh();
    }
};
?>

@if ($this->user)
    <li class="nav-item dropdown user-dropdown">
        <a class="nav-link dropdown-toggle user-dropdown-toggle d-flex align-items-center gap-2 user-dropdown-toggle"
           href="#"
           role="button"
           data-bs-toggle="dropdown"
           aria-expanded="false">

            <span class="user-avatar">
                @if (!empty($this->user->profile_picture))
                    <img src="{{ $this->user->profile_picture_url }}" alt="{{ $this->user->username }}">
                @else
                    <span class="user-avatar-fallback">
                        {{ strtoupper(substr($this->user->username, 0, 1)) }}
                    </span>
                @endif
            </span>

            <span class="user-name">{{ $this->user->username }}</span>
        </a>

        <div class="dropdown-menu dropdown-menu-end user-dropdown-menu">

            <div class="user-dropdown-header">
                <div class="user-dropdown-avatar">
                    @if (!empty($this->user->profile_picture))
                        <img src="{{ $this->user->profile_picture_url }}" alt="{{ $this->user->username }}">
                    @else
                        <span class="user-avatar-fallback">
                            {{ strtoupper(substr($this->user->username, 0, 1)) }}
                        </span>
                    @endif
                </div>

                <div class="user-dropdown-info">
                    <div class="user-dropdown-name">{{ $this->user->username }}</div>
                    @if (!empty($this->user->email))
                        <div class="user-dropdown-email">{{ $this->user->email }}</div>
                    @endif
                </div>
            </div>

            <div class="dropdown-divider"></div>

            {{-- Keep your streak component inside the menu (mobile style) --}}
            <div class="mobile-dropdown-streak">
                <livewire:navbar.navbar-streak-mobile />
            </div>

            <div class="dropdown-divider"></div>

            <a class="dropdown-item user-dropdown-item" href="{{ route('users.profile') }}">
                <i class="bi bi-person-circle me-2"></i>
                Profile
            </a>

            <a class="dropdown-item user-dropdown-item" href="{{ route('jars.create') }}">
                <i class="bi bi-plus-circle me-2"></i>
                Create Jar
            </a>

            @if($this->user->is_admin)
                <a class="dropdown-item user-dropdown-item" href="{{ route('users.index') }}">
                    <i class="bi bi-people me-2"></i>
                    Manage Users
                </a>
            @endif

            <div class="dropdown-divider"></div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item user-dropdown-item user-dropdown-logout">
                    <i class="bi bi-box-arrow-right me-2"></i>
                    Logout
                </button>
            </form>
        </div>
    </li>
@endif