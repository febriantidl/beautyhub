@extends('layouts.mua')

@section('title', 'Kelola Booking')

@section('content')

<header class="mb-8">
    <h1 style="
        font-size:2rem;
        font-weight:700;
        color:#2B2B2B;
        margin-bottom:.4rem;
    ">
        Kelola Booking
    </h1>

    <p style="
        color:#7A6F6E;
        font-size:.92rem;
    ">
        Kelola semua permintaan booking dari pelanggan Anda
    </p>
</header>

{{-- STATS --}}
<div style="
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:1.2rem;
    margin-bottom:2rem;
">

    <div style="
        background:white;
        border-radius:24px;
        padding:1.5rem;
        border:1px solid #EEE6E2;
        position:relative;
        overflow:hidden;
    ">
        <div style="
            position:absolute;
            top:0;
            left:0;
            right:0;
            height:5px;
            background:#D4A56A;
        "></div>

        <div style="
            width:52px;
            height:52px;
            border-radius:16px;
            background:#FFF6EA;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.4rem;
            margin-bottom:1rem;
        ">
            ⏳
        </div>

        <div style="
            font-size:2rem;
            font-weight:800;
            color:#2B2B2B;
            margin-bottom:.25rem;
        ">
            {{ $counts->pending ?? 0 }}
        </div>

        <div style="
            color:#7A6F6E;
            font-size:.88rem;
        ">
            Booking Pending
        </div>
    </div>

    <div style="
        background:white;
        border-radius:24px;
        padding:1.5rem;
        border:1px solid #EEE6E2;
        position:relative;
        overflow:hidden;
    ">
        <div style="
            position:absolute;
            top:0;
            left:0;
            right:0;
            height:5px;
            background:#5FA55F;
        "></div>

        <div style="
            width:52px;
            height:52px;
            border-radius:16px;
            background:#F1FFF1;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.4rem;
            margin-bottom:1rem;
        ">
            ✅
        </div>

        <div style="
            font-size:2rem;
            font-weight:800;
            color:#2B2B2B;
            margin-bottom:.25rem;
        ">
            {{ $counts->confirmed ?? 0 }}
        </div>

        <div style="
            color:#7A6F6E;
            font-size:.88rem;
        ">
            Booking Disetujui
        </div>
    </div>

    <div style="
        background:white;
        border-radius:24px;
        padding:1.5rem;
        border:1px solid #EEE6E2;
        position:relative;
        overflow:hidden;
    ">
        <div style="
            position:absolute;
            top:0;
            left:0;
            right:0;
            height:5px;
            background:#C9A56E;
        "></div>

        <div style="
            width:52px;
            height:52px;
            border-radius:16px;
            background:#FFF8EC;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:1.4rem;
            margin-bottom:1rem;
        ">
            🎉
        </div>

        <div style="
            font-size:2rem;
            font-weight:800;
            color:#2B2B2B;
            margin-bottom:.25rem;
        ">
            {{ $counts->completed ?? 0 }}
        </div>

        <div style="
            color:#7A6F6E;
            font-size:.88rem;
        ">
            Booking Selesai
        </div>
    </div>

</div>

{{-- FILTER --}}
<div style="
    background:white;
    border-radius:22px;
    padding:1rem 1.2rem;
    border:1px solid #EEE6E2;
    margin-bottom:1.5rem;
    display:flex;
    flex-wrap:wrap;
    align-items:center;
    gap:.75rem;
">

    <span style="
        font-weight:700;
        color:#2B2B2B;
        margin-right:.3rem;
    ">
        Filter:
    </span>

    @foreach([
        'all' => 'Semua',
        'pending' => 'Pending',
        'confirmed' => 'Disetujui',
        'cancelled' => 'Dibatalkan',
        'completed' => 'Selesai'
    ] as $val => $label)

    <a
        href="{{ route('mua.bookings.index', ['status' => $val]) }}"
        style="
            padding:.65rem 1.1rem;
            border-radius:999px;
            text-decoration:none;
            font-size:.83rem;
            font-weight:700;
            transition:.2s;
            {{ request('status', 'all') === $val
                ? 'background:#4f0404;color:white;'
                : 'background:#FAF7F5;color:#6F6463;border:1px solid #EEE6E2;'
            }}
        "
    >
        {{ $label }}
    </a>

    @endforeach

</div>

{{-- BOOKING LIST --}}
<div style="
    background:white;
    border-radius:28px;
    padding:2rem;
    border:1px solid #EEE6E2;
