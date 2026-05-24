@extends('layouts.mua')

@section('content')

<div class="portfolio-page">

    {{-- HEADER --}}
    <div class="portfolio-header">

        <div>
            <h1 class="portfolio-title">
                Portfolio Saya
            </h1>

            <p class="portfolio-subtitle">
                Tampilkan hasil makeup terbaikmu agar client semakin percaya.
            </p>
        </div>

        <div class="portfolio-count">
            {{ $portfolios->count() }} Foto
        </div>

    </div>

    {{-- UPLOAD CARD --}}
    <div class="upload-card">

        <div class="upload-header">
            <div>
                <h2>Upload Portfolio Baru</h2>
                <p>Tambahkan hasil makeup terbaru untuk menarik lebih banyak client.</p>
            </div>
        </div>

        <form
            action="{{ route('mua.portfolio.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf

            <div class="form-grid">

                <div class="form-group full">
                    <label>Upload Foto</label>

                    <input
                        type="file"
                        name="images[]"
                        multiple
                        required
                    >
                </div>

                <div class="form-group">
                    <label>Judul Portfolio</label>

                    <input
                        type="text"
                        name="titles[]"
                        placeholder="Contoh: Wedding Makeup Elegant"
                    >
                </div>

                <div class="form-group">
                    <label>Kategori Makeup</label>

                    <select name="style_categories[]">
                        <option value="wedding">Wedding</option>
                        <option value="graduation">Graduation</option>
                        <option value="party">Party</option>
                    </select>
                </div>

                <div class="form-group full">
                    <label>Caption / Deskripsi</label>

                    <textarea
                        name="captions[]"
                        rows="4"
                        placeholder="Ceritakan detail makeup look ini..."
                    ></textarea>
                </div>

            </div>

            <button type="submit" class="upload-btn">
                Upload Portfolio
            </button>

        </form>

    </div>

    {{-- PORTFOLIO GRID --}}
    <div class="portfolio-grid">

        @forelse($portfolios as $p)

        <div class="portfolio-card">

            <div class="portfolio-image-wrap">

                <img
                    src="{{ asset('storage/' . $p->image_path) }}"
                    alt="{{ $p->title }}"
                    class="portfolio-image"
                >

                <div class="portfolio-overlay">
                    <span>{{ ucfirst($p->style_category) }}</span>
                </div>

            </div>

            <div class="portfolio-body">

                <div>
                    <h3 class="portfolio-name">
                        {{ $p->title }}
                    </h3>

                    <p class="portfolio-caption">
                        {{ $p->caption ?? 'Tidak ada deskripsi.' }}
                    </p>
                </div>

                <form
                    action="{{ route('mua.portfolio.destroy', $p->id) }}"
                    method="POST"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="delete-btn"
                    >
                        Hapus
                    </button>
                </form>

            </div>

        </div>

        @empty

        <div class="empty-state">

            <div class="empty-icon">
                ✨
            </div>

            <h3>Belum Ada Portfolio</h3>

            <p>
                Upload hasil makeup pertamamu untuk mulai membangun profile profesional.
            </p>

        </div>

        @endforelse

    </div>

</div>

<style>

    *{
        font-family: -apple-system,
        BlinkMacSystemFont,
        "SF Pro Display",
        "Segoe UI",
        sans-serif;
    }

    .portfolio-page{
        padding: 0.5rem;
    }

    .portfolio-header{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .portfolio-title{
        font-size: 2rem;
        font-weight: 700;
        color: #1f1f1f;
        margin-bottom: 0.3rem;
    }

    .portfolio-subtitle{
        color: #7b7b7b;
        font-size: 0.95rem;
    }

    .portfolio-count{
        background: #4f0404;
        color: white;
        padding: 0.7rem 1rem;
        border-radius: 14px;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .upload-card{
        background: white;
        border: 1px solid #ececec;
        border-radius: 24px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .upload-header h2{
        font-size: 1.2rem;
        margin-bottom: 0.3rem;
        color: #1f1f1f;
    }

    .upload-header p{
        color: #7b7b7b;
        font-size: 0.9rem;
        margin-bottom: 1.5rem;
    }

    .form-grid{
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .form-group{
        display: flex;
        flex-direction: column;
    }

    .form-group.full{
        grid-column: span 2;
    }

    .form-group label{
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
        font-weight: 600;
        color: #4a4a4a;
    }

    .form-group input,
    .form-group select,
    .form-group textarea{
        border: 1px solid #e7e7e7;
        border-radius: 14px;
        padding: 0.9rem 1rem;
        outline: none;
        font-size: 0.92rem;
        transition: 0.2s;
        background: #fff;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus{
        border-color: #c9a56e;
        box-shadow: 0 0 0 4px rgba(201,165,110,0.12);
    }

    .upload-btn{
        margin-top: 1.2rem;
        background: #4f0404;
        color: white;
        border: none;
        border-radius: 14px;
        padding: 0.9rem 1.4rem;
        font-size: 0.92rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.25s;
    }

    .upload-btn:hover{
        background: #6a0909;
        transform: translateY(-1px);
    }

    .portfolio-grid{
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .portfolio-card{
        background: white;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #ececec;
        transition: 0.25s;
    }

    .portfolio-card:hover{
        transform: translateY(-4px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.06);
    }

    .portfolio-image-wrap{
        position: relative;
        overflow: hidden;
    }

    .portfolio-image{
        width: 100%;
        height: 280px;
        object-fit: cover;
        display: block;
    }

    .portfolio-overlay{
        position: absolute;
        top: 14px;
        left: 14px;
    }

    .portfolio-overlay span{
        background: rgba(255,255,255,0.9);
        backdrop-filter: blur(10px);
        padding: 0.45rem 0.8rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4f0404;
    }

    .portfolio-body{
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .portfolio-name{
        font-size: 1rem;
        font-weight: 700;
        color: #1f1f1f;
        margin-bottom: 0.4rem;
    }

    .portfolio-caption{
        font-size: 0.85rem;
        color: #7b7b7b;
        line-height: 1.6;
    }

    .delete-btn{
        width: 100%;
        border: none;
        background: #fff2f2;
        color: #b42318;
        padding: 0.85rem;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 600;
        transition: 0.2s;
    }

    .delete-btn:hover{
        background: #ffe4e4;
    }

    .empty-state{
        background: white;
        border-radius: 24px;
        border: 1px dashed #d8d8d8;
        padding: 4rem 2rem;
        text-align: center;
        grid-column: 1/-1;
    }

    .empty-icon{
        font-size: 2rem;
        margin-bottom: 1rem;
    }

    .empty-state h3{
        margin-bottom: 0.5rem;
        color: #1f1f1f;
    }

    .empty-state p{
        color: #7b7b7b;
        font-size: 0.92rem;
    }

    @media(max-width: 768px){

        .form-grid{
            grid-template-columns: 1fr;
        }

        .form-group.full{
            grid-column: span 1;
        }

        .portfolio-image{
            height: 240px;
        }

    }

</style>

@endsection