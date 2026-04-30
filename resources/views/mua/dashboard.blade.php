@extends('layouts.mua')

@section('title', 'Dashboard')

@section('content')
<header class="mb-8">
    <h1 class="text-4xl font-bold mb-2">Selamat Datang Kembali! 👋</h1>
    <p class="text-gray">Berikut adalah ringkasan aktivitas bisnis Anda hari ini</p>
</header>

<div class="stats-grid" id="statsGrid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #ffe5e5, #ffd5d5);">📅</div>
        <div class="stat-value" id="statPending">{{ $pendingCount }}</div>
        <div class="stat-label">Pending Bookings</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fff4e5, #ffe5c4);">🎨</div>
        <div class="stat-value" id="statPortfolio">{{ $portfolioCount }}</div>
        <div class="stat-label">Portfolio Items</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #e5f4e5, #d4e8d4);">⭐</div>
        <div class="stat-value" id="statRating">{{ number_format($rating, 1) }}</div>
        <div class="stat-label">Average Rating</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #e5e8ff, #d4daff);">💰</div>
        <div class="stat-value" id="statRevenue">{{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
</div>

<div class="content-grid">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Booking Requests Terbaru</h2>
            <a href="{{ route('mua.bookings') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        <div id="bookingsList">
            @forelse($recentBookings as $booking)
            <div class="booking-item">
                <div class="booking-avatar">{{ substr($booking->customer->name, 0, 1) }}</div>
                <div class="booking-info">
                    <div class="booking-name">{{ $booking->customer->name }}</div>
                    <div class="booking-date">📅 {{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }} - {{ $booking->time_slot }}</div>
                    <span class="badge badge-{{ $booking->status }}">{{ strtoupper($booking->status) }}</span>
                </div>
                <div class="flex gap-2">
                    <form action="{{ route('mua.bookings.approve', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Terima</button>
                    </form>
                    <form action="{{ route('mua.bookings.reject', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline btn-sm">Tolak</button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-center text-gray p-8">Tidak ada booking request saat ini</p>
            @endforelse
        </div>
    </div>
</div>
@endsection