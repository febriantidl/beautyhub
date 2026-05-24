<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BeautyHub MUA — @yield('title', 'Dashboard')</title>

    <style>

        :root{
            --maroon: #4f0404;
            --gold: #C9A56E;
            --bg: #FAFAFA;
            --text: #1F1F1F;
            --muted: #8A8A8A;
            --border: #ECECEC;
        }

        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body{
            font-family:
            -apple-system,
            BlinkMacSystemFont,
            "SF Pro Display",
            "SF Pro Text",
            "Helvetica Neue",
            sans-serif;

            background: var(--bg);
            color: var(--text);
        }

        /* ================================
           SIDEBAR
        ================================= */

        .sidebar{
            position: fixed;
            top: 0;
            left: 0;
            width: 268px;
            height: 100vh;

            background: var(--maroon);

            padding: 1.7rem 1.2rem;

            display: flex;
            flex-direction: column;

            overflow-y: auto;
            z-index: 100;
        }

        .logo{
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.03em;

            color: white;

            margin-bottom: 2rem;
            padding-left: .35rem;

            font-family: "Playfair Display", serif;
        }

        .logo-accent{
            color: var(--gold);
            font-family: "Playfair Display", serif;
        }

        .nav-section-label{
            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;

            color: rgba(255,255,255,.55);

            margin: 1.2rem 0 .55rem;
            padding-left: .4rem;
        }

        .nav-menu{
            list-style: none;
        }

        .nav-item{
            margin-bottom: .35rem;
        }

        .nav-link{
            display: flex;
            align-items: center;
            gap: .75rem;

            padding: .82rem .95rem;

            border-radius: 16px;

            color: rgba(255,255,255,.92);
            text-decoration: none;

            font-size: .92rem;
            font-weight: 500;

            transition: .2s ease;
        }

        .nav-link:hover{
            background: rgba(255,255,255,.10);
            transform: translateX(2px);
        }

        .sidebar .nav-link.active{
            background: white !important;
            color: var(--maroon) !important;
            font-weight: 700;

            box-shadow:
                0 10px 30px rgba(0,0,0,.10);
        }

        .sidebar .nav-link.active svg{
            stroke: var(--maroon) !important;
        }

        .nav-icon{
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }

        .divider{
            height: 1px;
            background: rgba(255,255,255,.12);
            margin: 1rem 0;
        }

        /* ================================
           USER PROFILE
        ================================= */

        .sidebar-profile{
    margin-top: auto;

    background:
        linear-gradient(
            135deg,
            rgba(255,255,255,.10),
            rgba(255,255,255,.04)
        );

    border: 1px solid rgba(255,255,255,.10);

    border-radius: 20px;

    padding: .75rem .8rem;

    display: flex;
    align-items: center;
    gap: .7rem;

    box-shadow:
        0 8px 24px rgba(0,0,0,.12);

    backdrop-filter: blur(10px);
}

.sidebar-profile-avatar{
    width: 42px;
    height: 42px;

    min-width: 42px;

    border-radius: 14px;

    overflow: hidden;

    background:
        linear-gradient(
            135deg,
            var(--gold),
            #d8b684
        );

    display: flex;
    align-items: center;
    justify-content: center;

    color: white;
    font-weight: 700;
    font-size: .9rem;

    border: 2px solid rgba(255,255,255,.12);
}

.sidebar-profile-avatar img{
    width: 100%;
    height: 100%;

    object-fit: cover;
    object-position: center;

    display: block;
}

.sidebar-profile-content{
    flex: 1;
    min-width: 0;
}

.sidebar-profile-name{
    color: white;

    font-size: .84rem;
    font-weight: 650;

    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;

    line-height: 1.2;
}

.sidebar-profile-role{
    color: rgba(255,255,255,.62);

    font-size: .68rem;

    margin-top: 2px;

    letter-spacing: .02em;
}
        

        /* ================================
           MAIN CONTENT
        ================================= */

        .main-content{
            margin-left: 268px;

            min-height: 100vh;

            padding: 2.2rem;

            background: var(--bg);
        }

        /* ================================
           GLOBAL COMPONENTS
        ================================= */

        .card{
            background: white;

            border-radius: 26px;

            border: 1px solid var(--border);

            padding: 1.4rem;

            box-shadow:
                0 4px 24px rgba(0,0,0,.03);
        }

        .btn{
            border: none;
            outline: none;
            cursor: pointer;

            border-radius: 14px;

            padding: .75rem 1rem;

            font-size: .88rem;
            font-weight: 600;

            text-decoration: none;

            transition: .2s ease;
        }

        .btn:hover{
            transform: translateY(-1px);
        }

        .btn-primary{
            background: var(--maroon);
            color: white;
        }

        .btn-outline{
            border: 1px solid #E8E8E8;
            background: white;
            color: var(--maroon);
        }

        .btn-outline:hover{
            background: #FAFAFA;
        }

        /* ================================
           STATS
        ================================= */

        .stats-grid{
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(220px,1fr));
            gap: 1.2rem;

            margin-bottom: 1.7rem;
        }

        .stat-card{
            background: white;

            border-radius: 24px;

            padding: 1.4rem;

            border: 1px solid var(--border);

            box-shadow:
                0 4px 20px rgba(0,0,0,.03);

            transition: .2s ease;
        }

        .stat-card:hover{
            transform: translateY(-2px);
        }

        .stat-icon{
            width: 48px;
            height: 48px;

            border-radius: 16px;

            background: #F6F2ED;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 1rem;

            font-size: 1.2rem;
        }

        .stat-value{
            font-size: 2rem;
            font-weight: 700;

            letter-spacing: -0.04em;

            color: var(--text);

            margin-bottom: .2rem;
        }

        .stat-label{
            font-size: .88rem;
            color: var(--muted);
        }

        /* ================================
           BOOKING
        ================================= */

        .booking-item{
            display: flex;
            align-items: center;
            gap: .9rem;

            background: #FAFAFA;

            padding: .95rem;

            border-radius: 18px;

            margin-bottom: .75rem;
        }

        .booking-avatar{
            width: 48px;
            height: 48px;

            border-radius: 15px;

            background: var(--gold);

            display: flex;
            align-items: center;
            justify-content: center;

            color: white;
            font-weight: 700;
        }

        .booking-info{
            flex: 1;
        }

        .booking-name{
            font-size: .92rem;
            font-weight: 700;
            color: #1F1F1F;
        }

        .booking-date{
            font-size: .78rem;
            color: #888;
            margin-top: 3px;
        }

        /* ================================
           BADGES
        ================================= */

        .badge{
            display: inline-flex;
            align-items: center;
            justify-content: center;

            padding: .42rem .7rem;

            border-radius: 999px;

            font-size: .68rem;
            font-weight: 700;
            letter-spacing: .04em;

            text-transform: uppercase;
        }

        .badge-pending{
            background: #FFF3D8;
            color: #B77A00;
        }

        .badge-approved{
            background: #E8F7EC;
            color: #2F8A52;
        }

        .badge-completed{
            background: #EAF1FF;
            color: #2D67D9;
        }

        .badge-rejected{
            background: #FFEAEA;
            color: #C24B4B;
        }

        /* ================================
           UTILITIES
        ================================= */

        .text-center{
            text-align: center;
        }

        .text-gray{
            color: #8B8B8B;
        }

        .mb-8{
            margin-bottom: 2rem;
        }

        .mb-6{
            margin-bottom: 1.5rem;
        }

        .text-sm{
            font-size: .88rem;
        }

        .text-xs{
            font-size: .75rem;
        }

        /* ================================
           RESPONSIVE
        ================================= */

        @media(max-width: 992px){

            .sidebar{
                width: 100%;
                height: auto;
                position: relative;
            }

            .main-content{
                margin-left: 0;
                padding: 1.2rem;
            }
        }

    </style>

    @stack('styles')
</head>

<body>

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <a href="{{ route('mua.dashboard') }}"
           style="text-decoration:none;">

            <div class="logo">
                Beauty<span class="logo-accent">Hub</span>
            </div>

        </a>

        <!-- MENU -->
        <p class="nav-section-label">
            Menu Utama
        </p>

        <ul class="nav-menu">

            <li class="nav-item">

                <a href="{{ route('mua.dashboard') }}"
                   class="nav-link {{ request()->routeIs('mua.dashboard') ? 'active' : '' }}">

                    <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                    </svg>

                    Dashboard

                </a>

            </li>

            <li class="nav-item">

                <a href="{{ route('mua.bookings.index') }}"
                   class="nav-link {{ request()->routeIs('mua.bookings.*') ? 'active' : '' }}">

                    <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 4H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2z"></path>
                        <path d="M16 2v4M8 2v4M3 10h18"></path>
                    </svg>

                    Kelola Booking

                </a>

            </li>

            <li class="nav-item">

                <a href="{{ route('mua.portfolio.index') }}"
                   class="nav-link {{ request()->routeIs('mua.portfolio.*') ? 'active' : '' }}">

                    <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <path d="M21 15l-5-5L5 21"></path>
                    </svg>

                    Portfolio

                </a>

            </li>

            <li class="nav-item">

                <a href="{{ route('mua.services.index') }}"
                   class="nav-link {{ request()->routeIs('mua.services.*') ? 'active' : '' }}">

                    <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                    </svg>

                    Kelola Layanan

                </a>

            </li>

            <li class="nav-item">

                <a href="{{ route('mua.verification') }}"
                   class="nav-link {{ request()->routeIs('mua.verification') ? 'active' : '' }}">

                    <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"></path>
                    </svg>

                    Verifikasi QR

                </a>

            </li>

        </ul>

        <div class="divider"></div>

        <!-- ACCOUNT -->
        <p class="nav-section-label">
            Akun
        </p>

        <ul class="nav-menu">

            <li class="nav-item">

                <a href="{{ route('mua.profile') }}"
   class="nav-link {{ request()->routeIs('mua.profile*') ? 'active' : '' }}">

    <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M20 21a8 8 0 0 0-16 0"></path>
        <circle cx="12" cy="7" r="4"></circle>
    </svg>

    Profil Saya

</a>

            </li>

            <li class="nav-item">

                <form method="POST"
                      action="{{ route('mua.logout') }}"
                      id="sf-logout">

                    @csrf

                </form>

                <a href="#"
   class="nav-link"
   onclick="openLogoutModal(event)">

    <svg class="nav-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
        <polyline points="16 17 21 12 16 7"></polyline>
        <line x1="21" y1="12" x2="9" y2="12"></line>
    </svg>

    Logout

</a>

            </li>

        </ul>

        <!-- USER -->
        <div class="sidebar-profile">

    <div class="sidebar-profile-avatar">

        @if(Auth::user()->avatar)

            <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                 alt="Profile">

        @else

            {{ strtoupper(substr(Auth::user()->name ?? 'M', 0, 1)) }}

        @endif

    </div>

    <div class="sidebar-profile-content">

        <div class="sidebar-profile-name">
            {{ Auth::user()->name }}
        </div>

        <div class="sidebar-profile-role">
            {{ ucfirst(Auth::user()->role ?? 'mua') }}
        </div>

    </div>

    </aside>

    <!-- MAIN -->
    <main class="main-content">

    

    @yield('content')

</main>

    @stack('scripts')

    {{-- LOGOUT MODAL --}}
<div id="logoutModal" style="
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 9999;
">

    <div style="
        width: 100%;
        max-width: 380px;
        background: white;
        border-radius: 28px;
        padding: 2rem;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,.18);
        animation: popupFade .18s ease;
    ">

        <div style="
            width: 74px;
            height: 74px;
            margin: 0 auto 1.2rem;
            border-radius: 24px;
            background: rgba(79,4,4,.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        ">
            🚪
        </div>

        <h3 style="
            font-size: 1.3rem;
            font-weight: 700;
            color: #1F1F1F;
            margin-bottom: .55rem;
        ">
            Logout akun?
        </h3>

        <p style="
            color: #8A8A8A;
            font-size: .92rem;
            line-height: 1.6;
            margin-bottom: 1.8rem;
        ">
            Kamu yakin ingin keluar dari akun BeautyHub?
        </p>

        <div style="
            display: flex;
            gap: .75rem;
        ">

            <button
                onclick="closeLogoutModal()"
                style="
                    flex:1;
                    border:none;
                    background:#F5F5F5;
                    color:#444;
                    padding:.9rem;
                    border-radius:16px;
                    font-weight:600;
                    cursor:pointer;
                ">
                Batal
            </button>

            <button
                onclick="submitLogout()"
                style="
                    flex:1;
                    border:none;
                    background:#4f0404;
                    color:white;
                    padding:.9rem;
                    border-radius:16px;
                    font-weight:600;
                    cursor:pointer;
                ">
                Logout
            </button>

        </div>

    </div>
</div>

<style>
@keyframes popupFade{
    from{
        opacity:0;
        transform:scale(.95);
    }
    to{
        opacity:1;
        transform:scale(1);
    }
}
</style>

<script>
function openLogoutModal(event){
    event.preventDefault();
    document.getElementById('logoutModal').style.display = 'flex';
}

function closeLogoutModal(){
    document.getElementById('logoutModal').style.display = 'none';
}

function submitLogout(){
    document.getElementById('sf-logout').submit();
}
</script>
</body>
</html>