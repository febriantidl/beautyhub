@extends('layouts.mua')

@section('title', 'Kelola Booking')

@section('content')
<header class="mb-8">
    <h1 style="font-size: 2.25rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-dark);">Kelola Booking</h1>
    <p style="color: var(--text-gray);">Kelola semua permintaan booking dari pelanggan Anda</p>
</header>

{{-- Status Counts --}}
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
    <div style="background: white; padding: 1.5rem; border-radius: 20px; border: 1px solid var(--border-soft);">
        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">⏳</div>
        <div style="font-size: 1.75rem; font-weight: 800;">{{ $counts->pending ?? 0 }}</div>
        <div style="color: var(--text-gray); font-size: 0.9rem;">Pending</div>
    </div>
    <div style="background: white; padding: 1.5rem; border-radius: 20px; border: 1px solid var(--border-soft);">
        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">✅</div>
        <div style="font-size: 1.75rem; font-weight: 800;">{{ $counts->confirmed ?? 0 }}</div>
        <div style="color: var(--text-gray); font-size: 0.9rem;">Disetujui</div>
    </div>
    <div style="background: white; padding: 1.5rem; border-radius: 20px; border: 1px solid var(--border-soft);">
        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🎉</div>
        <div style="font-size: 1.75rem; font-weight: 800;">{{ $counts->completed ?? 0 }}</div>
        <div style="color: var(--text-gray); font-size: 0.9rem;">Selesai</div>
    </div>
</div>

{{-- Filter --}}
<div style="background: white; padding: 1rem; border-radius: 20px; border: 1px solid var(--border-soft); margin-bottom: 1.5rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
    <span style="font-weight: 600; color: var(--text-dark);">Filter:</span>
    @foreach(['all' => 'Semua', 'pending' => 'Pending', 'confirmed' => 'Disetujui', 'cancelled' => 'Dibatalkan', 'completed' => 'Selesai'] as $val => $label)
    <a href="{{ route('mua.bookings.index', ['status' => $val]) }}"
       style="padding: 0.5rem 1.2rem; border-radius: 9999px; font-size: 0.85rem; text-decoration: none; font-weight: 600;
              {{ request('status', 'all') === $val ? 'background: #D4A5A5; color: white;' : 'border: 1px solid var(--border-soft); color: var(--text-gray);' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- Bookings List --}}
<div style="background: white; padding: 2rem; border-radius: 20px; border: 1px solid var(--border-soft);">
    <h2 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1.5rem;">
        Daftar Booking ({{ $bookings->total() }})
    </h2>

    @forelse($bookings as $booking)
    <div style="display: flex; align-items: center; gap: 1.5rem; padding: 1.5rem; background: #fdfaf9; border-radius: 16px; margin-bottom: 1rem; border: 1px solid #f0e6e6;">
        <div style="width: 60px; height: 60px; border-radius: 16px; background: #D4A5A5; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.25rem;">
            {{ substr($booking->customer->name ?? 'C', 0, 1) }}
        </div>
        <div style="flex: 1;">
            <div style="font-weight: 700; color: var(--text-dark);">{{ $booking->customer->name ?? 'Customer' }}</div>
            <div style="font-size: 0.85rem; color: var(--text-gray);">📅 {{ \Carbon\Carbon::parse($booking->event_date)->format('d M Y') }} · 💄 {{ $booking->service->name ?? 'Service' }}</div>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <span style="padding: 0.4rem 1rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; background: #eee;">
                {{ $booking->status }}
            </span>
            <a href="{{ route('mua.bookings.show', $booking->id) }}" style="padding: 0.4rem 1rem; border: 1px solid #ddd; border-radius: 12px; text-decoration: none; color: #333; font-size: 0.85rem;">Detail</a>
            
            @if($booking->status === 'pending')
            <form action="{{ route('mua.bookings.approve', $booking->id) }}" method="POST">
                @csrf
                <button type="submit" style="padding: 0.4rem 1rem; border: none; background: #D4A5A5; color: white; border-radius: 12px; cursor: pointer;">Terima</button>
            </form>
            @endif
        </div>
    </div>
    @empty
    <p style="text-align: center; color: var(--text-gray); padding: 2rem;">Tidak ada data booking saat ini.</p>
    @endforelse

    {{-- PAGINATION LINK (WAJIB ADA) --}}
    <div style="margin-top: 2rem; display: flex; justify-content: center;">
        {{ $bookings->appends(['status' => request('status')])->links() }}
    </div>
</div>
@endsection