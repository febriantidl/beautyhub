@extends('layouts.mua')
@section('title', 'Verifikasi QR')

@section('content')
<script src="https://unpkg.com/html5-qrcode"></script>

<header class="mb-8">
    <h1 class="text-4xl font-bold mb-2">Verifikasi Kedatangan</h1>
    <p class="text-gray">Gunakan kamera untuk scan QR atau masukkan kode manual</p>
</header>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
    
    {{-- Verify Section --}}
    <div class="card" style="padding: 2rem;">
        <h2 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.5rem;">📷 Scan atau Input Kode</h2>
        
        {{-- CAMERA SCANNER --}}
        <div id="reader" style="width: 100%; border-radius: 16px; overflow: hidden; margin-bottom: 1.5rem;"></div>

        {{-- MANUAL INPUT --}}
        <div style="margin-bottom: 1.25rem;">
            <input type="text" id="verifyCode" placeholder="Atau ketik kode manual..." maxlength="10"
                   style="width: 100%; padding: 1rem; border: 2px solid #E8E2DD; border-radius: 16px; text-align: center; font-size: 1.2rem; text-transform: uppercase;">
        </div>

        <button onclick="processVerify(document.getElementById('verifyCode').value)" id="verifyBtn" class="btn btn-primary" style="width: 100%; padding: 1rem; border-radius: 16px;">✅ Verifikasi Sekarang</button>
        <div id="verifyResult" style="display: none; margin-top: 1.5rem; padding: 1.25rem; border-radius: 16px;"></div>
    </div>

    {{-- Recent Verifications --}}
    <div class="card">
        <h2 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 1.25rem;">📋 Riwayat Verifikasi</h2>
        {{-- List lo tetap di sini (sama kayak kode sebelumnya) --}}
    </div>
</div>
@endsection

@push('scripts')
<script>
// FUNGSI SCANNER
function onScanSuccess(decodedText) {
    document.getElementById('verifyCode').value = decodedText;
    processVerify(decodedText);
    html5QrcodeScanner.clear(); // Stop kamera setelah dapet
}

let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: {width: 250, height: 250} });
html5QrcodeScanner.render(onScanSuccess);

// FUNGSI PROSES KE CONTROLLER
async function processVerify(code) {
    if (!code) return;
    const btn = document.getElementById('verifyBtn');
    btn.disabled = true;
    
    const res = await fetch('{{ route("mua.verification.verify") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ verification_code: code }),
    });
    
    const json = await res.json();
    showResult(json.success, json.message);
    if(json.success) setTimeout(() => location.reload(), 2000);
    btn.disabled = false;
}

function showResult(success, html) {
    const el = document.getElementById('verifyResult');
    el.style.display = 'block';
    el.innerHTML = html;
    el.style.background = success ? 'rgba(127,168,127,0.1)' : 'rgba(183,110,110,0.1)';
}
</script>
@endpush