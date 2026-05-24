@extends('layouts.mua')

@section('content')
<div class="card">
    <h2 class="text-2xl font-bold mb-6">Kelola Portfolio</h2>
    
    {{-- Form Upload (Sesuaiin sama payload yg dipake Flutter) --}}
    <form action="{{ route('mua.portfolio.store') }}" method="POST" enctype="multipart/form-data" class="mb-8">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="file" name="images[]" multiple class="border p-2 rounded">
            <input type="text" name="titles[]" placeholder="Judul Foto" class="border p-2 rounded">
            <input type="text" name="captions[]" placeholder="Caption/Deskripsi" class="border p-2 rounded">
            <select name="style_categories[]" class="border p-2 rounded">
                <option value="wedding">Wedding</option>
                <option value="graduation">Graduation</option>
                <option value="party">Party</option>
            </select>
        </div>
        <button type="submit" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">Upload Portfolio</button>
    </form>

    {{-- Tabel Data --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        @foreach($portfolios as $p)
        <div class="border p-2 rounded">
            <img src="{{ asset('storage/' . $p->image_path) }}" class="w-full h-32 object-cover rounded">
            <p class="font-bold mt-2">{{ $p->title }}</p>
            <p class="text-xs text-gray-500">{{ $p->style_category }}</p>
            
            <form action="{{ route('mua.portfolio.destroy', $p->id) }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-500 text-sm">Hapus</button>
            </form>
        </div>
        @endforeach
    </div>
    <div style="padding: 2rem; max-width: 1200px; margin: auto;">
    <h2 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 1.5rem;">Portfolio Saya</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 1.5rem;">
        @foreach($portfolios as $item)
            <div style="background: white; padding: 1rem; border-radius: 16px; border: 1px solid #eee;">
                <img src="{{ asset('storage/'.$item->image) }}" style="width: 100%; border-radius: 12px;">
                <p style="margin-top: 10px; font-weight: 600;">{{ $item->title }}</p>
            </div>
        @endforeach
    </div>
</div>
</div>
@endsection