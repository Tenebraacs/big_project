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
                        Forgot your password? No worries!
                    </p>
                </div>

                <div class="col-md-6">
                    <div class="card claim-modal-content rounded-5 border-0">
                        <div class="card-header border-0 claim-modal-header" style="background: transparent;">
                            <h5 class="claim-modal-title m-0">{{ __('Reset Password') }}</h5>
                        </div>

                        <div class="card-body claim-modal-body">
                            @if (session('status'))
                                <div class="alert alert-success fw-bold border-0" style="background: #2D3E50; color: #FFD2C4;" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form method="POST" action="{{ route('password.email') }}">
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

                                <div class="d-flex flex-column gap-2 mt-4">
                                    <button type="submit" class="btn blue-button rounded-pill w-100">
                                        {{ __('Send Password Reset Link') }}
                                    </button>
                                    <a class="btn btn-link p-0 text-decoration-none mt-2 text-center" style="color:#FFD2C4;" href="{{ route('login') }}">
                                        {{ __('Back to Login') }}
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
