@extends('layouts.admin')
@section('title', 'Detail Pengguna')

@section('content')
<div style="margin-bottom:1.25rem;">
    <a href="{{ route('admin.users.index') }}" style="color:#C9A56E;text-decoration:none;font-weight:500;">← Kembali ke Daftar Pengguna</a>
</div>

<header class="mb-8" style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
    <div style="display:flex;align-items:center;gap:1.25rem;">
        <div style="width:72px;height:72px;border-radius:18px;overflow:hidden;background:linear-gradient(135deg,#C9A56E,#9B6B6B);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:700;color:white;flex-shrink:0;">
            @if($user->avatar)<img src="{{ asset('storage/'.$user->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
            @else {{ substr($user->name,0,1) }} @endif
        </div>
        <div>
            <h1 class="text-4xl font-bold" style="margin-bottom:0.25rem;">{{ $user->name }}</h1>
            <p class="text-gray">{{ $user->email }} · {{ $user->phone ?? 'No. telepon belum diisi' }}</p>
            <div style="margin-top:0.5rem;display:flex;gap:0.5rem;">
                <span class="badge badge-{{ $user->role }}">{{ strtoupper($user->role) }}</span>
                <span class="badge {{ $user->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
        @if(!$user->isAdmin())
        <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST">
            @csrf
            <button type="submit" class="btn {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                {{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}
            </button>
        </form>
        <form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST" onsubmit="return confirm('Reset password ke Password1! ?')">
            @csrf
            <button type="submit" class="btn btn-outline">🔑 Reset Password</button>
        </form>
        {{-- Change Role --}}
        <form action="{{ route('admin.users.update-role', $user->id) }}" method="POST" style="display:flex;gap:0.5rem;align-items:center;">
            @csrf
            <select name="role" style="padding:0.5rem 0.75rem;border:1px solid #D8DCE8;border-radius:10px;font-family:inherit;font-size:0.825rem;outline:none;">
                @foreach(['admin','mua','customer'] as $r)
                <option value="{{ $r }}" {{ $user->role === $r ? 'selected' : '' }}>{{ ucfirst($r) }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline btn-sm">Ubah Role</button>
        </form>
        @endif
    </div>
</header>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.5rem;">
    <div class="card">
        <h2 class="card-title" style="margin-bottom:1rem;">📋 Informasi Akun</h2>
        <table style="width:100%;font-size:0.875rem;border-collapse:collapse;">
            @foreach([['Nama',$user->name],['Email',$user->email],['Telepon',$user->phone??'-'],['Alamat',$user->address??'-'],['Gender',$user->gender?ucfirst($user->gender):'-'],['Bergabung',$user->created_at->format('d M Y')]] as [$l,$v])
            <tr>
                <td style="padding:0.5rem 0;color:#7A8396;width:35%;font-weight:500;">{{ $l }}</td>
                <td style="padding:0.5rem 0;font-weight:600;">{{ $v }}</td>
            </tr>
            @endforeach
        </table>
    </div>

    @if($user->isMua() && $user->mua)
    <div class="card">
        <h2 class="card-title" style="margin-bottom:1rem;">💄 Profil MUA</h2>
        <table style="width:100%;font-size:0.875rem;border-collapse:collapse;">
            <tr><td style="padding:0.5rem 0;color:#7A8396;width:40%;">Lokasi</td><td style="font-weight:600;">{{ $user->mua->location??'-' }}</td></tr>
            <tr><td style="padding:0.5rem 0;color:#7A8396;">Pengalaman</td><td style="font-weight:600;">{{ $user->mua->experience_years }} tahun</td></tr>
            <tr><td style="padding:0.5rem 0;color:#7A8396;">Rating</td><td style="font-weight:700;color:#C9A56E;">⭐ {{ $user->mua->rating }} ({{ $user->mua->total_reviews }} ulasan)</td></tr>
            <tr><td style="padding:0.5rem 0;color:#7A8396;">Layanan</td><td style="font-weight:600;">{{ $user->mua->services->count() }} layanan</td></tr>
            <tr><td style="padding:0.5rem 0;color:#7A8396;">Portfolio</td><td style="font-weight:600;">{{ $user->mua->portfolios->count() }} foto</td></tr>
            <tr><td style="padding:0.5rem 0;color:#7A8396;">Terverifikasi</td>
                <td><span class="badge {{ $user->mua->is_verified?'badge-verified':'badge-unverified' }}">{{ $user->mua->is_verified?'Ya':'Belum' }}</span></td></tr>
        </table>
        <div style="margin-top:1rem;">
            <a href="{{ route('admin.muas.show', $user->mua->id) }}" class="btn btn-outline btn-sm">Lihat Profil MUA Lengkap →</a>
        </div>
    </div>
    @endif
</div>

{{-- Riwayat Booking --}}
<div class="card">
    <h2 class="card-title" style="margin-bottom:1rem;">📋 Riwayat Booking</h2>
    <div class="table-wrap">
        <table>
            <thead><tr><th>MUA</th><th>Layanan</th><th>Tanggal Acara</th><th>Harga</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($bookings as $b)
                <tr>
                    <td style="font-weight:600;">{{ $b->mua->user->name ?? '-' }}</td>
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
