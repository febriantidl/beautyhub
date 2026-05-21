<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeautyHub MUA — @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #FAF8F6; color: #2C2423; }

        /* ── Sidebar ─────────────────── */
        .sidebar { position: fixed; left: 0; top: 0; width: 268px; height: 100vh; background: white; border-right: 1px solid #EDE8E3; padding: 1.75rem 1.25rem 1.25rem; display: flex; flex-direction: column; z-index: 100; overflow-y: auto; }
        .logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; color: #9B6B6B; margin-bottom: 2rem; padding-left: 0.5rem; letter-spacing: -0.02em; }
        .logo-accent { color: #C9A56E; }

        .nav-section-label { font-size: 0.67rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #B8ACA7; padding: 0 0.5rem; margin: 1.25rem 0 0.4rem; }
        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.2rem; }
        .nav-link { display: flex; align-items: center; gap: 0.7rem; padding: 0.65rem 0.875rem; color: #7A6F6E; text-decoration: none; border-radius: 11px; transition: all 0.18s; font-weight: 500; font-size: 0.875rem; position: relative; }
        .nav-link:hover { background: rgba(212,165,165,0.07); color: #8B5A5A; }
        .nav-link.active { background: linear-gradient(135deg, #D4A5A5 0%, #9B6B6B 100%); color: white; box-shadow: 0 4px 14px rgba(155,107,107,0.28); }
        .nav-icon { font-size: 0.95rem; width: 18px; text-align: center; flex-shrink: 0; }
        .nav-badge { margin-left: auto; background: #D4A5A5; color: white; border-radius: 9999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 700; }
        .nav-link.active .nav-badge { background: rgba(255,255,255,0.3); }

        .divider { height: 1px; background: #EDE8E3; margin: 1rem 0.5rem; }

        .user-profile { padding: 0.875rem; background: #FAF8F6; border-radius: 14px; display: flex; align-items: center; gap: 0.7rem; border: 1px solid #EDE8E3; margin-top: auto; }
        .user-avatar { width: 40px; height: 40px; border-radius: 11px; background: linear-gradient(135deg, #D4A5A5, #C9A56E); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 16px; flex-shrink: 0; overflow: hidden; }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-name { font-weight: 700; font-size: 0.825rem; color: #2C2423; }
        .user-role { font-size: 0.72rem; color: #9B8F8E; }

        /* ── Main ────────────────────── */
        .main-content { margin-left: 268px; padding: 2rem 2.25rem; min-height: 100vh; }

        /* ── Stats ───────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.4rem; border-radius: 18px; border: 1px solid #EDE8E3; transition: box-shadow 0.2s; }
        .stat-card:hover { box-shadow: 0 8px 20px rgba(155,107,107,0.09); }
        .stat-icon { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 0.875rem; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; margin-bottom: 0.2rem; color: #2C2423; }
        .stat-label { color: #7A6F6E; font-size: 0.825rem; }

        /* ── Card ────────────────────── */
        .card { background: white; border-radius: 18px; padding: 1.5rem; border: 1px solid #EDE8E3; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .card-title { font-size: 1.15rem; font-weight: 700; color: #2C2423; }
        .content-grid { display: grid; grid-template-columns: 1fr 320px; gap: 1.5rem; }

        /* ── Buttons ─────────────────── */
        .btn { padding: 0.5rem 1.25rem; border-radius: 11px; border: none; cursor: pointer; font-weight: 600; font-size: 0.85rem; font-family: inherit; transition: all 0.18s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; }
        .btn:hover { transform: translateY(-1px); }
        .btn-outline { background: transparent; border: 1px solid #D4A5A5; color: #9B6B6B; }
        .btn-outline:hover { background: rgba(212,165,165,0.07); }
        .btn-sm { padding: 0.3rem 0.8rem; font-size: 0.78rem; border-radius: 9px; }
        .btn-success { background: linear-gradient(135deg, #90C490, #5A8A5A); color: white; }
        .btn-primary { background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white; box-shadow: 0 3px 10px rgba(155,107,107,0.2); }

        /* ── Booking Items ────────────── */
        .booking-item { display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem; background: #FAF8F6; border-radius: 14px; margin-bottom: 0.625rem; }
        .booking-avatar { width: 48px; height: 48px; border-radius: 13px; background: linear-gradient(135deg, #C9A56E, #D4A5A5); display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 700; color: white; flex-shrink: 0; }
        .booking-info { flex: 1; min-width: 0; }
        .booking-name { font-weight: 700; color: #2C2423; font-size: 0.9rem; }
        .booking-date { font-size: 0.78rem; color: #7A6F6E; margin-top: 2px; }

        /* ── Badges ──────────────────── */
        .badge { display: inline-block; padding: 0.18rem 0.65rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; margin-top: 4px; }
        .badge-pending   { background: rgba(212,165,110,0.12); color: #9B7030; }
        .badge-approved  { background: rgba(100,160,100,0.12); color: #2E6A2E; }
        .badge-rejected  { background: rgba(183,110,110,0.12); color: #8A3030; }
        .badge-completed { background: rgba(100,150,190,0.12); color: #1E5A7A; }
        .badge-cancelled { background: rgba(160,160,160,0.12); color: #555; }
        .badge-verified  { background: rgba(70,150,200,0.12); color: #1A6A8E; }

        /* ── Utilities ───────────────── */
        .flex { display: flex; }
        .gap-2 { gap: 0.5rem; }
        .text-center { text-align: center; }
        .text-gray { color: #7A6F6E; }
        .font-bold { font-weight: 700; }
        .mb-8 { margin-bottom: 2rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .text-4xl { font-size: 1.875rem; font-family: 'Playfair Display', serif; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
        .p-8 { padding: 2rem; }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <a href="{{ route('mua.dashboard') }}" style="text-decoration: none;">
            <div class="logo">Beauty<span class="logo-accent">Hub</span></div>
        </a>

        <p class="nav-section-label">Menu Utama</p>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('mua.dashboard') }}" class="nav-link {{ request()->routeIs('mua.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('mua.bookings.index') }}" class="nav-link {{ request()->routeIs('mua.bookings.*') ? 'active' : '' }}">
                    <span class="nav-icon">📋</span> Kelola Booking
                    @php $pb = Auth::user()->mua?->bookings()->where('status','pending')->count() ?? 0; @endphp
                    @if($pb > 0)<span class="nav-badge">{{ $pb }}</span>@endif
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('mua.portfolio.index') }}" class="nav-link {{ request()->routeIs('mua.portfolio.*') ? 'active' : '' }}">
                    <span class="nav-icon">🖼️</span> Portfolio
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('mua.services.index') }}" class="nav-link {{ request()->routeIs('mua.services.*') ? 'active' : '' }}">
                    <span class="nav-icon">💄</span> Kelola Layanan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('mua.verification') }}" class="nav-link {{ request()->routeIs('mua.verification*') ? 'active' : '' }}">
                    <span class="nav-icon">📱</span> Verifikasi QR
                </a>
            </li>
        </ul>

        <div class="divider"></div>

        <p class="nav-section-label">Akun</p>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('mua.profile') }}" class="nav-link {{ request()->routeIs('mua.profile*') ? 'active' : '' }}">
                    <span class="nav-icon">👤</span> Profil Saya
                </a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('mua.logout') }}" id="sf-logout">@csrf</form>
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('sf-logout').submit();">
                    <span class="nav-icon">🚪</span> Logout
                </a>
            </li>
        </ul>

        <div class="user-profile" style="margin-top: 1.5rem;">
            <div class="user-avatar">
                @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="">
                @else
                    {{ substr(Auth::user()->name ?? 'M', 0, 1) }}
                @endif
            </div>
            <div style="min-width: 0; flex: 1;">
                <div class="user-name" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ Auth::user()->name ?? 'MUA' }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'mua') }} · BeautyHub</div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
