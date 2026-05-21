@extends('layouts.mua')
@section('title', 'Verifikasi QR')

@section('content')
<header class="mb-8">
    <h1 class="text-4xl font-bold mb-2">Verifikasi Kedatangan</h1>
    <p class="text-gray">Scan atau masukkan kode verifikasi dari pelanggan</p>
</header>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">

    {{-- Verify Form --}}
    <div class="card" style="padding: 2rem;">
        <h2 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem;">🔍 Masukkan Kode</h2>

        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="width: 100px; height: 100px; border-radius: 24px; background: linear-gradient(135deg, #FAE5E5, #F5D4C9); margin: 0 auto 1.5rem; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                📱
            </div>
            <p style="font-size: 0.875rem; color: #6B6160; line-height: 1.6;">
                Minta pelanggan menunjukkan kode verifikasi yang diterima setelah booking disetujui.
            </p>
        </div>

        <div style="margin-bottom: 1.25rem;">
            <label style="display: block; font-weight: 700; margin-bottom: 0.75rem; font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6B6160;">Kode Verifikasi</label>
            <input type="text" id="verifyCode"
                   placeholder="Contoh: AB3KF7XZ"
                   maxlength="10"
                   style="width: 100%; padding: 1rem; border: 2px solid #E8E2DD; border-radius: 16px; font-size: 1.5rem; font-family: 'Playfair Display', serif; text-align: center; text-transform: uppercase; letter-spacing: 0.2em; transition: border-color 0.2s;"
                   oninput="this.value = this.value.toUpperCase()"
                   onfocus="this.style.borderColor='#D4A5A5'"
                   onblur="this.style.borderColor='#E8E2DD'">
        </div>

        <button onclick="verifyCode()" id="verifyBtn"
                class="btn btn-primary"
                style="width: 100%; padding: 1rem; font-size: 1rem; border-radius: 16px;">
            ✅ Verifikasi Sekarang
        </button>

        {{-- Result --}}
        <div id="verifyResult" style="display: none; margin-top: 1.5rem; padding: 1.25rem; border-radius: 16px;"></div>
    </div>

    {{-- Recent Verifications --}}
    <div class="card">
        <h2 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.25rem;">📋 Verifikasi Hari Ini</h2>
        @forelse($recentVerifications as $booking)
        <div style="display: flex; align-items: center; gap: 0.875rem; padding: 0.875rem; background: rgba(127,168,127,0.05); border-radius: 14px; margin-bottom: 0.625rem; border: 1px solid rgba(127,168,127,0.2);">
            <div style="width: 44px; height: 44px; border-radius: 12px; background: linear-gradient(135deg, #A8CCA8, #7FA87F); display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; color: white; flex-shrink: 0;">
                {{ substr($booking->customer->name ?? '?', 0, 1) }}
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 700; font-size: 0.9rem; color: #2C2423;">{{ $booking->customer->name ?? 'N/A' }}</div>
                <div style="font-size: 0.78rem; color: #6B6160;">{{ $booking->service?->name ?? 'Custom' }} · {{ $booking->time_slot ?? '-' }}</div>
                <div style="font-size: 0.72rem; color: #9B8F8E; margin-top: 1px;">✅ {{ $booking->verified_at?->format('H:i') }}</div>
            </div>
            <div style="font-weight: 700; font-size: 0.82rem; color: #3E7A3E; background: rgba(127,168,127,0.12); padding: 0.25rem 0.6rem; border-radius: 8px;">
                VERIFIED
            </div>
        </div>
        @empty
        <div style="text-align: center; padding: 2rem; color: #9B8F8E;">
            <div style="font-size: 2.5rem; margin-bottom: 0.75rem;">📭</div>
            <p style="font-size: 0.875rem;">Belum ada verifikasi hari ini</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

async function verifyCode() {
    const code = document.getElementById('verifyCode').value.trim();
    if (code.length < 6) {
        showResult(false, 'Kode harus minimal 6 karakter.');
        return;
    }

    const btn = document.getElementById('verifyBtn');
    btn.disabled = true;
    btn.textContent = '⏳ Memverifikasi...';

    try {
        const res = await fetch('{{ route("mua.verification.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ verification_code: code }),
        });
        const json = await res.json();

        if (json.success) {
            const d = json.data;
            showResult(true, `
                <div style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.75rem;">✅ ${json.message}</div>
                <table style="width: 100%; font-size: 0.875rem; border-collapse: collapse;">
                    <tr><td style="padding: 3px 0; color: #6B6160; width: 40%;">Pelanggan</td><td style="font-weight: 700;">${d.customer}</td></tr>
                    <tr><td style="padding: 3px 0; color: #6B6160;">Layanan</td><td style="font-weight: 600;">${d.service}</td></tr>
                    <tr><td style="padding: 3px 0; color: #6B6160;">Tanggal</td><td>${d.event_date}</td></tr>
                    <tr><td style="padding: 3px 0; color: #6B6160;">Waktu</td><td>${d.time_slot}</td></tr>
                    <tr><td style="padding: 3px 0; color: #6B6160;">Lokasi</td><td>${d.location}</td></tr>
                </table>
            `);
            document.getElementById('verifyCode').value = '';

            // Auto reload recent list after 2s
            setTimeout(() => location.reload(), 2500);
        } else {
            showResult(false, json.message || 'Verifikasi gagal.');
        }
    } catch (e) {
        showResult(false, 'Terjadi kesalahan. Coba lagi.');
    } finally {
        btn.disabled = false;
        btn.textContent = '✅ Verifikasi Sekarang';
    }
}

function showResult(success, html) {
    const el = document.getElementById('verifyResult');
    el.style.display = 'block';
    el.style.background = success ? 'rgba(127,168,127,0.08)' : 'rgba(183,110,110,0.08)';
    el.style.border = `1px solid ${success ? 'rgba(127,168,127,0.3)' : 'rgba(183,110,110,0.3)'}`;
    el.style.color = success ? '#2E5A2E' : '#7A2E2E';
    el.innerHTML = html;
}

// Enter key trigger
document.getElementById('verifyCode').addEventListener('keydown', e => {
    if (e.key === 'Enter') verifyCode();
});
</script>
@endpush
