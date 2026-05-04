<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SideQuestJar') }}</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"/>

    @vite(['resources/sass/ownapp.scss','resources/sass/app.scss', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="background">

<livewire:auth.login-modal />

<div id="app" class="d-flex flex-column min-vh-100">

    <nav class="navbar-base navbar navbar-expand-md navbar-light">
        <div class="container-fluid">

            <!-- Top row -->
            <div class="nav-top w-100 d-flex align-items-center justify-content-between">
                <a class="navbar-brand mb-0 brand-link" href="{{ url('/') }}">
                    <span class="mobile-brand-logo d-md-none">SQJ</span>

                    <span class="logo-second fs-3 d-none d-md-inline"
                          style="text-shadow: 0px 3px black; color: #EAB2A0;">
                        {{ config('app.name', 'SideQuestJar') }}
                    </span>
                </a>

                {{-- Mobile: user menu only --}}
                <div class="d-flex d-md-none align-items-center">
                    @auth
                        <div class="me-3 d-flex align-items-center">
                            <livewire:navbar.navbar-streak />
                        </div>
                        <ul class="navbar-nav m-0 p-0">
                            
                            <livewire:navbar.user-menu wire:key="navbar-user-menu-mobile" />
                        </ul>
                    @else
                        <div class="auth-buttons d-flex align-items-center gap-2">
                            <a href="{{ route('login') }}" class="guest-auth-chip">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Login</span>
                            </a>

                            <a href="{{ route('register') }}" class="guest-auth-chip">
                                <i class="bi bi-person-plus"></i>
                                <span>Register</span>
                            </a>
                        </div>
                    @endauth
                </div>

                {{-- Desktop only hamburger --}}
                <button class="navbar-toggler d-none" type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent"
                        aria-controls="navbarSupportedContent"
                        aria-expanded="false"
                        aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Desktop only collapsible area -->
            <div class="collapse navbar-collapse mt-3 mt-md-0 d-none d-md-flex" id="navbarSupportedContent">
                <div class="nav-inner w-100">

                    <div class="nav-left"></div>

                    <div class="nav-center">
                        @auth
                            <livewire:navbar.navbar-streak />
                        @endauth
                    </div>

                    <div class="nav-right">
                        <ul class="navbar-nav">
                            @guest
                                <li class="nav-item">
                                    <div class="auth-buttons d-flex align-items-center gap-2">
                                        <a href="{{ route('login') }}" class="guest-auth-chip">
                                            <i class="bi bi-box-arrow-in-right"></i>
                                            <span>Login</span>
                                        </a>

                                        <a href="{{ route('register') }}" class="guest-auth-chip">
                                            <i class="bi bi-person-plus"></i>
                                            <span>Register</span>
                                        </a>
                                    </div>
                                </li>
                            @else
                                
                                <livewire:navbar.user-menu wire:key="navbar-user-menu-desktop" />
                            @endguest
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </nav>

    <main class="py-4 grow">
        @yield('content')
    </main>

    <footer class="mt-auto">
        <div class="footer-base py-3">
            <div class="container-fluid">
                <div class="row align-items-center">
                    <div class="col-12 col-md-6 text-center text-md-start mb-2 mb-md-0">
                        <p class="mb-0">+36 20 261 9875</p>
                        <p class="mb-0">sidequestjar@gmail.com</p>
                    </div>

                    <div class="col-12 col-md-6 text-center text-md-end">
                        <p class="mb-0">© 2026 SideQuestJar</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

</div>

@livewireScripts
@stack('scripts')
</body>
</html>