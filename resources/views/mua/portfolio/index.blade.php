@extends('layouts.mua')
@section('title', 'Portfolio Saya')

@section('content')
<header class="mb-8" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 class="text-4xl font-bold mb-2">Portfolio Saya</h1>
        <p class="text-gray">{{ $portfolios->total() }} foto · Tampilkan karya terbaik Anda</p>
    </div>
    <button onclick="document.getElementById('uploadModal').style.display='flex'"
            class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem;">
        + Unggah Foto
    </button>
</header>

@if(session('success'))
<div style="background: rgba(127,168,127,0.1); border: 1px solid #7FA87F; color: #3E7A3E; padding: 1rem; border-radius: 12px; margin-bottom: 1.5rem;">
    ✅ {{ session('success') }}
</div>
@endif

@if($portfolios->isEmpty())
<div style="text-align: center; padding: 5rem 2rem; background: white; border-radius: 20px; border: 2px dashed #E8E2DD;">
    <div style="font-size: 4rem; margin-bottom: 1rem;">🖼️</div>
    <h3 style="font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; font-family: 'Playfair Display', serif;">Belum ada foto portfolio</h3>
    <p style="color: #6B6160; margin-bottom: 1.5rem;">Unggah karya terbaik Anda untuk menarik lebih banyak pelanggan!</p>
    <button onclick="document.getElementById('uploadModal').style.display='flex'" class="btn btn-primary" style="padding: 0.75rem 2rem;">
        + Unggah Foto Pertama
    </button>
</div>
@else

{{-- Portfolio Grid --}}
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem;">
    @foreach($portfolios as $portfolio)
    <div class="portfolio-card" style="background: white; border-radius: 20px; overflow: hidden; border: 1px solid #E8E2DD; transition: box-shadow 0.2s;" 
         onmouseover="this.style.boxShadow='0 8px 24px rgba(155,107,107,0.12)'" 
         onmouseout="this.style.boxShadow='none'">
        <div style="position: relative; aspect-ratio: 4/3; overflow: hidden; background: #F5F3F1;">
            <img src="{{ asset('storage/' . $portfolio->image_path) }}"
                 alt="{{ $portfolio->title ?? 'Portfolio' }}"
                 style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;"
                 onmouseover="this.style.transform='scale(1.04)'"
                 onmouseout="this.style.transform='scale(1)'">
            @if($portfolio->style_category)
            <span style="position: absolute; top: 0.75rem; left: 0.75rem; padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.92); border-radius: 9999px; font-size: 0.72rem; font-weight: 700; color: #9B6B6B; text-transform: uppercase; letter-spacing: 0.04em;">
                {{ $portfolio->style_category }}
            </span>
            @endif
            <div style="position: absolute; top: 0.75rem; right: 0.75rem; display: flex; gap: 0.4rem;">
                <button onclick="openEditModal({{ $portfolio->id }}, '{{ addslashes($portfolio->title) }}', '{{ addslashes($portfolio->caption) }}', '{{ $portfolio->style_category }}')"
                        style="width: 34px; height: 34px; border-radius: 10px; background: rgba(255,255,255,0.92); border: none; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; justify-content: center;">✏️</button>
                <form action="{{ route('mua.portfolio.destroy', $portfolio->id) }}" method="POST"
                      onsubmit="return confirm('Hapus foto ini dari portfolio?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="width: 34px; height: 34px; border-radius: 10px; background: rgba(255,255,255,0.92); border: none; cursor: pointer; font-size: 0.9rem; display: flex; align-items: center; justify-content: center;">🗑️</button>
                </form>
            </div>
        </div>
        <div style="padding: 1rem;">
            <div style="font-weight: 700; color: #2C2423; margin-bottom: 0.25rem;">
                {{ $portfolio->title ?: 'Tanpa Judul' }}
            </div>
            @if($portfolio->caption)
            <div style="font-size: 0.8rem; color: #6B6160; line-height: 1.4;">{{ Str::limit($portfolio->caption, 80) }}</div>
            @endif
            <div style="font-size: 0.75rem; color: #9B8F8E; margin-top: 0.5rem;">{{ $portfolio->created_at->format('d M Y') }}</div>
        </div>
    </div>
    @endforeach
</div>

@if($portfolios->hasPages())
<div style="margin-top: 2rem;">{{ $portfolios->links() }}</div>
@endif
@endif

