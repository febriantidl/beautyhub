@extends('layouts.mua')

@section('title', 'Detail Booking #' . $booking->id)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('mua.bookings.index') }}" style="color: var(--primary-deep); text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem;">
        ← Kembali ke Daftar Booking
    </a>
</div>

<header class="mb-8" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="text-4xl font-bold mb-2">Detail Booking #{{ $booking->id }}</h1>
        <p class="text-gray">Dibuat pada {{ $booking->created_at->format('d M Y, H:i') }}</p>
    </div>
    @php
    $badgeColors = [
        'pending'   => 'background: rgba(212,165,110,0.15); color: #B88A40;',
        'approved'  => 'background: rgba(127,168,127,0.15); color: #3E7A3E;',
        'rejected'  => 'background: rgba(183,110,110,0.15); color: #A04040;',
        'completed' => 'background: rgba(110,157,183,0.15); color: #2E6B8A;',
        'cancelled' => 'background: rgba(150,150,150,0.15); color: #555;',
        'verified'  => 'background: rgba(80,160,200,0.15); color: #2A7A9E;',
    ];
    @endphp
    <span style="padding: 0.5rem 1.5rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 700; {{ $badgeColors[$booking->status] ?? '' }}">
        {{ strtoupper($booking->status) }}
    </span>
</header>

{{-- Flash --}}
@if(session('success'))
<div style="background: var(--success-bg); border: 1px solid var(--success); color: var(--success); padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
    ✅ {{ session('success') }}
</div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

    {{-- Customer Info --}}
    <div class="card">
        <h2 class="card-title" style="margin-bottom: 1rem; font-size: 1.15rem;">👤 Informasi Pelanggan</h2>
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div style="width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #C9A56E, #D4A5A5); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700; color: white;">
                {{ substr($booking->customer->name ?? '?', 0, 1) }}
            </div>
            <div>
                <div style="font-weight: 700; font-size: 1.1rem;">{{ $booking->customer->name }}</div>
                <div style="color: var(--text-gray); font-size: 0.9rem;">{{ $booking->customer->email }}</div>
                <div style="color: var(--text-gray); font-size: 0.9rem;">{{ $booking->customer->phone ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Event Info --}}
    <div class="card">
        <h2 class="card-title" style="margin-bottom: 1rem; font-size: 1.15rem;">📅 Detail Acara</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr><td style="padding: 0.4rem 0; color: var(--text-gray); font-size: 0.875rem; width: 45%;">Tanggal Acara</td>
                <td style="font-weight: 600;">{{ $booking->event_date->format('d M Y') }}</td></tr>
            <tr><td style="padding: 0.4rem 0; color: var(--text-gray); font-size: 0.875rem;">Waktu</td>
                <td style="font-weight: 600;">{{ $booking->time_slot ?? '-' }}</td></tr>
            <tr><td style="padding: 0.4rem 0; color: var(--text-gray); font-size: 0.875rem;">Lokasi</td>
                <td style="font-weight: 600;">{{ $booking->location_address }}</td></tr>
            @if($booking->location_notes)
            <tr><td style="padding: 0.4rem 0; color: var(--text-gray); font-size: 0.875rem;">Catatan Lokasi</td>
                <td>{{ $booking->location_notes }}</td></tr>
            @endif
            <tr><td style="padding: 0.4rem 0; color: var(--text-gray); font-size: 0.875rem;">Layanan</td>
                <td style="font-weight: 600;">{{ $booking->service->name ?? 'Custom' }}</td></tr>
            <tr><td style="padding: 0.4rem 0; color: var(--text-gray); font-size: 0.875rem;">Harga</td>
                <td style="font-weight: 700; color: var(--primary-deep);">Rp {{ number_format($booking->price, 0, ',', '.') }}</td></tr>
        </table>
    </div>
</div>

