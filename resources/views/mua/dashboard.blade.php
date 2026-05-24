@extends('layouts.mua')
@section('title', 'Dashboard')

@section('content')
<header class="mb-8">
    <h1 class="text-4xl font-bold mb-2">Halo, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
    <p class="text-gray">Ringkasan bisnis Anda hari ini.</p>
</header>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-value">{{ $pendingCount }}</div>
        <div class="stat-label">Booking Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📅</div>
        <div class="stat-value">{{ $bookings->count() }}</div>
        <div class="stat-label">Jadwal Minggu Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">⭐</div>
        <div class="stat-value">{{ number_format(Auth::user()->mua->rating ?? 0, 1) }}</div>
        <div class="stat-label">Rating MUA</div>
    </div>
</div>

<div class="content-grid">
    {{-- Recent Bookings --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Booking Terbaru</h2>
            <a href="{{ route('mua.bookings.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
        </div>
        @forelse($recentBookings as $booking)
        <div class="booking-item">
            <div class="booking-avatar">{{ substr($booking->customer->name ?? '?', 0, 1) }}</div>
            <div class="booking-info">
                <div class="booking-name">{{ $booking->customer->name ?? 'N/A' }}</div>
                <div class="booking-date">📅 {{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }}</div>
            </div>
            <span class="badge badge-{{ $booking->status }}">{{ strtoupper($booking->status) }}</span>
        </div>
        @empty
        <p style="text-align: center; color: #9B8F8E;">Belum ada booking.</p>
        @endforelse
    </div>

    {{-- Jadwal MUA (Solid Design) --}}
    <div class="card">
        <h2 class="card-title" style="margin-bottom: 1rem; color: var(--maroon);">Jadwal MUA</h2>
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            @forelse($bookings as $b)
            <div style="padding: 0.75rem; border-left: 4px solid var(--gold); background: #FFF9E6; border-radius: 4px;">
                <div style="font-weight: 700; font-size: 0.85rem;">{{ \Carbon\Carbon::parse($b->event_date)->format('d M') }} - Booking #{{ $b->id }}</div>
            </div>
            @empty
            <p class="text-sm text-gray">Tidak ada jadwal terkonfirmasi.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection