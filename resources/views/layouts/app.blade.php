<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Koja') · Koja</title>

    <meta
        name="description"
        content="Discover salons, explore their details, and read reviews on Koja."
    >

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    @stack('styles')
</head>
<body>
    <div class="site-wrapper">

        {{-- Navigation --}}
        <header class="site-header">
            <div class="container nav-container">

                <a href="{{ route('salons.index') }}" class="brand">
                    Koja<span class="brand-dot">.</span>
                </a>

                <nav class="nav-links" aria-label="Main navigation">
                    <a href="{{ route('salons.index') }}">
                        Explore salons
                    </a>

                    @auth
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}">
                                Admin dashboard
                            </a>
                        @elseif (auth()->user()->role === 'owner')
                            <a href="{{ route('owner.dashboard') }}">
                                Owner dashboard
                            </a>
                        @endif

                        @if (
                            auth()->user()->role !== 'admin'
                            && ! auth()->user()->salon()->exists()
                        )
                            <a href="{{ route('salons.owner-register') }}">
                                List your salon
                            </a>
                        @endif
                    @endauth
                </nav>

                <div class="nav-account">
                    @guest
                        <a
                            href="{{ route('login') }}"
                            class="button button-ghost"
                        >
                            Log in
                        </a>

                        <a
                            href="{{ route('register') }}"
                            class="button button-primary"
                        >
                            Sign up
                        </a>
                    @else
                        <span class="nav-user">
                            {{ auth()->user()->name }}
                        </span>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="button button-ghost"
                            >
                                Log out
                            </button>
                        </form>
                    @endguest
                </div>

            </div>
        </header>

        {{-- Flash messages and validation errors --}}
        <div class="container flash-container" aria-live="polite">
            @if (session('success'))
                <div class="alert alert-success" role="status">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-error" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-error" role="alert">
                    <strong>Please correct the following errors:</strong>

                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        {{-- Current page --}}
        <main class="site-main">
            @yield('content')
        </main>

        {{-- Footer --}}
        <footer class="site-footer">
            <div class="container footer-content">
                <a href="{{ route('salons.index') }}" class="footer-brand">
                    Koja<span class="brand-dot">.</span>
                </a>

                <p>Find your next favorite salon.</p>

                <span class="footer-copyright">
                    &copy; {{ date('Y') }} Koja
                </span>
            </div>
        </footer>

    </div>

    @stack('scripts')
</body>
</html>