@extends('layouts.admin')
@section('title', 'Detail MUA')

@section('content')
<div style="margin-bottom:1.25rem;">
    <a href="{{ route('admin.muas.index') }}" style="color:#C9A56E;text-decoration:none;font-weight:500;">← Kembali ke Daftar MUA</a>
</div>

<header class="mb-8" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
    <div style="display:flex;align-items:center;gap:1.25rem;">
        <div style="width:72px;height:72px;border-radius:18px;background:linear-gradient(135deg,#C9A56E,#9B6B6B);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;color:white;flex-shrink:0;">
            {{ substr($mua->user->name ?? '?', 0, 1) }}
        </div>
        <div>
            <h1 class="text-4xl font-bold" style="margin-bottom:0.25rem;">{{ $mua->user->name }}</h1>
            <p class="text-gray">{{ $mua->user->email }} · {{ $mua->location ?? 'Lokasi belum diisi' }}</p>
            <div style="margin-top:0.5rem;display:flex;gap:0.5rem;flex-wrap:wrap;">
                <span class="badge {{ $mua->is_verified ? 'badge-verified' : 'badge-unverified' }}">{{ $mua->is_verified ? '✅ Terverifikasi' : '⏳ Belum Verifikasi' }}</span>
                <span class="badge {{ $mua->user->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $mua->user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
        <form action="{{ route('admin.muas.toggle-verified', $mua->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn {{ $mua->is_verified ? 'btn-danger' : 'btn-success' }}">
                {{ $mua->is_verified ? '❌ Batalkan Verifikasi' : '✅ Verifikasi MUA' }}
            </button>
        </form>
    </div>
</header>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
    {{-- Info Profil --}}
    <div class="card">
        <h2 class="card-title" style="margin-bottom:1rem;">👤 Profil MUA</h2>
        <table style="width:100%;font-size:0.875rem;border-collapse:collapse;">
            @foreach([['Pengalaman',$mua->experience_years.' tahun'],['Rating','⭐ '.$mua->rating.' ('.$mua->total_reviews.' ulasan)'],['Sertifikat',$mua->certificate ?? '-'],['Telepon',$mua->user->phone ?? '-'],['Bio',$mua->bio ?? '-'],['Style Tags',implode(', ', $mua->style_tags ?? []) ?: '-']] as [$label,$val])
            <tr>
                <td style="padding:0.5rem 0;color:#7A8396;width:38%;font-weight:500;">{{ $label }}</td>
                <td style="padding:0.5rem 0;font-weight:600;">{{ $val }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    {{-- Stats --}}
    <div class="card">
        <h2 class="card-title" style="margin-bottom:1rem;">📊 Statistik</h2>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
            @foreach([['💰','Total Pendapatan','Rp '.number_format($revenue,0,',','.')],['📋','Total Booking',$mua->bookings->count()],['🖼️','Portfolio',$mua->portfolios->count()],['💄','Layanan',$mua->services->count()]] as [$icon,$label,$val])
            <div style="background:#F5F7FA;border-radius:12px;padding:1rem;text-align:center;">
                <div style="font-size:1.5rem;margin-bottom:0.3rem;">{{ $icon }}</div>
                <div style="font-weight:700;font-size:1.1rem;color:#1E2532;">{{ $val }}</div>
                <div style="font-size:0.75rem;color:#7A8396;margin-top:2px;">{{ $label }}</div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Layanan --}}
@if($mua->services->isNotEmpty())
<div class="card" style="margin-bottom:1.5rem;">
    <h2 class="card-title" style="margin-bottom:1rem;">💄 Layanan</h2>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:0.75rem;">
        @foreach($mua->services as $svc)
        <div style="background:#F5F7FA;border-radius:12px;padding:1rem;{{ !$svc->is_active ? 'opacity:0.6;' : '' }}">
            <div style="font-weight:700;font-size:0.875rem;margin-bottom:0.25rem;">{{ $svc->name }}</div>
            <div style="font-size:0.78rem;color:#7A8396;text-transform:uppercase;letter-spacing:0.04em;margin-bottom:0.5rem;">{{ $svc->category }}</div>
            <div style="font-weight:700;color:#C9A56E;">Rp {{ number_format($svc->price,0,',','.') }}</div>
            @if(!$svc->is_active)<span class="badge badge-inactive" style="margin-top:4px;display:inline-block;">Nonaktif</span>@endif
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Booking Terbaru --}}
<div class="card">
    <h2 class="card-title" style="margin-bottom:1rem;">📋 Booking Terbaru</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Pelanggan</th><th>Layanan</th><th>Tanggal Acara</th><th>Harga</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td style="font-weight:600;">{{ $b->customer->name ?? '-' }}</td>
                    <td style="color:#5A6278;">{{ $b->service->name ?? 'Custom' }}</td>
                    <td>{{ $b->event_date->format('d M Y') }}</td>
                    <td style="font-weight:600;">Rp {{ number_format($b->price,0,',','.') }}</td>
                    <td><span class="badge badge-{{ $b->status }}">{{ strtoupper($b->status) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:2rem;color:#7A8396;">Belum ada booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
