@extends('layouts.mua')

@section('title', 'Kelola Booking')

@section('content')
<header class="mb-8">
    <h1 class="text-4xl font-bold mb-2">Kelola Booking</h1>
    <p class="text-gray">Kelola semua permintaan booking dari pelanggan Anda</p>
</header>

{{-- Status Counts --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 2rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #fff4e5, #ffe5c4);">⏳</div>
        <div class="stat-value">{{ $counts['pending'] }}</div>
        <div class="stat-label">Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #e5f4e5, #d4e8d4);">✅</div>
        <div class="stat-value">{{ $counts['approved'] }}</div>
        <div class="stat-label">Disetujui</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #e5e8ff, #d4daff);">🎉</div>
        <div class="stat-value">{{ $counts['completed'] }}</div>
        <div class="stat-label">Selesai</div>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('success'))
<div style="background: var(--success-bg); border: 1px solid var(--success); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
    ✅ {{ session('success') }}
</div>
@endif

{{-- Filter --}}
<div class="card mb-6" style="margin-bottom: 1.5rem;">
    <form method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <label style="font-weight: 600; color: var(--text-dark);">Filter Status:</label>
        @foreach(['all' => 'Semua', 'pending' => 'Pending', 'approved' => 'Disetujui', 'rejected' => 'Ditolak', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $label)
        <a href="{{ route('mua.bookings.index', ['status' => $val]) }}"
           style="padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; text-decoration: none;
                  {{ request('status', 'all') === $val ? 'background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white;' : 'border: 1px solid #E8E2DD; color: #6B6160;' }}">
            {{ $label }}
        </a>
        @endforeach
    </form>
</div>

{{-- Bookings List --}}
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Daftar Booking</h2>
        <span style="font-size: 0.875rem; color: var(--text-gray);">{{ $bookings->total() }} total</span>
    </div>

    @forelse($bookings as $booking)
    <div style="display: flex; align-items: center; gap: 1rem; padding: 1.25rem; background: var(--bg-cream); border-radius: 16px; margin-bottom: 0.75rem; flex-wrap: wrap;">
        <div style="width: 56px; height: 56px; border-radius: 16px; background: linear-gradient(135deg, #C9A56E, #D4A5A5); display: flex; align-items: center; justify-content: center; font-size: 22px; font-weight: 700; color: white; flex-shrink: 0;">
            {{ substr($booking->customer->name ?? '?', 0, 1) }}
        </div>
        <div style="flex: 1; min-width: 0;">
            <div style="font-weight: 700; font-size: 1rem; color: var(--text-dark);">{{ $booking->customer->name ?? 'N/A' }}</div>
            <div style="font-size: 0.85rem; color: var(--text-gray); margin-top: 2px;">
                📅 {{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }}
                @if($booking->time_slot) · ⏰ {{ $booking->time_slot }} @endif
                @if($booking->service) · 💄 {{ $booking->service->name }} @endif
            </div>
            <div style="font-size: 0.8rem; color: var(--text-gray); margin-top: 2px;">📍 {{ Str::limit($booking->location_address, 50) }}</div>
        </div>
        <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
            @php
            $badgeColors = [
                'pending'   => 'background: rgba(212,165,110,0.1); color: #D4A56E;',
                'approved'  => 'background: rgba(127,168,127,0.1); color: #5C8A5C;',
                'rejected'  => 'background: rgba(183,110,110,0.1); color: #B76E6E;',
                'completed' => 'background: rgba(110,157,183,0.1); color: #4A7D9B;',
                'cancelled' => 'background: rgba(150,150,150,0.1); color: #777;',
                'verified'  => 'background: rgba(80,160,200,0.1); color: #3A8FB5;',
            ];
            $badgeStyle = $badgeColors[$booking->status] ?? '';
            @endphp
            <span style="padding: 0.3rem 0.9rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; {{ $badgeStyle }}">
                {{ strtoupper($booking->status) }}
            </span>
            <a href="{{ route('mua.bookings.show', $booking->id) }}" class="btn btn-outline btn-sm">Detail</a>
            @if($booking->status === 'pending')
            <form action="{{ route('mua.bookings.approve', $booking->id) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-success btn-sm">Terima</button>
            </form>
            <button onclick="showRejectModal({{ $booking->id }})" class="btn btn-sm" style="background: rgba(183,110,110,0.1); color: #B76E6E; border: 1px solid #D49A9A;">Tolak</button>
            @endif
        </div>
    </div>
    @empty
    <div style="text-align: center; padding: 3rem; color: var(--text-gray);">
        <div style="font-size: 3rem; margin-bottom: 1rem;">📭</div>
        <p>Tidak ada booking pada filter ini.</p>
    </div>
    @endforelse

    {{-- Pagination --}}
    @if($bookings->hasPages())
    <div style="margin-top: 1.5rem;">{{ $bookings->appends(request()->query())->links() }}</div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 2rem; width: 100%; max-width: 440px; margin: 1rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">Tolak Booking</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; color: var(--text-dark);">Alasan penolakan (opsional)</label>
                <textarea name="rejection_reason" rows="3"
                    style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-soft); border-radius: 12px; font-family: inherit; resize: vertical;"
                    placeholder="Contoh: Jadwal sudah penuh pada tanggal tersebut..."></textarea>
            </div>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="closeRejectModal()" class="btn btn-outline" style="flex: 1;">Batal</button>
                <button type="submit" class="btn" style="flex: 1; background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white;">Tolak Booking</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showRejectModal(bookingId) {
    document.getElementById('rejectForm').action = `/mua/bookings/${bookingId}/reject`;
    document.getElementById('rejectModal').style.display = 'flex';
}
function closeRejectModal() {
    document.getElementById('rejectModal').style.display = 'none';
}
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
@endpush