">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:1.8rem;
        flex-wrap:wrap;
        gap:1rem;
    ">
        <div>
            <h2 style="
                font-size:1.3rem;
                font-weight:700;
                color:#2B2B2B;
                margin-bottom:.2rem;
            ">
                Daftar Booking
            </h2>

            <p style="
                color:#8A7E7C;
                font-size:.85rem;
            ">
                Total {{ $bookings->total() }} booking ditemukan
            </p>
        </div>
    </div>

    @forelse($bookings as $booking)

    <div style="
        background:#FFFCFB;
        border:1px solid #F0E8E4;
        border-radius:22px;
        padding:1.3rem;
        margin-bottom:1rem;
        transition:.2s ease;
    ">

        <div style="
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:1rem;
            flex-wrap:wrap;
        ">

            {{-- LEFT --}}
            <div style="
                display:flex;
                align-items:center;
                gap:1rem;
                min-width:0;
                flex:1;
            ">

                <div style="
                    width:62px;
                    height:62px;
                    border-radius:18px;
                    background:linear-gradient(135deg,#4f0404,#C9A56E);
                    color:white;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-weight:700;
                    font-size:1.3rem;
                    flex-shrink:0;
                ">
                    {{ substr($booking->customer->name ?? 'C', 0, 1) }}
                </div>

                <div style="min-width:0;">

                    <div style="
                        font-weight:700;
                        font-size:1rem;
                        color:#2B2B2B;
                        margin-bottom:.35rem;
                    ">
                        {{ $booking->customer->name ?? 'Customer' }}
                    </div>

                    <div style="
                        display:flex;
                        flex-wrap:wrap;
                        gap:.8rem;
                        font-size:.82rem;
                        color:#7A6F6E;
                        line-height:1.7;
                    ">
                        <span>
                            📅
                            {{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }}
                        </span>

                        <span>
                            💄
                            {{ $booking->service->name ?? 'Service' }}
                        </span>
                    </div>

                </div>

            </div>

            {{-- RIGHT --}}
            <div style="
                display:flex;
                align-items:center;
                gap:.6rem;
                flex-wrap:wrap;
            ">

                @php
                    $statusColors = [
                        'pending' => ['#FFF7E8','#B7791F'],
                        'confirmed' => ['#EDF9ED','#2F7A2F'],
                        'completed' => ['#EEF6FF','#2563EB'],
                        'cancelled' => ['#FFF1F1','#B04A4A']
                    ];

                    $bg = $statusColors[$booking->status][0] ?? '#F3F3F3';
                    $color = $statusColors[$booking->status][1] ?? '#666';
                @endphp

                <span style="
                    padding:.5rem .9rem;
                    border-radius:999px;
                    font-size:.72rem;
                    font-weight:700;
                    text-transform:uppercase;
                    background:{{ $bg }};
                    color:{{ $color }};
                    letter-spacing:.04em;
                ">
                    {{ $booking->status }}
                </span>

                <a
                    href="{{ route('mua.bookings.show', $booking->id) }}"
                    style="
                        padding:.65rem 1rem;
                        border-radius:12px;
                        text-decoration:none;
                        border:1px solid #E5D9D6;
                        color:#4f0404;
                        font-size:.84rem;
                        font-weight:600;
                        background:white;
                    "
                >
                    Detail
                </a>

                @if($booking->status === 'pending')

                <form
                    action="{{ route('mua.bookings.approve', $booking->id) }}"
                    method="POST"
                >
                    @csrf

                    <button
                        type="submit"
                        style="
                            padding:.68rem 1rem;
                            border:none;
                            background:#4f0404;
                            color:white;
                            border-radius:12px;
                            cursor:pointer;
                            font-size:.83rem;
                            font-weight:700;
                        "
                    >
                        Terima
                    </button>

                </form>

                @endif

            </div>

        </div>

    </div>

    @empty

    <div style="
        text-align:center;
        padding:5rem 2rem;
    ">

        <div style="
            font-size:4rem;
            margin-bottom:1rem;
        ">
            📭
        </div>

        <h3 style="
            font-size:1.4rem;
            font-weight:700;
            color:#2B2B2B;
            margin-bottom:.6rem;
        ">
            Belum Ada Booking
        </h3>

        <p style="
            color:#7A6F6E;
            max-width:420px;
            margin:auto;
            line-height:1.7;
        ">
            Semua permintaan booking dari client akan muncul di halaman ini.
        </p>

    </div>

    @endforelse

    {{-- PAGINATION --}}
    <div style="
        margin-top:2rem;
        display:flex;
        justify-content:center;
    ">
        {{ $bookings->appends(['status' => request('status')])->links() }}
    </div>

</div>

@endsection