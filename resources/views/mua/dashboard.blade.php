@extends('layouts.mua')
@section('title', 'Dashboard')

@section('content')
<header class="mb-8" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="text-4xl font-bold mb-2">Selamat Datang, {{ explode(' ', Auth::user()->name)[0] }}! 👋</h1>
        <p class="text-gray">{{ now()->isoFormat('dddd, D MMMM Y') }} · Berikut ringkasan bisnis Anda</p>
    </div>
    @if(Auth::user()->mua?->is_verified)
    <span style="background: rgba(127,168,127,0.1); color: #3E7A3E; padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(127,168,127,0.3);">
        ✅ MUA Terverifikasi
    </span>
    @endif
</header>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #ffe5e5, #ffd5d5);">📅</div>
        <div class="stat-value">{{ $pendingCount }}</div>
        <div class="stat-label">Pending Booking</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fff4e5, #ffe5c4);">🎨</div>
        <div class="stat-value">{{ $portfolioCount }}</div>
        <div class="stat-label">Foto Portfolio</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #e5f4e5, #d4e8d4);">⭐</div>
        <div class="stat-value">{{ number_format($rating, 1) }}</div>
        <div class="stat-label">Rating Rata-rata</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #e5e8ff, #d4daff);">💰</div>
        <div class="stat-value" style="font-size: 1.5rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-label">Total Pendapatan</div>
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
            <div class="booking-info" style="flex: 1; min-width: 0;">
                <div class="booking-name">{{ $booking->customer->name ?? 'N/A' }}</div>
                <div class="booking-date">
                    📅 {{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }}
                    @if($booking->time_slot) · ⏰ {{ $booking->time_slot }} @endif
                </div>
                <span class="badge badge-{{ $booking->status }}">{{ strtoupper($booking->status) }}</span>
            </div>
            <div class="flex gap-2">
                @if($booking->status === 'pending')
                <form action="{{ route('mua.bookings.approve', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">Terima</button>
                </form>
                @endif
                <a href="{{ route('mua.bookings.show', $booking->id) }}" class="btn btn-outline btn-sm">Detail</a>
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 2.5rem; color: #9B8F8E;">
            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📭</div>
            <p>Tidak ada booking saat ini</p>
        </div>
        @endforelse
    </div>

    {{-- Quick Actions --}}
    <div class="card" style="margin-top: 0;">
        <h2 class="card-title" style="margin-bottom: 1.25rem;">⚡ Aksi Cepat</h2>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <a href="{{ route('mua.bookings.index', ['status' => 'pending']) }}"
               style="display: flex; align-items: center; gap: 0.875rem; padding: 1rem; background: rgba(212,165,110,0.07); border-radius: 14px; text-decoration: none; border: 1px solid rgba(212,165,110,0.2); transition: all 0.2s;"
               onmouseover="this.style.background='rgba(212,165,110,0.12)'" onmouseout="this.style.background='rgba(212,165,110,0.07)'">
                <span style="font-size: 1.5rem;">⏳</span>
                <div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: #2C2423;">Review Pending Booking</div>
                    <div style="font-size: 0.78rem; color: #6B6160;">{{ $pendingCount }} booking menunggu konfirmasi</div>
                </div>
                <span style="margin-left: auto; color: #9B8F8E; font-size: 0.9rem;">→</span>
            </a>
            <a href="{{ route('mua.portfolio.index') }}"
               style="display: flex; align-items: center; gap: 0.875rem; padding: 1rem; background: rgba(212,165,165,0.07); border-radius: 14px; text-decoration: none; border: 1px solid rgba(212,165,165,0.2); transition: all 0.2s;"
               onmouseover="this.style.background='rgba(212,165,165,0.12)'" onmouseout="this.style.background='rgba(212,165,165,0.07)'">
                <span style="font-size: 1.5rem;">📸</span>
                <div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: #2C2423;">Upload Portfolio</div>
                    <div style="font-size: 0.78rem; color: #6B6160;">Tambah foto karya terbaru</div>
                </div>
                <span style="margin-left: auto; color: #9B8F8E; font-size: 0.9rem;">→</span>
            </a>
            <a href="{{ route('mua.verification') }}"
               style="display: flex; align-items: center; gap: 0.875rem; padding: 1rem; background: rgba(127,168,127,0.07); border-radius: 14px; text-decoration: none; border: 1px solid rgba(127,168,127,0.2); transition: all 0.2s;"
               onmouseover="this.style.background='rgba(127,168,127,0.12)'" onmouseout="this.style.background='rgba(127,168,127,0.07)'">
                <span style="font-size: 1.5rem;">🔍</span>
                <div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: #2C2423;">Verifikasi QR</div>
                    <div style="font-size: 0.78rem; color: #6B6160;">Scan kode kedatangan pelanggan</div>
                </div>
                <span style="margin-left: auto; color: #9B8F8E; font-size: 0.9rem;">→</span>
            </a>
            <a href="{{ route('mua.services.index') }}"
               style="display: flex; align-items: center; gap: 0.875rem; padding: 1rem; background: rgba(110,157,183,0.07); border-radius: 14px; text-decoration: none; border: 1px solid rgba(110,157,183,0.2); transition: all 0.2s;"
               onmouseover="this.style.background='rgba(110,157,183,0.12)'" onmouseout="this.style.background='rgba(110,157,183,0.07)'">
                <span style="font-size: 1.5rem;">💄</span>
                <div>
                    <div style="font-weight: 700; font-size: 0.9rem; color: #2C2423;">Kelola Layanan</div>
                    <div style="font-size: 0.78rem; color: #6B6160;">Atur paket dan harga</div>
                </div>
                <span style="margin-left: auto; color: #9B8F8E; font-size: 0.9rem;">→</span>
            </a>
        </div>
    </div>
</div>
@endsection
