@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col"> 
                <div class="row bg-black p-4">
                    <div class="col-md-6">
                        
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                        <div class="card-header">{{ __('Register') }}</div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf

                                
                                <div class="row mb-3">
                                    
                                    <label for="username" class="row-md-4 col-form-label text-md-start">{{ __('UserName') }}</label>
                                    <div class="row-md-6">
                                        <input id="username" type="text" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username') }}" placeholder="Username" required autocomplete="username" autofocus>

                                        @error('username')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="birth_date" class="row-md-4 col-form-label text-md-start">{{ __('Birth Date') }}</label>

                                    <div class="row-md-6">
                                        <input id="birth_date" type="date" class="form-control @error('birth_date') is-invalid @enderror" name="birth_date" value="{{ old('birth_date') }}" max="{{ date( 'Y-m-d') }}" autocomplete="birth_date" autofocus>

                                        @error('birth_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                    
                                <div class="row mb-3">       
                                    <label for="first_name" class="row-md-4 col-form-label text-md-start">{{ __('Full Name') }}</label>
                                    <div class="col">    
                                        
                                        <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required autocomplete="given-name">
                                        @error('first_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="col">                               
                                        <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required autocomplete="family-name">
                                        @error('last_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            
                                <div class="row mb-3">
                                    <label for="email" class="row-md-4 -form-label text-md-start">{{ __('Email Address') }}</label>

                                    <div class="row-md-6">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="hello@example.com" required autocomplete="email">

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
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" required autocomplete="new-password">

                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="password-confirm" class="row-md-4 col-form-label text-md-start">{{ __('Confirm Password') }}</label>

                                    <div class="row-md-6">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confrim Password" required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="row mb-0">
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary w-100 ">
                                            {{ __('Register') }}
                                        </button>
                                    </div>
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