{{-- Upload Modal --}}
<div id="uploadModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: white; border-radius: 24px; padding: 2rem; width: 100%; max-width: 600px; max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.35rem; font-weight: 700; font-family: 'Playfair Display', serif;">Unggah Foto Portfolio</h3>
            <button onclick="document.getElementById('uploadModal').style.display='none'" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E8E2DD; background: white; cursor: pointer; font-size: 1.1rem; display: flex; align-items: center; justify-content: center;">×</button>
        </div>

        <form action="{{ route('mua.portfolio.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf
            {{-- Drag & Drop Area --}}
            <div id="dropZone" style="border: 2px dashed #D4A5A5; border-radius: 16px; padding: 2.5rem; text-align: center; background: rgba(212,165,165,0.04); cursor: pointer; margin-bottom: 1.5rem; transition: all 0.2s;"
                 onclick="document.getElementById('fileInput').click()"
                 ondragover="event.preventDefault(); this.style.background='rgba(212,165,165,0.1)'"
                 ondragleave="this.style.background='rgba(212,165,165,0.04)'"
                 ondrop="handleDrop(event)">
                <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📸</div>
                <p style="font-weight: 600; color: #2C2423; margin-bottom: 0.25rem;">Klik atau drag foto ke sini</p>
                <p style="font-size: 0.8rem; color: #6B6160;">JPG, PNG, WebP · Max 5MB per foto · Maks 10 foto</p>
            </div>
            <input type="file" id="fileInput" name="images[]" accept="image/*" multiple style="display: none;" onchange="previewImages(event)">

            {{-- Preview Area --}}
            <div id="previewArea" style="display: none; margin-bottom: 1.5rem;"></div>

            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('uploadModal').style.display='none'" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem;">Unggah</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 100; align-items: center; justify-content: center; padding: 1rem;">
    <div style="background: white; border-radius: 24px; padding: 2rem; width: 100%; max-width: 480px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1.2rem; font-weight: 700; font-family: 'Playfair Display', serif;">Edit Portfolio</h3>
            <button onclick="document.getElementById('editModal').style.display='none'" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E8E2DD; background: white; cursor: pointer; font-size: 1.1rem;">×</button>
        </div>
        <form id="editForm" method="POST">
            @csrf @method('PUT')
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Judul</label>
                <input type="text" name="title" id="editTitle" placeholder="Contoh: Rias Pengantin Sunda"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem;">
            </div>
            <div style="margin-bottom: 1rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Keterangan</label>
                <textarea name="caption" id="editCaption" rows="3" placeholder="Deskripsi singkat karya ini..."
                          style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem; resize: vertical;"></textarea>
            </div>
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.875rem;">Kategori Style</label>
                <select name="style_category" id="editCategory" style="width: 100%; padding: 0.75rem; border: 1px solid #E8E2DD; border-radius: 12px; font-family: inherit; font-size: 0.9rem; appearance: none;">
                    <option value="">— Pilih Kategori —</option>
                    @foreach(['wedding','graduation','party','photoshoot','formal','natural','glam','other'] as $cat)
                    <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: flex; gap: 0.75rem; justify-content: flex-end;">
                <button type="button" onclick="document.getElementById('editModal').style.display='none'" class="btn btn-outline">Batal</button>
                <button type="submit" class="btn btn-primary" style="padding: 0.6rem 1.75rem;">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, title, caption, category) {
    document.getElementById('editTitle').value = title;
    document.getElementById('editCaption').value = caption;
    document.getElementById('editCategory').value = category;
    document.getElementById('editForm').action = `/mua/portfolio/${id}`;
    document.getElementById('editModal').style.display = 'flex';
}

function previewImages(event) {
    const files = Array.from(event.target.files);
    renderPreviews(files);
}

function handleDrop(event) {
    event.preventDefault();
    document.getElementById('dropZone').style.background = 'rgba(212,165,165,0.04)';
    const files = Array.from(event.dataTransfer.files).filter(f => f.type.startsWith('image/'));
    const dt = new DataTransfer();
    files.forEach(f => dt.items.add(f));
    document.getElementById('fileInput').files = dt.files;
    renderPreviews(files);
}

function renderPreviews(files) {
    const area = document.getElementById('previewArea');
    area.style.display = 'block';
    area.innerHTML = '';

    files.slice(0, 10).forEach((file, idx) => {
        const reader = new FileReader();
        reader.onload = e => {
            const div = document.createElement('div');
            div.style.cssText = 'display:flex; gap:1rem; align-items:flex-start; padding:1rem; background:#FAF8F6; border-radius:16px; margin-bottom:0.75rem;';
            div.innerHTML = `
                <img src="${e.target.result}" style="width:80px; height:80px; object-fit:cover; border-radius:12px; flex-shrink:0;">
                <div style="flex:1; min-width:0;">
                    <input type="text" name="titles[]" placeholder="Judul (opsional)"
                           style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E8E2DD; border-radius:10px; font-family:inherit; font-size:0.85rem; margin-bottom:0.5rem;">
                    <select name="style_categories[]" style="width:100%; padding:0.5rem 0.75rem; border:1px solid #E8E2DD; border-radius:10px; font-family:inherit; font-size:0.85rem;">
                        <option value="">Kategori...</option>
                        ${['wedding','graduation','party','photoshoot','formal','natural','glam','other']
                          .map(c => `<option value="${c}">${c.charAt(0).toUpperCase()+c.slice(1)}</option>`).join('')}
                    </select>
                </div>`;
            area.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
}

// Close modal on backdrop click
['uploadModal','editModal'].forEach(id => {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>
@endpush
