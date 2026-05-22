<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BeautyHub Admin — @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5F7FA; color: #1E2532; }

        /* ── Sidebar ─────────────────────────────────── */
        .sidebar { position: fixed; left: 0; top: 0; width: 260px; height: 100vh; background: #1E2532; padding: 1.75rem 1.25rem 1.25rem; display: flex; flex-direction: column; z-index: 100; overflow-y: auto; }
        .logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: #fff; margin-bottom: 0.25rem; }
        .logo-accent { color: #C9A56E; }
        .logo-badge { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; background: rgba(201,165,110,0.2); color: #C9A56E; padding: 0.15rem 0.5rem; border-radius: 6px; display: inline-block; margin-bottom: 2rem; }

        .nav-section-label { font-size: 0.65rem; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #5A6278; padding: 0 0.5rem; margin: 1.25rem 0 0.4rem; }
        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.2rem; }
        .nav-link { display: flex; align-items: center; gap: 0.7rem; padding: 0.65rem 0.875rem; color: #8A93A8; text-decoration: none; border-radius: 10px; transition: all 0.18s; font-weight: 500; font-size: 0.875rem; }
        .nav-link:hover { background: rgba(255,255,255,0.06); color: #fff; }
        .nav-link.active { background: linear-gradient(135deg, #C9A56E, #A07840); color: white; box-shadow: 0 4px 12px rgba(201,165,110,0.3); }
        .nav-icon { font-size: 0.95rem; width: 18px; text-align: center; flex-shrink: 0; }
        .nav-badge { margin-left: auto; background: #C9A56E; color: white; border-radius: 9999px; padding: 0.1rem 0.45rem; font-size: 0.68rem; font-weight: 700; }
        .nav-link.active .nav-badge { background: rgba(255,255,255,0.25); }

        .divider { height: 1px; background: rgba(255,255,255,0.07); margin: 0.875rem 0.5rem; }

        .user-profile { padding: 0.875rem; background: rgba(255,255,255,0.05); border-radius: 12px; display: flex; align-items: center; gap: 0.7rem; border: 1px solid rgba(255,255,255,0.08); margin-top: auto; }
        .user-avatar { width: 38px; height: 38px; border-radius: 10px; background: linear-gradient(135deg, #C9A56E, #9B6B6B); display: flex; align-items: center; justify-content: center; font-weight: 700; color: white; font-size: 15px; flex-shrink: 0; }
        .user-name { font-weight: 700; font-size: 0.8rem; color: #fff; }
        .user-role { font-size: 0.7rem; color: #8A93A8; }

        /* ── Main ────────────────────────────────────── */
        .main-content { margin-left: 260px; padding: 2rem 2.25rem; min-height: 100vh; }

        /* ── Stats ───────────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.2rem; margin-bottom: 2rem; }
        .stat-card { background: white; padding: 1.4rem; border-radius: 16px; border: 1px solid #EEF0F5; transition: box-shadow 0.2s; }
        .stat-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,0.06); }
        .stat-icon { width: 44px; height: 44px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 0.875rem; }
        .stat-value { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; margin-bottom: 0.2rem; color: #1E2532; }
        .stat-label { color: #7A8396; font-size: 0.8rem; }

        /* ── Card ────────────────────────────────────── */
        .card { background: white; border-radius: 16px; padding: 1.5rem; border: 1px solid #EEF0F5; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .card-title { font-size: 1.1rem; font-weight: 700; color: #1E2532; }

        /* ── Table ───────────────────────────────────── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead th { padding: 0.75rem 1rem; text-align: left; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #7A8396; border-bottom: 1px solid #EEF0F5; }
        tbody td { padding: 0.9rem 1rem; border-bottom: 1px solid #F5F7FA; color: #2E3545; vertical-align: middle; }
        tbody tr:hover td { background: #FAFBFD; }
        tbody tr:last-child td { border-bottom: none; }

        /* ── Buttons ─────────────────────────────────── */
        .btn { padding: 0.5rem 1.25rem; border-radius: 10px; border: none; cursor: pointer; font-weight: 600; font-size: 0.825rem; font-family: inherit; transition: all 0.18s; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; }
        .btn:hover { transform: translateY(-1px); }
        .btn-primary { background: linear-gradient(135deg, #C9A56E, #A07840); color: white; box-shadow: 0 3px 10px rgba(160,120,64,0.25); }
        .btn-outline { background: transparent; border: 1px solid #D8DCE8; color: #5A6278; }
        .btn-outline:hover { background: #F5F7FA; }
        .btn-danger { background: rgba(220,53,69,0.08); color: #C0392B; border: 1px solid rgba(220,53,69,0.2); }
        .btn-success { background: rgba(40,167,69,0.08); color: #1E7E34; border: 1px solid rgba(40,167,69,0.2); }
        .btn-sm { padding: 0.3rem 0.75rem; font-size: 0.775rem; border-radius: 8px; }

        /* ── Badges ──────────────────────────────────── */
        .badge { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; }
        .badge-admin    { background: rgba(201,165,110,0.12); color: #8A6020; }
        .badge-mua      { background: rgba(155,107,107,0.12); color: #7A3A3A; }
        .badge-customer { background: rgba(90,100,140,0.1); color: #3A4070; }
        .badge-active   { background: rgba(40,167,69,0.1);  color: #1E7E34; }
        .badge-inactive { background: rgba(220,53,69,0.1);  color: #C0392B; }
        .badge-verified { background: rgba(40,167,69,0.1);  color: #1E7E34; }
        .badge-unverified { background: rgba(255,193,7,0.1); color: #856404; }
        .badge-pending   { background: rgba(255,193,7,0.1); color: #856404; }
        .badge-approved  { background: rgba(40,167,69,0.1); color: #1E7E34; }
        .badge-completed { background: rgba(13,110,253,0.1);color: #0A58CA; }
        .badge-rejected  { background: rgba(220,53,69,0.1); color: #C0392B; }

        /* ── Search bar ──────────────────────────────── */
        .search-bar { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.25rem; align-items: center; }
        .search-input { padding: 0.6rem 1rem; border: 1px solid #D8DCE8; border-radius: 10px; font-family: inherit; font-size: 0.875rem; outline: none; min-width: 220px; }
        .search-input:focus { border-color: #C9A56E; box-shadow: 0 0 0 3px rgba(201,165,110,0.12); }

        /* ── Alert ───────────────────────────────────── */
        .alert { padding: 0.875rem 1rem; border-radius: 12px; margin-bottom: 1.25rem; font-size: 0.875rem; }
        .alert-success { background: rgba(40,167,69,0.07); border: 1px solid rgba(40,167,69,0.25); color: #1E7E34; }
        .alert-error   { background: rgba(220,53,69,0.07); border: 1px solid rgba(220,53,69,0.25); color: #C0392B; }

        /* ── Utilities ───────────────────────────────── */
        .flex { display: flex; } .gap-2 { gap: 0.5rem; } .items-center { align-items: center; }
        .text-gray { color: #7A8396; } .font-bold { font-weight: 700; }
        .mb-8 { margin-bottom: 2rem; } .mb-4 { margin-bottom: 1rem; }
        .text-4xl { font-size: 1.875rem; font-family: 'Playfair Display', serif; }
        .font-bold { font-weight: 700; }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="sidebar">
        <a href="{{ route('admin.dashboard') }}" style="text-decoration:none;">
            <div class="logo">Beauty<span class="logo-accent">Hub</span></div>
        </a>
        <span class="logo-badge">Admin Panel</span>

        <p class="nav-section-label">Overview</p>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
            </li>
        </ul>

        <p class="nav-section-label">Manajemen</p>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('admin.muas.index') }}" class="nav-link {{ request()->routeIs('admin.muas.*') ? 'active' : '' }}">
                    <span class="nav-icon">💄</span> Kelola MUA
                    @php $unverif = \App\Models\Mua::where('is_verified', false)->count(); @endphp
                    @if($unverif > 0)<span class="nav-badge">{{ $unverif }}</span>@endif
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <span class="nav-icon">👥</span> Kelola Pengguna
                </a>
            </li>
        </ul>

        <div class="divider"></div>
        <ul class="nav-menu">
            <li class="nav-item">
                <form method="POST" action="{{ route('mua.logout') }}" id="admin-logout">@csrf</form>
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('admin-logout').submit();">
                    <span class="nav-icon">🚪</span> Logout
                </a>
            </li>
        </ul>

        <div class="user-profile" style="margin-top:1.5rem;">
            <div class="user-avatar">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</div>
            <div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </aside>

    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error') || $errors->any())
            <div class="alert alert-error">⚠️ {{ session('error') ?? $errors->first() }}</div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