{{-- Notes & Reference --}}
@if($booking->notes || $booking->reference_image)
<div class="card" style="margin-top: 1.5rem;">
    <h2 class="card-title" style="margin-bottom: 1rem; font-size: 1.15rem;">📝 Catatan & Referensi</h2>
    <div style="display: grid; grid-template-columns: {{ $booking->reference_image ? '1fr 1fr' : '1fr' }}; gap: 1.5rem;">
        @if($booking->notes)
        <div>
            <p style="font-size: 0.875rem; color: var(--text-gray); margin-bottom: 0.5rem;">Catatan dari pelanggan:</p>
            <p style="line-height: 1.6;">{{ $booking->notes }}</p>
        </div>
        @endif
        @if($booking->reference_image)
        <div>
            <p style="font-size: 0.875rem; color: var(--text-gray); margin-bottom: 0.5rem;">Gambar referensi:</p>
            <img src="{{ asset('storage/' . $booking->reference_image) }}" alt="Reference"
                 style="width: 100%; max-width: 300px; border-radius: 16px; border: 1px solid var(--border-soft);">
        </div>
        @endif
    </div>
</div>
@endif

{{-- Verification Code --}}
@if($booking->status === 'approved' && $booking->verification_code)
<div class="card" style="margin-top: 1.5rem; border: 2px solid var(--accent-gold); background: linear-gradient(135deg, rgba(201,165,110,0.05), rgba(212,165,165,0.05));">
    <h2 class="card-title" style="margin-bottom: 1rem; font-size: 1.15rem;">🔑 Kode Verifikasi</h2>
    <p style="font-size: 0.875rem; color: var(--text-gray); margin-bottom: 1rem;">Minta pelanggan menunjukkan kode ini saat hari-H untuk verifikasi kedatangan:</p>
    <div style="font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 700; letter-spacing: 0.5rem; color: var(--primary-deep); text-align: center; padding: 1rem; background: white; border-radius: 12px; border: 1px solid var(--border-soft);">
        {{ $booking->verification_code }}
    </div>
</div>
@endif

{{-- Review --}}
@if($booking->review)
<div class="card" style="margin-top: 1.5rem;">
    <h2 class="card-title" style="margin-bottom: 1rem; font-size: 1.15rem;">⭐ Ulasan Pelanggan</h2>
    <div style="display: flex; gap: 0.25rem; margin-bottom: 0.75rem;">
        @for($i = 1; $i <= 5; $i++)
            <span style="font-size: 1.25rem; color: {{ $i <= $booking->review->rating ? '#C9A56E' : '#E8E2DD' }};">★</span>
        @endfor
        <span style="font-weight: 700; margin-left: 0.5rem; color: var(--text-dark);">{{ $booking->review->rating }}/5</span>
    </div>
    @if($booking->review->comment)
    <p style="color: var(--text-gray); line-height: 1.6; font-style: italic;">"{{ $booking->review->comment }}"</p>
    @endif
</div>
@endif

{{-- Action Buttons --}}
<div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
    @if($booking->status === 'pending')
    <form action="{{ route('mua.bookings.approve', $booking->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success" style="padding: 0.75rem 2rem; font-size: 1rem;">✅ Terima Booking</button>
    </form>
    <button onclick="document.getElementById('rejectModalInline').style.display='flex'" class="btn" style="padding: 0.75rem 2rem; font-size: 1rem; background: rgba(183,110,110,0.1); color: #B76E6E; border: 1px solid #D49A9A;">❌ Tolak Booking</button>
    @endif

    @if($booking->status === 'verified')
    <form action="{{ route('mua.bookings.complete', $booking->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn" style="padding: 0.75rem 2rem; font-size: 1rem; background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white;">🎉 Tandai Selesai</button>
    </form>
    @endif
</div>

{{-- Inline Reject Modal --}}
<div id="rejectModalInline" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 50; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 20px; padding: 2rem; width: 100%; max-width: 440px; margin: 1rem;">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">Tolak Booking #{{ $booking->id }}</h3>
        <form action="{{ route('mua.bookings.reject', $booking->id) }}" method="POST">
            @csrf
            <textarea name="rejection_reason" rows="3"
                style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-soft); border-radius: 12px; font-family: inherit; resize: vertical; margin-bottom: 1rem;"
                placeholder="Alasan penolakan (opsional)..."></textarea>
            <div style="display: flex; gap: 0.75rem;">
                <button type="button" onclick="document.getElementById('rejectModalInline').style.display='none'" class="btn btn-outline" style="flex: 1;">Batal</button>
                <button type="submit" class="btn" style="flex: 1; background: linear-gradient(135deg, #D4A5A5, #9B6B6B); color: white;">Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection
