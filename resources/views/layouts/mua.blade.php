<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeautyHub MUA — @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #FAF8F6; color: #2C2423; }

        /* ── Sidebar ─────────────────────────────────── */
        .sidebar {
            position: fixed; left: 0; top: 0;
            width: 280px; height: 100vh;
            background: white;
            border-right: 1px solid #E8E2DD;
            padding: 2rem 1.5rem;
            display: flex; flex-direction: column;
            z-index: 100;
        }
        .logo {
            font-family: 'Playfair Display', serif;
            font-size: 26px; font-weight: 700;
            color: #9B6B6B; margin-bottom: 2.5rem;
            padding-left: 0.5rem;
        }
        .logo-accent { color: #C9A56E; }

        .nav-section-label {
            font-size: 0.7rem; font-weight: 700;
            letter-spacing: 0.1em; text-transform: uppercase;
            color: #9B8F8E; padding: 0 0.5rem; margin-bottom: 0.5rem; margin-top: 1rem;
        }
        .nav-menu { list-style: none; flex: 1; }
        .nav-item { margin-bottom: 0.25rem; }
        .nav-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.7rem 1rem; color: #6B6160;
            text-decoration: none; border-radius: 12px;
            transition: all 0.2s; font-weight: 500; font-size: 0.9rem;
        }
        .nav-link:hover { background: rgba(212,165,165,0.08); color: #9B6B6B; }
        .nav-link.active { background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white; box-shadow: 0 4px 12px rgba(155,107,107,0.25); }
        .nav-link .nav-icon { font-size: 1rem; width: 20px; text-align: center; }
        .nav-link .nav-badge {
            margin-left: auto; background: #D4A5A5; color: white;
            border-radius: 9999px; padding: 0.1rem 0.5rem; font-size: 0.7rem; font-weight: 700;
        }
        .nav-link.active .nav-badge { background: rgba(255,255,255,0.3); }

        .user-profile {
            padding: 1rem; background: #FAF8F6; border-radius: 16px;
            display: flex; align-items: center; gap: 0.75rem;
            border: 1px solid #E8E2DD; margin-top: auto;
        }
        .user-avatar {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #D4A5A5, #C9A56E);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; color: white; font-size: 18px; flex-shrink: 0;
        }
        .user-name { font-weight: 700; font-size: 0.875rem; color: #2C2423; }
        .user-role { font-size: 0.75rem; color: #9B8F8E; }

        /* ── Main Content ─────────────────────────────── */
        .main-content { margin-left: 280px; padding: 2rem; min-height: 100vh; }

        /* ── Cards & Stats ────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.5rem; border-radius: 20px; border: 1px solid #E8E2DD; transition: box-shadow 0.2s; }
        .stat-card:hover { box-shadow: 0 8px 16px rgba(155,107,107,0.1); }
        .stat-icon { width: 48px; height: 48px; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 1rem; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 2.25rem; font-weight: 700; margin-bottom: 0.25rem; color: #2C2423; }
        .stat-label { color: #6B6160; font-size: 0.875rem; }

        .card { background: white; border-radius: 20px; padding: 1.5rem; border: 1px solid #E8E2DD; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .card-title { font-size: 1.25rem; font-weight: 700; color: #2C2423; }
        .content-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }

        /* ── Buttons ──────────────────────────────────── */
        .btn { padding: 0.5rem 1.25rem; border-radius: 12px; border: none; cursor: pointer; font-weight: 600; font-size: 0.875rem; font-family: inherit; transition: all 0.2s; }
        .btn:hover { transform: translateY(-1px); }
        .btn-outline { background: transparent; border: 1px solid #D4A5A5; color: #9B6B6B; }
        .btn-outline:hover { background: rgba(212,165,165,0.08); }
        .btn-sm { padding: 0.25rem 0.75rem; font-size: 0.8rem; }
        .btn-success { background: linear-gradient(135deg, #95C995, #5A8A5A); color: white; }
        .btn-primary { background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white; }

        /* ── Booking Items ────────────────────────────── */
        .booking-item { display: flex; align-items: center; gap: 1rem; padding: 1rem; background: #FAF8F6; border-radius: 16px; margin-bottom: 0.75rem; }
        .booking-avatar { width: 52px; height: 52px; border-radius: 14px; background: linear-gradient(135deg, #C9A56E, #D4A5A5); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 700; color: white; flex-shrink: 0; }
        .booking-info { flex: 1; min-width: 0; }
        .booking-name { font-weight: 700; color: #2C2423; }
        .booking-date { font-size: 0.8rem; color: #6B6160; margin-top: 2px; }

        /* ── Badges ───────────────────────────────────── */
        .badge { display: inline-block; padding: 0.2rem 0.75rem; border-radius: 9999px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; margin-top: 4px; }
        .badge-pending   { background: rgba(212,165,110,0.12); color: #B88A40; }
        .badge-approved  { background: rgba(127,168,127,0.12); color: #3E7A3E; }
        .badge-rejected  { background: rgba(183,110,110,0.12); color: #A04040; }
        .badge-completed { background: rgba(110,157,183,0.12); color: #2E6B8A; }
        .badge-cancelled { background: rgba(150,150,150,0.12); color: #555; }
        .badge-verified  { background: rgba(80,160,200,0.12); color: #2A7A9E; }

        /* ── Utilities ────────────────────────────────── */
        .flex { display: flex; }
        .gap-2 { gap: 0.5rem; }
        .text-center { text-align: center; }
        .text-gray { color: #6B6160; }
        .font-bold { font-weight: 700; }
        .mb-8 { margin-bottom: 2rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .text-4xl { font-size: 2rem; font-family: 'Playfair Display', serif; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <div class="logo">Beauty<span class="logo-accent">Hub</span></div>

        <nav>
            <p class="nav-section-label">Menu Utama</p>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="{{ route('mua.dashboard') }}"
                       class="nav-link {{ request()->routeIs('mua.dashboard') ? 'active' : '' }}">
                        <span class="nav-icon">📊</span> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('mua.bookings.index') }}"
                       class="nav-link {{ request()->routeIs('mua.bookings.*') ? 'active' : '' }}">
                        <span class="nav-icon">📋</span> Kelola Booking
                        @php $pendingCount = Auth::user()->mua?->bookings()->where('status','pending')->count() ?? 0; @endphp
                        @if($pendingCount > 0)
                            <span class="nav-badge">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">🖼️</span> Portfolio
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">📱</span> Verifikasi QR
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">⭐</span> Ulasan
                    </a>
                </li>
            </ul>

            <p class="nav-section-label">Akun</p>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span class="nav-icon">👤</span> Profil Saya
                    </a>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('mua.logout') }}" id="sidebar-logout-form">@csrf</form>
                    <a href="#" class="nav-link"
                       onclick="event.preventDefault(); document.getElementById('sidebar-logout-form').submit();">
                        <span class="nav-icon">🚪</span> Logout
                    </a>
                </li>
            </ul>
        </nav>

        <div class="user-profile">
            <div class="user-avatar">{{ substr(Auth::user()->name ?? 'M', 0, 1) }}</div>
            <div>
                <div class="user-name">{{ Auth::user()->name ?? 'MUA' }}</div>
                <div class="user-role">{{ ucfirst(Auth::user()->role ?? 'mua') }} Professional</div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
