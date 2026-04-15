<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SideQuestJar') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/sass/ownapp.scss','resources/sass/app.scss', 'resources/js/app.js'])

    @livewireStyles
</head>
<body class="background">
    <div id="app" class=" d-flex flex-column min-vh-100">
        <nav class="navbar-base navbar navbar-expand-md navbar-light shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'SideQuestJar') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    

                    <ul class="navbar-nav me-auto">
                        @auth
                            <li class="nav-item">
                                    <a class="nav-link" href="{{  route("jars.create") }}">CreateJar</a>
                            </li>
                        @endauth

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    {{ Auth::user()->username }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4 grow">
            @yield('content')
        </main>

        <footer class="mt-auto">
            <div class="footer-base py-3">
                <div class="container-fluid d-flex justify-content-center align-items-center">

                    <!-- Left Side -->
                    <div class="col-6 text-start">
                        <p class="mb-0">+36 20 261 9875</p>
                        <p class="mb-0">sidequestjar@gmail.com</p>
                    </div>
                    <!-- Right Side -->
                    <div class="col-6 text-end">
                        <p class="mb-0">© 2026 SideQuestJar</p>
                    </div>
                
                </div>
            </div>
        </footer>
    </div>

    @livewireScripts
</body>
</html>
