@extends('layouts.mua')
@section('title', 'Kelola Layanan')

@section('content')
<header class="mb-8" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="text-4xl font-bold mb-2">Kelola Layanan</h1>
        <p class="text-gray">{{ $services->count() }} layanan · Atur paket dan harga Anda</p>
    </div>
    <button onclick="openAddModal()" class="btn btn-primary" style="padding: 0.75rem 1.75rem;">+ Tambah Layanan</button>
</header>

@if(session('success'))
<div style="background: rgba(127,168,127,0.1); border: 1px solid #7FA87F; color: #3E7A3E; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
    ✅ {{ session('success') }}
</div>
@endif

@php
$categoryIcons = ['wedding'=>'💍','graduation'=>'🎓','party'=>'🎉','photoshoot'=>'📸','formal'=>'👔','other'=>'✨'];
$categoryLabels = ['wedding'=>'Wedding','graduation'=>'Wisuda','party'=>'Party','photoshoot'=>'Photoshoot','formal'=>'Formal','other'=>'Lainnya'];
@endphp

@if($services->isEmpty())
<div style="text-align: center; padding: 5rem 2rem; background: white; border-radius: 20px; border: 2px dashed #E8E2DD;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">💄</div>
    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; font-family: 'Playfair Display', serif;">Belum ada layanan</h3>
    <button onclick="openAddModal()" class="btn btn-primary" style="padding: 0.75rem 2rem;">+ Tambah Layanan Pertama</button>
</div>
@else
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.25rem;">
    @foreach($services as $service)
    <div style="background: white; border-radius: 20px; border: 1px solid {{ $service->is_active ? '#E8E2DD' : '#FFD5D5' }}; padding: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
            <span style="font-size: 1.75rem;">{{ $categoryIcons[$service->category] ?? '✨' }}</span>
            <div>
                <div style="font-weight: 700; font-size: 1rem;">{{ $service->name }}</div>
                <div style="font-size: 0.78rem; color: #9B8F8E;">{{ $categoryLabels[$service->category] ?? $service->category }}</div>
            </div>
        </div>
        <p style="font-size: 0.85rem; color: #6B6160; margin-bottom: 1rem;">{{ $service->description }}</p>
        <div style="font-weight: 700; color: #9B6B6B; margin-bottom: 1.25rem;">Rp {{ number_format($service->price, 0, ',', '.') }}</div>
        <div style="display: flex; gap: 0.5rem;">
            <button onclick="openEditModal({{ $service->id }}, '{{ addslashes($service->name) }}', '{{ addslashes($service->description) }}', {{ $service->price }}, '{{ $service->category }}', {{ $service->is_active ? 'true' : 'false' }})" class="btn btn-outline btn-sm" style="flex: 1;">✏️ Edit</button>
            <form action="{{ route('mua.services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Hapus layanan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm" style="background: #FFF1F1; color: #B76E6E; border: 1px solid #D49A9A;">🗑️</button>
            </form>
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- Modal --}}
<div id="addModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: white; border-radius: 24px; padding: 2rem; width: 100%; max-width: 500px;">
        <h3 id="modalTitle" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem;">Tambah Layanan</h3>
        <form id="serviceForm" method="POST" action="">
            @csrf
            <div id="methodField"></div> {{-- Tempat naruh @method('PUT') --}}
            @include('mua.services._form')
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end; margin-top: 1.5rem;">
                <button type="button" onclick="closeModals()" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Layanan';
    document.getElementById('serviceForm').action = '{{ route("mua.services.store") }}';
    document.getElementById('methodField').innerHTML = ''; 
    document.getElementById('addModal').style.display = 'flex';
}

function openEditModal(id, name, desc, price, category, isActive) {
    document.getElementById('modalTitle').textContent = 'Edit Layanan';
    document.getElementById('serviceForm').action = `/mua/services/${id}`;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    
    const form = document.getElementById('serviceForm');
    form.elements['name'].value = name;
    form.elements['description'].value = desc;
    form.elements['price'].value = price;
    form.elements['category'].value = category;
    if(form.elements['is_active']) form.elements['is_active'].checked = isActive;
    
    document.getElementById('addModal').style.display = 'flex';
}

function closeModals() { document.getElementById('addModal').style.display = 'none'; }
</script>
@endsection