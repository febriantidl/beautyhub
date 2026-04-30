<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautyHub - @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="logo">Beauty<span class="logo-accent">Hub</span></div>
        <nav>
            <ul class="nav-menu">
                <li class="nav-item"><a href="{{ route('mua.dashboard') }}" class="nav-link {{ request()->routeIs('mua.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li class="nav-item"><a href="{{ route('mua.bookings') }}" class="nav-link">Kelola Booking</a></li>
                <li class="nav-item"><a href="{{ route('mua.portfolio') }}" class="nav-link">Portfolio</a></li>
                <li class="nav-item"><a href="{{ route('mua.verification') }}" class="nav-link">Verifikasi QR</a></li>
                <li class="nav-item"><a href="{{ route('mua.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="nav-link">Logout</a></li>
            </ul>
        </nav>
        <div class="user-profile">
            <div class="user-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
            <div>
                <h4 class="text-sm font-semibold">{{ Auth::user()->name }}</h4>
                <p class="text-xs text-gray">MUA Professional</p>
            </div>
        </div>
    </aside>
    <main class="main-content">
        @yield('content')
    </main>
    <form id="logout-form" action="{{ route('mua.logout') }}" method="POST" style="display: none;">@csrf</form>
    <script src="{{ asset('js/utils.js') }}"></script>
    @stack('scripts')
</body>
</html>