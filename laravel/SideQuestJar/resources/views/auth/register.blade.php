@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="row rounded-5 bg-block-blue p-4 align-item-center">
                <div class="col-md-6">
                    <h2 class="logo-first mt-3">SIDE QUEST</h2>
                    <h1 class="logo-second display-1">JAR</h1>
                    <br>
                    <p class="welcome-text m-1.5 fs-1" style="transform: rotate(5deg);">
                        Welcome to The Amazing Digital Side Quest Jar!
                    </p>
                </div>

                <div class="col-md-6">
                    <div class="card claim-modal-content rounded-5 border-0">
                        <div class="card-header border-0 claim-modal-header" style="background: transparent;">
                            <h5 class="claim-modal-title m-0">{{ __('Register') }}</h5>
                        </div>

                        <div class="card-body claim-modal-body">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="username" class="claim-label">{{ __('UserName') }}</label>
                                    <input
                                        id="username"
                                        type="text"
                                        class="form-control claim-input @error('username') is-invalid @enderror"
                                        name="username"
                                        value="{{ old('username') }}"
                                        placeholder="Username"
                                        required
                                        autocomplete="username"
                                        autofocus
                                    >
                                    @error('username')
                                        <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="birth_date" class="claim-label">{{ __('Birth Date') }}</label>

                                    <div class="input-group claim-input-group">
                                        <span class="input-group-text claim-input-addon">
                                        <i class="bi bi-calendar3"></i>
                                        </span>

                                        <input
                                            id="birth_date"
                                            type="text"
                                            class="form-control claim-input claim-date js-birthdate @error('birth_date') is-invalid @enderror"
                                            name="birth_date"
                                            value="{{ old('birth_date') }}"
                                            placeholder="YYYY-MM-DD"
                                            autocomplete="bday"
                                            />
                                    </div>

                                    <div class="claim-help">You must be 16+</div>

                                    @error('birth_date')
                                        <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                        {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="claim-label">{{ __('Full Name') }}</label>
                                    <div class="row g-2">
                                        <div class="col-12 col-sm-6">
                                            <input
                                                id="first_name"
                                                type="text"
                                                class="form-control claim-input @error('first_name') is-invalid @enderror"
                                                name="first_name"
                                                value="{{ old('first_name') }}"
                                                placeholder="First Name"
                                                required
                                                autocomplete="given-name"
                                            >
                                            @error('first_name')
                                                <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="col-12 col-sm-6">
                                            <input
                                                id="last_name"
                                                type="text"
                                                class="form-control claim-input @error('last_name') is-invalid @enderror"
                                                name="last_name"
                                                value="{{ old('last_name') }}"
                                                placeholder="Last Name"
                                                required
                                                autocomplete="family-name"
                                            >
                                            @error('last_name')
                                                <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="claim-label">{{ __('Email Address') }}</label>
                                    <input
                                        id="email"
                                        type="email"
                                        class="form-control claim-input @error('email') is-invalid @enderror"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="hello@example.com"
                                        required
                                        autocomplete="email"
                                    >
                                    @error('email')
                                        <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="claim-label">{{ __('Password') }}</label>
                                    <input
                                        id="password"
                                        type="password"
                                        class="form-control claim-input @error('password') is-invalid @enderror"
                                        name="password"
                                        placeholder="••••••••"
                                        required
                                        autocomplete="new-password"
                                    >
                                    @error('password')
                                        <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password-confirm" class="claim-label">{{ __('Confirm Password') }}</label>
                                    <input
                                        id="password-confirm"
                                        type="password"
                                        class="form-control claim-input"
                                        name="password_confirmation"
                                        placeholder="••••••••"
                                        required
                                        autocomplete="new-password"
                                    >
                                </div>

                                <div class="d-flex flex-column gap-2 mt-3">
                                    <button type="submit" class="btn w-100 blue-button rounded-pill">
                                        {{ __('Register') }}
                                    </button>

                                    {{-- optional: add a consistent "back to login" link (doesn't change fundamentals) --}}
                                    <a class="btn btn-link p-0 text-decoration-none" style="color:#FFD2C4;"
                                       href="{{ route('login') }}">
                                        Already have an account? Login
                                    </a>
                                </div>

                            </form>
                        </div>
                    </div>
                    {{-- /card --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection