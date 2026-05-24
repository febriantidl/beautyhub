@extends('layouts.mua')
@section('title', 'Kelola Layanan')

@section('content')

<header class="mb-8" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;">
    <div>
        <h1 style="font-size:2rem;font-weight:700;color:#2B2B2B;margin-bottom:.35rem;">
            Kelola Layanan
        </h1>

        <p class="text-gray" style="font-size:.92rem;">
            {{ $services->count() }} layanan tersedia · Atur paket dan harga MUA Anda
        </p>
    </div>

    <button onclick="openAddModal()" class="btn btn-primary" style="
        padding:.85rem 1.4rem;
        border-radius:14px;
        font-weight:700;
    ">
        + Tambah Layanan
    </button>
</header>

@if(session('success'))
<div style="
    background:#F4FFF4;
    border:1px solid #B8D8B8;
    color:#2F6A2F;
    padding:1rem 1.1rem;
    border-radius:16px;
    margin-bottom:1.5rem;
    font-size:.9rem;
">
    ✅ {{ session('success') }}
</div>
@endif

@php
$categoryIcons = [
    'wedding'=>'💍',
    'graduation'=>'🎓',
    'party'=>'🎉',
    'photoshoot'=>'📸',
    'formal'=>'👑',
    'other'=>'✨'
];

$categoryLabels = [
    'wedding'=>'Wedding',
    'graduation'=>'Wisuda',
    'party'=>'Party',
    'photoshoot'=>'Photoshoot',
    'formal'=>'Formal',
    'other'=>'Lainnya'
];
@endphp

@if($services->isEmpty())

<div style="
    background:white;
    border-radius:28px;
    border:1px dashed #E8E2DD;
    padding:5rem 2rem;
    text-align:center;
">
    <div style="font-size:4rem;margin-bottom:1rem;">
        💄
    </div>

    <h2 style="
        font-size:1.7rem;
        font-weight:700;
        color:#2B2B2B;
        margin-bottom:.7rem;
    ">
        Belum Ada Layanan
    </h2>

    <p style="
        color:#7A6F6E;
        max-width:450px;
        margin:auto;
        line-height:1.7;
        margin-bottom:1.7rem;
    ">
        Tambahkan layanan makeup pertama Anda agar client bisa melihat paket dan harga yang tersedia.
    </p>

    <button onclick="openAddModal()" class="btn btn-primary" style="
        padding:.9rem 2rem;
        border-radius:14px;
    ">
        + Tambah Layanan
    </button>
</div>

@else

<div style="
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
    gap:1.35rem;
">

@foreach($services as $service)

<div style="
    background:white;
    border-radius:24px;
    padding:1.4rem;
    border:1px solid #EEE6E2;
    transition:.25s ease;
    position:relative;
    overflow:hidden;
">

    {{-- top accent --}}
    <div style="
        position:absolute;
        top:0;
        left:0;
        right:0;
        height:5px;
        background:
        {{ $service->is_active
            ? 'linear-gradient(90deg,#4f0404,#C9A56E)'
            : '#D9D9D9'
        }};
    "></div>

    {{-- header --}}
    <div style="
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        margin-bottom:1rem;
        gap:.8rem;
    ">

        <div style="
            display:flex;
            gap:.9rem;
            align-items:center;
        ">
            <div style="
                width:54px;
                height:54px;
                border-radius:16px;
                background:#FAF5EF;
                display:flex;
                align-items:center;
                justify-content:center;
                font-size:1.5rem;
                flex-shrink:0;
            ">
                {{ $categoryIcons[$service->category] ?? '✨' }}
            </div>

            <div>
                <div style="
                    font-weight:700;
                    font-size:1rem;
                    color:#2B2B2B;
                    margin-bottom:.2rem;
                ">
                    {{ $service->name }}
                </div>

                <div style="
                    font-size:.78rem;
                    color:#8A7E7C;
                ">
                    {{ $categoryLabels[$service->category] ?? $service->category }}
                </div>
            </div>
        </div>

        <div>
            @if($service->is_active)
                <span style="
                    background:#EDF9ED;
                    color:#2F7A2F;
                    font-size:.72rem;
                    padding:.35rem .7rem;
                    border-radius:999px;
                    font-weight:700;
                ">
                    Aktif
                </span>
            @else
                <span style="
                    background:#FFF1F1;
                    color:#B04A4A;
                    font-size:.72rem;
                    padding:.35rem .7rem;
                    border-radius:999px;
                    font-weight:700;
                ">
                    Nonaktif
                </span>
            @endif
        </div>

    </div>

    {{-- desc --}}
    <div style="
        color:#6F6463;
        font-size:.88rem;
        line-height:1.7;
        margin-bottom:1.3rem;
        min-height:70px;
    ">
        {{ $service->description }}
    </div>

    {{-- price --}}
    <div style="
        display:flex;
        align-items:end;
        justify-content:space-between;
        margin-bottom:1.4rem;
    ">
        <div>
            <div style="
                font-size:.72rem;
                color:#9B8F8E;
                margin-bottom:.2rem;
            ">
                Harga Paket
            </div>

            <div style="
                font-size:1.35rem;
                font-weight:700;
                color:#4f0404;
            ">
                Rp {{ number_format($service->price, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- actions --}}
    <div style="
        display:flex;
        gap:.65rem;
    ">

        <button
            onclick="openEditModal(
                {{ $service->id }},
                '{{ addslashes($service->name) }}',
                '{{ addslashes($service->description) }}',
                {{ $service->price }},
                '{{ $service->category }}',
                {{ $service->is_active ? 'true' : 'false' }}
            )"
            class="btn btn-outline"
            style="
                flex:1;
                justify-content:center;
                border-radius:12px;
            "
        >
            ✏️ Edit
        </button>

        <form
            action="{{ route('mua.services.destroy', $service->id) }}"
            method="POST"
            onsubmit="return confirm('Hapus layanan ini?')"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                style="
                    width:48px;
                    height:48px;
                    border:none;
                    border-radius:12px;
                    background:#FFF1F1;
                    cursor:pointer;
                    font-size:1rem;
                "
            >
                🗑️
            </button>
        </form>

    </div>

</div>

@endforeach
</div>

@endif

{{-- MODAL --}}
<div id="addModal" style="
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.55);
    z-index:999;
    align-items:center;
    justify-content:center;
    padding:1rem;
    backdrop-filter:blur(3px);
">

    <div style="
        width:100%;
        max-width:560px;
        background:white;
        border-radius:28px;
        padding:2rem;
        position:relative;
    ">

        <h3 id="modalTitle" style="
            font-size:1.45rem;
            font-weight:700;
            margin-bottom:1.5rem;
            color:#2B2B2B;
        ">
            Tambah Layanan
        </h3>

        <form id="serviceForm" method="POST" action="">
            @csrf

            <div id="methodField"></div>

            @include('mua.services._form')

            <div style="
                display:flex;
                justify-content:flex-end;
                gap:.8rem;
                margin-top:1.8rem;
            ">
                <button
                    type="button"
                    onclick="closeModals()"
                    class="btn btn-outline"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Simpan Layanan
                </button>
            </div>

        </form>
    </div>
</div>

<script>

function openAddModal() {

    document.getElementById('modalTitle').textContent = 'Tambah Layanan';

    document.getElementById('serviceForm').action =
        '{{ route("mua.services.store") }}';

    document.getElementById('methodField').innerHTML = '';

    document.getElementById('addModal').style.display = 'flex';
}

function openEditModal(id, name, desc, price, category, isActive) {

    document.getElementById('modalTitle').textContent = 'Edit Layanan';

    document.getElementById('serviceForm').action =
        `/mua/services/${id}`;

    document.getElementById('methodField').innerHTML =
        '@method("PUT")';

    const form = document.getElementById('serviceForm');

    form.elements['name'].value = name;
    form.elements['description'].value = desc;
    form.elements['price'].value = price;
    form.elements['category'].value = category;

    if(form.elements['is_active']) {
        form.elements['is_active'].checked = isActive;
    }

    document.getElementById('addModal').style.display = 'flex';
}

function closeModals() {
    document.getElementById('addModal').style.display = 'none';
}

window.onclick = function(e) {

    const modal = document.getElementById('addModal');

    if(e.target === modal){
        closeModals();
    }
}

</script>

@endsection