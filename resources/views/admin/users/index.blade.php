@extends('layouts.admin')
@section('title', 'Kelola Pengguna')

@section('content')
<header class="mb-8">
    <h1 class="text-4xl font-bold" style="margin-bottom:0.25rem;">Kelola Pengguna</h1>
    <p class="text-gray">Total {{ $counts['all'] }} pengguna terdaftar</p>
</header>

{{-- Count tabs --}}
<div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1.5rem;">
    @foreach([['all','Semua',$counts['all'],'#5A6278'],['admin','Admin',$counts['admin'],'#8A6020'],['mua','MUA',$counts['mua'],'#7A3A3A'],['customer','Customer',$counts['customer'],'#3A4070']] as [$role,$label,$count,$color])
    <a href="{{ route('admin.users.index', ['role'=>$role==='all'?null:$role] + request()->except('role','page')) }}"
       style="padding:0.5rem 1.25rem;border-radius:10px;font-size:0.85rem;font-weight:600;text-decoration:none;
              {{ request('role',$role==='all'?null:null) === ($role==='all'?null:$role) || (request('role') === null && $role==='all') ? "background:{$color};color:white;" : 'background:white;color:#5A6278;border:1px solid #D8DCE8;' }}">
        {{ $label }} <span style="opacity:0.8;">{{ $count }}</span>
    </a>
    @endforeach
</div>

<div class="card">
    <form method="GET" class="search-bar">
        <input type="hidden" name="role" value="{{ request('role') }}">
        <input type="text" name="search" class="search-input" placeholder="🔍 Cari nama atau email..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-outline">Cari</button>
        @if(request('search'))
        <a href="{{ route('admin.users.index', ['role' => request('role')]) }}" class="btn btn-outline">Reset</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Telepon</th>
                    <th>Booking</th>
                    <th>Status</th>
                    <th>Bergabung</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div style="width:38px;height:38px;border-radius:10px;flex-shrink:0;overflow:hidden;background:linear-gradient(135deg,#C9A56E,#9B6B6B);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/'.$user->avatar) }}" style="width:100%;height:100%;object-fit:cover;">
                                @else
                                    {{ substr($user->name,0,1) }}
                                @endif
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:0.875rem;">{{ $user->name }}</div>
                                <div style="font-size:0.775rem;color:#7A8396;">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge badge-{{ $user->role }}">{{ strtoupper($user->role) }}</span></td>
                    <td style="color:#5A6278;font-size:0.85rem;">{{ $user->phone ?? '-' }}</td>
                    <td style="font-weight:600;text-align:center;">{{ $user->bookings_count }}</td>
                    <td>
                        <span class="badge {{ $user->is_active ? 'badge-active' : 'badge-inactive' }}">
                            {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td style="color:#7A8396;font-size:0.8rem;">{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-outline btn-sm">Detail</a>
                            @if(!$user->isAdmin())
                            <form action="{{ route('admin.users.toggle-active', $user->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-danger' : 'btn-success' }}">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:#7A8396;">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">👥</div>
                    Tidak ada pengguna ditemukan.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
    <div style="margin-top:1.5rem;">{{ $users->appends(request()->query())->links() }}</div>
    @endif
</div>
@endsection
