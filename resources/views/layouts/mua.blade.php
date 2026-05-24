<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeautyHub MUA — @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>

:root {
    --maroon: #800000;
    --gold: #D4AF37;
    --bg: #FDFBF7;
}

        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--bg); }

.btn-primary { background-color: var(--maroon) !important; color: #fff !important; }
.stat-icon { background: #F3EFEF !important; color: var(--maroon) !important; }
.stat-value { color: var(--maroon) !important; }
.booking-avatar { background: var(--gold) !important; }
.badge { border: 1px solid currentColor; }

        /* ── Sidebar ─────────────────── */
        .sidebar { position: fixed; left: 0; top: 0; width: 268px; height: 100vh; background: #4f0404; border-right: 1px solid #EDE8E3; padding: 1.75rem 1.25rem 1.25rem; display: flex; flex-direction: column; z-index: 100; overflow-y: auto; }
        .logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; color: #ffffff; margin-bottom: 2rem; padding-left: 0.5rem; letter-spacing: -0.02em; }
        .logo-accent { color: #C9A56E; }
        /* Pastikan ini ada di bagian bawah CSS lo, biar dia menang aturan */
.sidebar .nav-link.active {
    background: #ffffff !important; /* Ganti warnanya jadi Gold atau Maroon sesuka lo */
    color: #4f0404 !important;
    font-weight: 700 !important;
}

/* Biar ikonnya ikut ganti warna putih pas aktif */
.sidebar .nav-link.active svg {
    stroke: #4f0404 !important;
}
        .nav-section-label { font-size: 0.67rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #e7e7e7; padding: 0 0.5rem; margin: 1.25rem 0 0.4rem; }
        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.2rem; }
        .nav-link { display: flex; align-items: center; gap: 0.7rem; padding: 0.65rem 0.875rem; color: #ffffff; text-decoration: none; border-radius: 11px; transition: all 0.18s; font-weight: 500; font-size: 0.875rem; position: relative; }
        .nav-link:hover { background: #9f4d4d; color: #ffffff; }
        
        .nav-icon { font-size: 0.95rem; width: 18px; text-align: center; flex-shrink: 0; }
        .nav-badge { margin-left: auto; background: #D4A5A5; color: white; border-radius: 9999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 700; }
        .nav-badge { background: rgba(255,255,255,0.3); }

        .divider { height: 1px; background: #EDE8E3; margin: 1rem 0.5rem; }

        .user-profile { padding: 0.875rem; background: #ffffff; border-radius: 14px; display: flex; align-items: center; gap: 0.7rem; border: 1px solid #EDE8E3; margin-top: auto; }
        .user-avatar { width: 40px; height: 40px; border-radius: 11px; background: linear-gradient(135deg, #D4A5A5, #C9A56E); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 16px; flex-shrink: 0; overflow: hidden; }
        .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-name { font-weight: 700; font-size: 0.825rem; color: #2C2423; }
        .user-role { font-size: 0.72rem; color: #9B8F8E; }

        /* ── Main ────────────────────── */
        .main-content { margin-left: 268px; padding: 2rem 2.25rem; min-height: 100vh; background: white;}

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
                <a href="{{ route('mua.dashboard') }}" class="nav-link ...">
    <svg style="width:18px; fill:none; stroke:currentColor; stroke-width:2;" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect></svg>
    Dashboard
</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('mua.bookings.index') }}" class="nav-link ...">
    <svg style="width:18px; fill:none; stroke:currentColor; stroke-width:2;" viewBox="0 0 24 24"><path d="M19 4H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"></path><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
    Kelola Booking
</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('mua.portfolio.index') }}" class="nav-link ...">
    <svg style="width:18px; fill:none; stroke:currentColor; stroke-width:2;" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><path d="M21 15l-5-5L5 21"></path></svg>
    Portfolio
</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('mua.services.index') }}" class="nav-link ...">
    <svg style="width:18px; fill:none; stroke:currentColor; stroke-width:2;" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
    Kelola Layanan
</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('mua.verification') }}" class="nav-link ...">
    <svg style="width:18px; fill:none; stroke:currentColor; stroke-width:2;" viewBox="0 0 24 24"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
    Verifikasi QR
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
