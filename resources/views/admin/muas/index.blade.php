@extends('layouts.admin')
@section('title', 'Kelola MUA')

@section('content')
<header class="mb-8" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 class="text-4xl font-bold" style="margin-bottom:0.25rem;">Kelola MUA</h1>
        <p class="text-gray">{{ $muas->total() }} MUA terdaftar</p>
    </div>
    <button onclick="document.getElementById('addMuaModal').style.display='flex'" class="btn btn-primary">+ Tambah MUA Baru</button>
</header>

<div class="card">
    {{-- Search & Filter --}}
    <form method="GET" class="search-bar">
        <input type="text" name="search" class="search-input" placeholder="🔍 Cari nama atau email..." value="{{ request('search') }}">
        <select name="verified" style="padding:0.6rem 1rem;border:1px solid #D8DCE8;border-radius:10px;font-family:inherit;font-size:0.875rem;outline:none;">
            <option value="">Semua Status</option>
            <option value="1" {{ request('verified')==='1' ? 'selected' : '' }}>✅ Terverifikasi</option>
            <option value="0" {{ request('verified')==='0' ? 'selected' : '' }}>⏳ Belum Verifikasi</option>
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
        @if(request('search') || request('verified') !== null)
        <a href="{{ route('admin.muas.index') }}" class="btn btn-outline">Reset</a>
        @endif
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>MUA</th>
                    <th>Lokasi</th>
                    <th>Pengalaman</th>
                    <th>Rating</th>
                    <th>Booking</th>
                    <th>Portfolio</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($muas as $mua)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:0.75rem;">
                            <div style="width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#C9A56E,#9B6B6B);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:16px;flex-shrink:0;">
                                {{ substr($mua->user->name ?? '?', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:0.875rem;">{{ $mua->user->name ?? '-' }}</div>
                                <div style="font-size:0.775rem;color:#7A8396;">{{ $mua->user->email ?? '-' }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:#5A6278;">{{ $mua->location ?? '-' }}</td>
                    <td style="color:#5A6278;">{{ $mua->experience_years }} thn</td>
                    <td>
                        <span style="font-weight:700;color:#C9A56E;">⭐ {{ number_format($mua->rating, 1) }}</span>
                        <div style="font-size:0.72rem;color:#7A8396;">{{ $mua->total_reviews }} ulasan</div>
                    </td>
                    <td style="font-weight:600;">{{ $mua->bookings_count }}</td>
                    <td style="font-weight:600;">{{ $mua->portfolios_count }}</td>
                    <td>
                        @if($mua->is_verified)
                            <span class="badge badge-verified">✅ Verified</span>
                        @else
                            <span class="badge badge-unverified">⏳ Pending</span>
                        @endif
                        <br>
                        @if($mua->user->is_active)
                            <span class="badge badge-active" style="margin-top:3px;">Aktif</span>
                        @else
                            <span class="badge badge-inactive" style="margin-top:3px;">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:0.4rem;flex-wrap:wrap;">
                            <a href="{{ route('admin.muas.show', $mua->id) }}" class="btn btn-outline btn-sm">Detail</a>
                            <form action="{{ route('admin.muas.toggle-verified', $mua->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm {{ $mua->is_verified ? 'btn-danger' : 'btn-success' }}">
                                    {{ $mua->is_verified ? 'Batalkan' : 'Verifikasi' }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:3rem;color:#7A8396;">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">💄</div>
                    Tidak ada MUA ditemukan.
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($muas->hasPages())
    <div style="margin-top:1.5rem;">{{ $muas->appends(request()->query())->links() }}</div>
    @endif
</div>

{{-- Add MUA Modal --}}
<div id="addMuaModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:200;align-items:center;justify-content:center;padding:1rem;">
    <div style="background:white;border-radius:20px;padding:2rem;width:100%;max-width:500px;max-height:90vh;overflow-y:auto;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="font-size:1.25rem;font-weight:700;font-family:'Playfair Display',serif;">Tambah Akun MUA Baru</h3>
            <button onclick="document.getElementById('addMuaModal').style.display='none'" style="width:32px;height:32px;border-radius:8px;border:1px solid #D8DCE8;background:white;cursor:pointer;font-size:1.1rem;">×</button>
        </div>
        <form action="{{ route('admin.muas.store') }}" method="POST">
            @csrf
            @foreach([['name','Nama Lengkap','text','Contoh: Siti Aminah',true],['email','Email','email','mua@example.com',true],['phone','No. Telepon','text','08xxxxxxxxxx',false],['location','Kota / Lokasi','text','Contoh: Kota Cirebon',false],['password','Password Awal','password','Min. 8 karakter',true]] as [$field,$label,$type,$ph,$req])
            <div style="margin-bottom:1rem;">
                <label style="display:block;font-weight:600;margin-bottom:0.4rem;font-size:0.85rem;">{{ $label }}{{ $req ? ' *' : '' }}</label>
                <input type="{{ $type }}" name="{{ $field }}" placeholder="{{ $ph }}" {{ $req ? 'required' : '' }}
                       style="width:100%;padding:0.7rem 0.875rem;border:1px solid #D8DCE8;border-radius:10px;font-family:inherit;font-size:0.875rem;outline:none;">
            </div>
            @endforeach
            <div style="display:flex;gap:0.75rem;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="document.getElementById('addMuaModal').style.display='none'" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Akun MUA</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('addMuaModal').addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>
@endpush
