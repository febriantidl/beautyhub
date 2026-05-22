@extends('layouts.mua')
@section('title', 'Detail Booking #' . $booking->id)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('mua.bookings.index') }}" style="color: var(--primary-deep, #9B6B6B); text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; gap: 0.5rem;">
        ← Kembali
    </a>
</div>

<header class="mb-8" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="text-4xl font-bold mb-2">Booking #{{ $booking->id }}</h1>
        <p class="text-gray">Dibuat {{ $booking->created_at->format('d M Y, H:i') }}</p>
    </div>
    @php
    $bc = ['pending'=>'#9B7030','approved'=>'#2E6A2E','rejected'=>'#8A3030','completed'=>'#1E5A7A','cancelled'=>'#555','verified'=>'#1A6A8E'];
    $bg = ['pending'=>'rgba(212,165,110,.1)','approved'=>'rgba(100,160,100,.1)','rejected'=>'rgba(183,110,110,.1)','completed'=>'rgba(100,150,190,.1)','cancelled'=>'rgba(160,160,160,.1)','verified'=>'rgba(70,150,200,.1)'];
    @endphp
    <span style="padding:.5rem 1.5rem;border-radius:9999px;font-size:.875rem;font-weight:700;background:{{ $bg[$booking->status]??'#eee' }};color:{{ $bc[$booking->status]??'#333' }}">
        {{ strtoupper($booking->status) }}
    </span>
</header>

@if(session('success'))
<div style="background:rgba(100,160,100,.08);border:1px solid rgba(100,160,100,.3);color:#2E6A2E;padding:1rem;border-radius:12px;margin-bottom:1.5rem;">
    ✅ {{ session('success') }}
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    {{-- Customer --}}
    <div class="card">
        <h2 class="card-title" style="margin-bottom:1rem;font-size:1.1rem;">👤 Pelanggan</h2>
        <div style="display:flex;align-items:center;gap:1rem;">
            <div style="width:56px;height:56px;border-radius:14px;background:linear-gradient(135deg,#C9A56E,#D4A5A5);display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;color:white;flex-shrink:0;">
                {{ substr($booking->customer->name??'?',0,1) }}
            </div>
            <div>
                <div style="font-weight:700;font-size:1rem;">{{ $booking->customer->name??'N/A' }}</div>
                <div style="color:#7A6F6E;font-size:.875rem;">{{ $booking->customer->email }}</div>
                <div style="color:#7A6F6E;font-size:.875rem;">{{ $booking->customer->phone??'-' }}</div>
            </div>
        </div>
    </div>

    {{-- Event Detail --}}
    <div class="card">
        <h2 class="card-title" style="margin-bottom:1rem;font-size:1.1rem;">📅 Detail Acara</h2>
        <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
            <tr><td style="padding:.35rem 0;color:#7A6F6E;width:45%;">Tanggal Acara</td><td style="font-weight:700;">{{ $booking->event_date->format('d M Y') }}</td></tr>
            <tr><td style="padding:.35rem 0;color:#7A6F6E;">Waktu</td><td style="font-weight:600;">{{ $booking->time_slot??'-' }}</td></tr>
            <tr><td style="padding:.35rem 0;color:#7A6F6E;">Lokasi</td><td>{{ $booking->location_address }}</td></tr>
            @if($booking->location_notes)
            <tr><td style="padding:.35rem 0;color:#7A6F6E;">Catatan Lokasi</td><td>{{ $booking->location_notes }}</td></tr>
            @endif
            <tr><td style="padding:.35rem 0;color:#7A6F6E;">Layanan</td><td style="font-weight:600;">{{ $booking->service->name??'Custom' }}</td></tr>
            <tr><td style="padding:.35rem 0;color:#7A6F6E;">Harga</td><td style="font-weight:700;color:#9B6B6B;font-size:1rem;">Rp {{ number_format($booking->price,0,',','.') }}</td></tr>
            @if($booking->rejection_reason)
            <tr><td style="padding:.35rem 0;color:#7A6F6E;">Alasan Tolak</td><td style="color:#8A3030;">{{ $booking->rejection_reason }}</td></tr>
            @endif
        </table>
    </div>
</div>

{{-- Notes & Reference --}}
@if($booking->notes || $booking->reference_image)
<div class="card" style="margin-top:1.5rem;">
    <h2 class="card-title" style="margin-bottom:1rem;font-size:1.1rem;">📝 Catatan & Referensi</h2>
    <div style="display:grid;grid-template-columns:{{ $booking->reference_image ? '1fr 1fr' : '1fr' }};gap:1.5rem;">
        @if($booking->notes)
        <div>
            <p style="font-size:.8rem;color:#7A6F6E;margin-bottom:.5rem;">Catatan dari pelanggan:</p>
            <p style="line-height:1.6;">{{ $booking->notes }}</p>
        </div>
        @endif
        @if($booking->reference_image)
        <div>
            <p style="font-size:.8rem;color:#7A6F6E;margin-bottom:.5rem;">Gambar referensi:</p>
            <img src="{{ asset('storage/'.$booking->reference_image) }}" alt="Referensi"
                 style="max-width:280px;border-radius:14px;border:1px solid #EDE8E3;">
        </div>
        @endif
    </div>
</div>
@endif

{{-- QR Code & Verification Code --}}
@if($booking->status === 'approved')
<div class="card" style="margin-top:1.5rem;border:2px solid #C9A56E;background:linear-gradient(135deg,rgba(201,165,110,.04),rgba(212,165,165,.04));">
    <h2 class="card-title" style="margin-bottom:1rem;font-size:1.1rem;">🔑 Kode Verifikasi</h2>
    <div style="display:grid;grid-template-columns:{{ $booking->qr_code_path ? 'auto 1fr' : '1fr' }};gap:2rem;align-items:center;">
        @if($booking->qr_code_path && file_exists(storage_path('app/public/'.$booking->qr_code_path)))
        <div style="text-align:center;">
            <img src="{{ asset('storage/'.$booking->qr_code_path) }}" alt="QR Code"
                 style="width:160px;height:160px;border-radius:16px;border:1px solid #EDE8E3;">
            <p style="font-size:.75rem;color:#7A6F6E;margin-top:.5rem;">Scan QR ini untuk verifikasi</p>
        </div>
        @endif
        <div>
            <p style="font-size:.8rem;color:#7A6F6E;margin-bottom:.75rem;">Tunjukkan kode ini kepada pelanggan untuk diverifikasi pada hari-H:</p>
            <div style="font-family:'Playfair Display',serif;font-size:2.25rem;font-weight:700;letter-spacing:.4rem;color:#9B6B6B;text-align:center;padding:1.25rem;background:white;border-radius:14px;border:1px solid #EDE8E3;margin-bottom:.75rem;">
                {{ $booking->verification_code }}
            </div>
            <p style="font-size:.78rem;color:#9B8F8E;text-align:center;">Kode ini unik dan hanya berlaku untuk booking ini</p>
        </div>
    </div>
</div>
@endif

{{-- Review --}}
@if($booking->review)
<div class="card" style="margin-top:1.5rem;">
    <h2 class="card-title" style="margin-bottom:1rem;font-size:1.1rem;">⭐ Ulasan Pelanggan</h2>
    <div style="display:flex;gap:.3rem;margin-bottom:.75rem;">
        @for($i=1;$i<=5;$i++)
        <span style="font-size:1.25rem;color:{{ $i<=$booking->review->rating?'#C9A56E':'#E8E2DD' }};">★</span>
        @endfor
        <span style="font-weight:700;margin-left:.5rem;">{{ $booking->review->rating }}/5</span>
    </div>
    @if($booking->review->comment)
    <p style="color:#7A6F6E;line-height:1.6;font-style:italic;">"{{ $booking->review->comment }}"</p>
    @endif
</div>
@endif

{{-- Actions --}}
<div style="display:flex;gap:1rem;margin-top:2rem;flex-wrap:wrap;">
    @if($booking->status==='pending')
    <form action="{{ route('mua.bookings.approve',$booking->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-success" style="padding:.75rem 2rem;">✅ Terima Booking</button>
    </form>
    <button onclick="document.getElementById('rejectM').style.display='flex'" class="btn" style="padding:.75rem 2rem;background:rgba(183,110,110,.08);color:#B76E6E;border:1px solid #D49A9A;">❌ Tolak</button>
    @endif
    @if($booking->status==='verified')
    <form action="{{ route('mua.bookings.complete',$booking->id) }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-primary" style="padding:.75rem 2rem;">🎉 Tandai Selesai</button>
    </form>
    @endif
</div>

{{-- Reject Modal --}}
<div id="rejectM" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:50;align-items:center;justify-content:center;">
    <div style="background:white;border-radius:20px;padding:2rem;width:100%;max-width:440px;margin:1rem;">
        <h3 style="font-size:1.2rem;font-weight:700;margin-bottom:1rem;">Tolak Booking #{{ $booking->id }}</h3>
        <form action="{{ route('mua.bookings.reject',$booking->id) }}" method="POST">
            @csrf
            <textarea name="rejection_reason" rows="3" placeholder="Alasan penolakan..."
                      style="width:100%;padding:.75rem;border:1px solid #EDE8E3;border-radius:12px;font-family:inherit;resize:vertical;margin-bottom:1rem;"></textarea>
            <div style="display:flex;gap:.75rem;">
                <button type="button" onclick="document.getElementById('rejectM').style.display='none'" class="btn btn-outline" style="flex:1;">Batal</button>
                <button type="submit" class="btn btn-primary" style="flex:1;">Tolak</button>
            </div>
        </form>
    </div>
</div>
@endsection
