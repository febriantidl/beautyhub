@extends('layouts.mua')
@section('title', 'Dashboard')

@section('content')

<style>
    *{
        font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display",
        "SF Pro Text", "Helvetica Neue", sans-serif;
    }

    .dashboard-header{
        margin-bottom: 2rem;
    }

    .dashboard-title{
        font-size: 2.2rem;
        font-weight: 700;
        color: #1f1f1f;
        letter-spacing: -0.03em;
        margin-bottom: 0.35rem;
    }

    .dashboard-subtitle{
        color: #8B8B8B;
        font-size: 0.96rem;
        font-weight: 400;
    }

    /* STATS */
    .stats-grid{
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.2rem;
        margin-bottom: 1.8rem;
    }

    .stat-card{
        background: #ffffff;
        border-radius: 24px;
        padding: 1.4rem;
        border: 1px solid #F1F1F1;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        transition: 0.2s ease;
    }

    .stat-card:hover{
        transform: translateY(-2px);
    }

    .stat-top{
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-icon{
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #F8F3ED;
        font-size: 1.2rem;
    }

    .stat-label{
        color: #8B8B8B;
        font-size: 0.88rem;
        font-weight: 500;
        margin-bottom: 0.45rem;
    }

    .stat-value{
        font-size: 2rem;
        font-weight: 700;
        color: #1E1E1E;
        letter-spacing: -0.04em;
    }

    /* CONTENT */
    .content-grid{
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 1.2rem;
    }

    .dashboard-card{
        background: #ffffff;
        border-radius: 24px;
        padding: 1.4rem;
        border: 1px solid #F1F1F1;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .card-header{
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.2rem;
    }

    .card-title{
        font-size: 1.05rem;
        font-weight: 700;
        color: #1E1E1E;
        letter-spacing: -0.02em;
    }

    .btn-view{
        padding: 0.55rem 0.9rem;
        border-radius: 12px;
        border: 1px solid #EAEAEA;
        text-decoration: none;
        color: #4f0404;
        font-size: 0.82rem;
        font-weight: 600;
        transition: 0.2s ease;
    }

    .btn-view:hover{
        background: #fafafa;
    }

    /* BOOKING ITEM */
    .booking-item{
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.9rem 0;
        border-bottom: 1px solid #F3F3F3;
    }

    .booking-item:last-child{
        border-bottom: none;
    }

    .booking-left{
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }

    .booking-avatar{
        width: 46px;
        height: 46px;
        border-radius: 16px;
        background: #4f0404;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
    }

    .booking-name{
        font-size: 0.94rem;
        font-weight: 600;
        color: #222;
        margin-bottom: 0.2rem;
    }

    .booking-date{
        font-size: 0.82rem;
        color: #8A8A8A;
    }

    /* BADGE */
    .badge{
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.03em;
    }

    .badge-pending{
        background: #FFF3D9;
        color: #B78103;
    }

    .badge-confirmed{
        background: #E7F8EC;
        color: #2F8F4E;
    }

    .badge-completed{
        background: #E8F1FF;
        color: #2D6CDF;
    }

    /* SCHEDULE */
    .schedule-list{
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .schedule-item{
        padding: 1rem;
        border-radius: 18px;
        background: #FAF7F2;
        border: 1px solid #F3ECE3;
    }

    .schedule-date{
        font-size: 0.82rem;
        color: #9B8F8E;
        margin-bottom: 0.3rem;
    }

    .schedule-title{
        font-size: 0.92rem;
        font-weight: 700;
        color: #2B2B2B;
    }

    .empty-state{
        text-align: center;
        color: #9B8F8E;
        font-size: 0.9rem;
        padding: 1rem 0;
    }

    /* MOBILE */
    @media(max-width: 992px){

        .stats-grid{
            grid-template-columns: 1fr;
        }

        .content-grid{
            grid-template-columns: 1fr;
        }

        .dashboard-title{
            font-size: 1.8rem;
        }
    }
</style>

<header class="dashboard-header">

    <h1 class="dashboard-title">
        Halo, {{ explode(' ', Auth::user()->name)[0] }} 👋
    </h1>

    <p class="dashboard-subtitle">
        Ringkasan aktivitas BeautyHub Anda hari ini.
    </p>

</header>

{{-- Stats --}}
<div class="stats-grid">

    <div class="stat-card">

        <div class="stat-top">
            <div class="stat-icon">⏳</div>
        </div>

        <div class="stat-label">
            Booking Pending
        </div>

        <div class="stat-value">
            {{ $pendingCount }}
        </div>

    </div>

    <div class="stat-card">

        <div class="stat-top">
            <div class="stat-icon">📅</div>
        </div>

        <div class="stat-label">
            Jadwal Minggu Ini
        </div>

        <div class="stat-value">
            {{ $bookings->count() }}
        </div>

    </div>

    <div class="stat-card">

        <div class="stat-top">
            <div class="stat-icon">⭐</div>
        </div>

        <div class="stat-label">
            Rating MUA
        </div>

        <div class="stat-value">
            {{ number_format(Auth::user()->mua->rating ?? 0, 1) }}
        </div>

    </div>

</div>

<div class="content-grid">

    {{-- Recent Bookings --}}
    <div class="dashboard-card">

        <div class="card-header">

            <h2 class="card-title">
                Booking Terbaru
            </h2>

            <a href="{{ route('mua.bookings.index') }}" class="btn-view">
                Lihat Semua
            </a>

        </div>

        @forelse($recentBookings as $booking)

        <div class="booking-item">

            <div class="booking-left">

                <div class="booking-avatar">
                    {{ substr($booking->customer->name ?? '?', 0, 1) }}
                </div>

                <div>

                    <div class="booking-name">
                        {{ $booking->customer->name ?? 'N/A' }}
                    </div>

                    <div class="booking-date">
                        📅 {{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }}
                    </div>

                </div>

            </div>

            <span class="badge badge-{{ $booking->status }}">
                {{ strtoupper($booking->status) }}
            </span>

        </div>

        @empty

        <p class="empty-state">
            Belum ada booking terbaru.
        </p>

        @endforelse

    </div>

    {{-- Schedule --}}
    <div class="dashboard-card">

        <div class="card-header">

            <h2 class="card-title">
                Jadwal MUA
            </h2>

        </div>

        <div class="schedule-list">

            @forelse($bookings as $b)

            <div class="schedule-item">

                <div class="schedule-date">
                    {{ \Carbon\Carbon::parse($b->event_date)->format('d M Y') }}
                </div>

                <div class="schedule-title">
                    Booking #{{ $b->id }}
                </div>

            </div>

            @empty

            <p class="empty-state">
                Tidak ada jadwal terkonfirmasi.
            </p>

            @endforelse

        </div>

    </div>

</div>

@endsection