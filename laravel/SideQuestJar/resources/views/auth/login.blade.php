@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col">
            <div class="row bg-block-blue p-4 align-item-center">
                <div class="col-md-6">
                    <h2 class="logo-first">SIDE QUEST</h2>
                    <h1 class="logo-second">JAR</h1>
                    <br>
                    <p class="welcome-text">Welcome to The Amazing Digital Side Quest Jar!</p>
                </div>
                <div class="col-md-6">
                    <div class="card block-radius">

                    <div class="card-header form-text"><h3>{{ __('Login') }}</h3></div>

                    <div class="card-body form-text">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            
                            <div class="row mb-3">
                                <label for="email" class="row-md-4 col-form-label text-md-start">{{ __('Email Address') }}</label>
                                <div class="row-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="hello@example.com" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password" class="row-md-4 col-form-label text-md-start">{{ __('Password') }}</label>

                                <div class="row-md-6">
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                    <button type="submit" class="btn btn-primary w-100 reg-log-button rounded-pill">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link text-black" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>

                            <div class="row mb-0">
                                
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
