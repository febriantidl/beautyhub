@extends('layouts.mua')
@section('title', 'Profil Saya')

@section('content')
<header class="mb-8">
    <h1 class="text-4xl font-bold mb-2">Profil Saya</h1>
    <p class="text-gray">Kelola informasi akun dan profil profesional Anda</p>
</header>

@if(session('success'))
<div style="background: rgba(127,168,127,0.1); border: 1px solid #7FA87F; color: #3E7A3E; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
    ✅ {{ session('success') }}
</div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

    {{-- Profile Update Form --}}
    <div class="card" style="grid-column: 1 / -1;">
        <h2 class="card-title" style="margin-bottom: 1.5rem; font-size: 1.2rem;">👤 Informasi Akun & Profil MUA</h2>
        <form action="{{ route('mua.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">

                {{-- Avatar --}}
                <div style="grid-column: 1 / -1; display: flex; align-items: center; gap: 1.5rem;">
                    <div id="avatarPreview" style="width: 88px; height: 88px; border-radius: 20px; overflow: hidden; border: 2px solid #E8E2DD; flex-shrink: 0; background: linear-gradient(135deg, #D4A5A5, #C9A56E); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700; color: white;">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            {{ substr($user->name, 0, 1) }}
                        @endif
                    </div>
                    <div>
                        <label for="avatarInput" class="btn btn-outline" style="cursor: pointer; display: inline-block;">📷 Ganti Foto</label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display: none;"
                               onchange="previewAvatar(event)">
                        <p style="font-size: 0.78rem; color: #9B8F8E; margin-top: 0.4rem;">JPG, PNG, WebP · Max 2MB</p>
                    </div>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Nama Lengkap *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">No. Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Kota/Lokasi</label>
                    <input type="text" name="location" value="{{ old('location', $mua->location) }}" placeholder="Contoh: Kota Cirebon"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Pengalaman (tahun)</label>
                    <input type="number" name="experience_years" value="{{ old('experience_years', $mua->experience_years) }}" min="0" max="50"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                </div>

                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Bio / Deskripsi Diri</label>
                    <textarea name="bio" rows="3" placeholder="Ceritakan tentang keahlian dan pengalaman Anda..."
                              style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem; resize: vertical;">{{ old('bio', $mua->bio) }}</textarea>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Sertifikat</label>
                    <input type="text" name="certificate" value="{{ old('certificate', $mua->certificate) }}" placeholder="Contoh: Bersertifikat BNSP"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Gender</label>
                    <select name="gender" style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                        <option value="">— Pilih —</option>
                        <option value="female" {{ $user->gender === 'female' ? 'selected' : '' }}>Perempuan</option>
                        <option value="male"   {{ $user->gender === 'male'   ? 'selected' : '' }}>Laki-laki</option>
                    </select>
                </div>

                <div style="grid-column: 1 / -1;">
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Style Tags
                        <span style="font-weight: 400; color: #9B8F8E; font-size: 0.8rem;">(tekan Enter untuk tambah)</span>
                    </label>
                    <div id="tagContainer" style="display: flex; flex-wrap: wrap; gap: 0.5rem; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; min-height: 48px; cursor: text;" onclick="document.getElementById('tagInput').focus()">
                        @foreach($mua->style_tags ?? [] as $tag)
                        <span class="tag-chip" style="background: rgba(212,165,165,0.15); color: #9B6B6B; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.82rem; font-weight: 600; display: flex; align-items: center; gap: 0.35rem;">
                            {{ $tag }}
                            <button type="button" onclick="removeTag(this)" style="background: none; border: none; cursor: pointer; color: #9B6B6B; font-size: 0.9rem; line-height: 1; padding: 0;">×</button>
                            <input type="hidden" name="style_tags[]" value="{{ $tag }}">
                        </span>
                        @endforeach
                        <input type="text" id="tagInput" placeholder="wedding, natural, glam..."
                               style="border: none; outline: none; font-family: inherit; font-size: 0.85rem; min-width: 120px; flex: 1; background: transparent;">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem; font-size: 0.95rem;">💾 Simpan Perubahan</button>
            </div>
        </form>
    </div>

    {{-- Change Password --}}
    <div class="card" style="grid-column: 1 / -1;">
        <h2 class="card-title" style="margin-bottom: 1.5rem; font-size: 1.2rem;">🔒 Ubah Password</h2>
        <form action="{{ route('mua.profile.password') }}" method="POST">
            @csrf @method('PUT')
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Password Saat Ini</label>
                    <input type="password" name="current_password" required
                           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                    @error('current_password') <p style="color: #B76E6E; font-size: 0.8rem; margin-top: 0.3rem;">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Password Baru</label>
                    <input type="password" name="password" required minlength="8"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                           style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-outline" style="padding: 0.75rem 2.5rem;">🔑 Ubah Password</button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
function previewAvatar(e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = ev => {
        const preview = document.getElementById('avatarPreview');
        preview.innerHTML = `<img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
    };
    reader.readAsDataURL(file);
}

// Tag input
document.getElementById('tagInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        const val = this.value.trim().toLowerCase().replace(/,/g,'');
        if (!val) return;
        const container = document.getElementById('tagContainer');
        const chip = document.createElement('span');
        chip.style.cssText = 'background:rgba(212,165,165,0.15);color:#9B6B6B;padding:0.25rem 0.75rem;border-radius:9999px;font-size:0.82rem;font-weight:600;display:flex;align-items:center;gap:0.35rem;';
        chip.innerHTML = `${val}<button type="button" onclick="removeTag(this)" style="background:none;border:none;cursor:pointer;color:#9B6B6B;font-size:0.9rem;line-height:1;padding:0;">×</button><input type="hidden" name="style_tags[]" value="${val}">`;
        container.insertBefore(chip, this);
        this.value = '';
    }
});

function removeTag(btn) {
    btn.parentElement.remove();
}
</script>
@endpush
