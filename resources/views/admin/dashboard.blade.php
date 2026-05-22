@extends('layouts.admin')
@section('title','Dashboard Admin')

@section('content')
<header class="mb-8" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 class="text-4xl font-bold" style="margin-bottom:0.25rem;">Dashboard Admin</h1>
        <p class="text-gray">{{ now()->isoFormat('dddd, D MMMM Y') }} · Ringkasan platform BeautyHub</p>
    </div>
</header>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#FFF4E5,#FFE5C4);">💄</div>
        <div class="stat-value">{{ $totalMua }}</div>
        <div class="stat-label">Total MUA</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#E5F0FF,#D4E4FF);">👥</div>
        <div class="stat-value">{{ $totalCustomer }}</div>
        <div class="stat-label">Customer</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#E5FFE5,#D4F0D4);">📋</div>
        <div class="stat-value">{{ $totalBookings }}</div>
        <div class="stat-label">Total Booking</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#FFE5E5,#FFD4D4);">⏳</div>
        <div class="stat-value">{{ $pendingBookings }}</div>
        <div class="stat-label">Booking Pending</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#FFFBE5,#FFF5C4);">💰</div>
        <div class="stat-value" style="font-size:1.4rem;">Rp {{ number_format($totalRevenue,0,',','.') }}</div>
        <div class="stat-label">Total Revenue</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:linear-gradient(135deg,#F5E5FF,#EDD4FF);">🔍</div>
        <div class="stat-value">{{ $unverifiedMua }}</div>
        <div class="stat-label">MUA Perlu Verifikasi</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;">
    {{-- Recent Bookings --}}
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Booking Terbaru</h2>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline btn-sm">Lihat Pengguna</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Customer</th><th>MUA</th><th>Tanggal</th><th>Harga</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($recentBookings as $b)
                    <tr>
                        <td style="font-weight:600;font-size:0.85rem;">{{ $b->customer->name ?? '-' }}</td>
                        <td style="color:#5A6278;font-size:0.85rem;">{{ $b->mua->user->name ?? '-' }}</td>
                        <td style="font-size:0.82rem;">{{ $b->event_date->format('d M Y') }}</td>
                        <td style="font-weight:600;font-size:0.85rem;">Rp {{ number_format($b->price,0,',','.') }}</td>
                        <td><span class="badge badge-{{ $b->status }}">{{ strtoupper($b->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:2rem;color:#7A8396;">Belum ada booking.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Top MUAs + Quick links --}}
    <div style="display:flex;flex-direction:column;gap:1.5rem;">
        <div class="card">
            <h2 class="card-title" style="margin-bottom:1rem;">🏆 Top MUA</h2>
            @foreach($topMuas as $mua)
            <div style="display:flex;align-items:center;gap:0.75rem;padding:0.625rem 0;border-bottom:1px solid #F5F7FA;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#C9A56E,#9B6B6B);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:14px;flex-shrink:0;">
                    {{ substr($mua->user->name??'?',0,1) }}
                </div>
                <div style="flex:1;min-width:0;">
                    <div style="font-weight:700;font-size:0.825rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $mua->user->name }}</div>
                    <div style="font-size:0.72rem;color:#7A8396;">{{ $mua->location ?? '-' }}</div>
                </div>
                <span style="font-weight:700;font-size:0.82rem;color:#C9A56E;">⭐ {{ $mua->rating }}</span>
            </div>
            @endforeach
            <div style="margin-top:0.875rem;">
                <a href="{{ route('admin.muas.index') }}" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;">Lihat Semua MUA →</a>
            </div>
        </div>

        <div class="card">
            <h2 class="card-title" style="margin-bottom:1rem;">⚡ Aksi Cepat</h2>
            <div style="display:flex;flex-direction:column;gap:0.5rem;">
                <a href="{{ route('admin.muas.index', ['verified'=>'0']) }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#F5F7FA;border-radius:12px;text-decoration:none;transition:background 0.15s;" onmouseover="this.style.background='#EEF0F5'" onmouseout="this.style.background='#F5F7FA'">
                    <span style="font-size:1.25rem;">🔍</span>
                    <div>
                        <div style="font-weight:700;font-size:0.825rem;color:#1E2532;">Verifikasi MUA</div>
                        <div style="font-size:0.72rem;color:#7A8396;">{{ $unverifiedMua }} menunggu</div>
                    </div>
                </a>
                <a href="{{ route('admin.users.index', ['role'=>'customer']) }}" style="display:flex;align-items:center;gap:0.75rem;padding:0.75rem;background:#F5F7FA;border-radius:12px;text-decoration:none;transition:background 0.15s;" onmouseover="this.style.background='#EEF0F5'" onmouseout="this.style.background='#F5F7FA'">
                    <span style="font-size:1.25rem;">👥</span>
                    <div>
                        <div style="font-weight:700;font-size:0.825rem;color:#1E2532;">Kelola Customer</div>
                        <div style="font-size:0.72rem;color:#7A8396;">{{ $totalCustomer }} customer</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
