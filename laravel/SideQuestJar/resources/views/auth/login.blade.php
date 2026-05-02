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
                    {{-- keep it a "card", but style it like the modal --}}
                    <div class="card claim-modal-content rounded-5 border-0">
                        <div class="card-header border-0 claim-modal-header" style="background: transparent;">
                            <h5 class="claim-modal-title m-0">{{ __('Login') }}</h5>
                        </div>

                        <div class="card-body claim-modal-body">
                            <form method="POST" action="{{ route('login') }}">
                                @csrf

                                <div class="mb-3">
                                    <label for="email" class="claim-label">{{ __('Email Address') }}</label>
                                    <input
                                        id="email"
                                        type="email"
                                        class="form-control claim-input @error('email') is-invalid @enderror"
                                        name="email"
                                        value="{{ old('email') }}"
                                        placeholder="you@example.com"
                                        required
                                        autocomplete="email"
                                        autofocus
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
                                        autocomplete="current-password"
                                    >
                                    @error('password')
                                        <div class="mt-2 small fw-bold" style="color:#FFD2C4;">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input claim-check"
                                        type="checkbox"
                                        name="remember"
                                        id="remember"
                                        {{ old('remember') ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label claim-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>

                                <div class="d-flex flex-column gap-2">
                                    <button type="submit" class="btn blue-button rounded-pill w-100">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link p-0 text-decoration-none" style="color:#FFD2C4;"
                                           href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
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