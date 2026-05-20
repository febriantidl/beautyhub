<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeautyHub MUA - @yield('title')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #FAF8F6; }
        .sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: white; border-right: 1px solid #E8E2DD; padding: 2rem; }
        .logo { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; color: #9B6B6B; margin-bottom: 2rem; }
        .logo-accent { color: #C9A56E; }
        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.5rem; }
        .nav-link { display: flex; align-items: center; padding: 0.75rem 1rem; color: #6B6160; text-decoration: none; border-radius: 12px; transition: all 0.2s; }
        .nav-link:hover { background: rgba(212, 165, 165, 0.08); color: #9B6B6B; }
        .nav-link.active { background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white; }
        .main-content { margin-left: 280px; padding: 2rem; min-height: 100vh; }
        .user-profile { position: absolute; bottom: 2rem; left: 2rem; right: 2rem; padding: 1rem; background: #FAF8F6; border-radius: 16px; display: flex; align-items: center; gap: 0.75rem; }
        .user-avatar { width: 48px; height: 48px; border-radius: 12px; background: linear-gradient(135deg, #D4A5A5, #C9A56E); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 20px; border: 1px solid #E8E2DD; }
        .stat-icon { width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 1rem; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; margin-bottom: 0.25rem; }
        .stat-label { color: #6B6160; font-size: 14px; }
        .card { background: white; border-radius: 20px; padding: 1.5rem; border: 1px solid #E8E2DD; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .card-title { font-size: 1.5rem; font-weight: 700; }
        .btn { padding: 0.5rem 1rem; border-radius: 12px; border: none; cursor: pointer; font-weight: 500; }
        .btn-outline { background: transparent; border: 1px solid #D4A5A5; color: #9B6B6B; }
        .btn-sm { padding: 0.25rem 0.75rem; font-size: 0.875rem; }
        .btn-success { background: #7FA87F; color: white; }
        .booking-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #FAF8F6; border-radius: 16px; margin-bottom: 0.75rem; }
        .booking-avatar { width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #C9A56E, #D4A5A5); display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; color: white; }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 12px; font-weight: 600; text-transform: uppercase; }
        .badge-pending { background: rgba(212, 165, 110, 0.1); color: #D4A56E; }
        .flex { display: flex; }
        .gap-2 { gap: 0.5rem; }
        .text-center { text-align: center; }
        .text-gray { color: #6B6160; }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="logo">Beauty<span class="logo-accent">Hub</span></div>
        <nav>
            <ul class="nav-menu">
                <li class="nav-item"><a href="{{ route('mua.dashboard') }}" class="nav-link {{ request()->routeIs('mua.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Kelola Booking</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Portfolio</a></li>
                <li class="nav-item"><a href="#" class="nav-link">Verifikasi QR</a></li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('mua.logout') }}" id="logout-form" style="display: none;">@csrf</form>
                    <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a>
                </li>
            </ul>
        </nav>
        <div class="user-profile">
            <div class="user-avatar">{{ substr(Auth::user()->name ?? 'M', 0, 1) }}</div>
            <div>
                <h4 class="text-sm font-semibold">{{ Auth::user()->name ?? 'MUA' }}</h4>
                <p class="text-xs text-gray">MUA Professional</p>
            </div>
        </div>
    </aside>
    <main class="main-content">
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>