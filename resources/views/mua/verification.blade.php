@extends('layouts.mua')
@section('title', 'Verifikasi QR')

@section('content')
<script src="https://unpkg.com/html5-qrcode"></script>

<header class="mb-8">
    <h1 style="font-size: 2.2rem; font-weight: 700; margin-bottom: .45rem;">
        Verifikasi Kedatangan
    </h1>

    <p class="text-gray">
        Scan QR customer atau masukkan kode booking secara manual.
    </p>
</header>

<div class="verification-grid">

    {{-- LEFT --}}
    <div class="card verify-card">

        <div class="section-head">
            <div>
                <div class="section-badge">SCAN QR</div>
                <h2>Verifikasi Booking</h2>
            </div>
        </div>

        {{-- QR SCANNER --}}
        <div class="scanner-wrapper">
            <div id="reader"></div>
        </div>

        <div class="divider-line">
            <span>atau input manual</span>
        </div>

        {{-- INPUT --}}
        <div class="manual-input">
            <input
                type="text"
                id="verifyCode"
                placeholder="Masukkan kode booking..."
                maxlength="10"
            >

            <button
                onclick="processVerify(document.getElementById('verifyCode').value)"
                id="verifyBtn"
                class="btn btn-primary verify-btn"
            >
                Verifikasi Sekarang
            </button>
        </div>

        {{-- RESULT --}}
        <div id="verifyResult" class="verify-result"></div>
    </div>

    {{-- RIGHT --}}
    <div class="card history-card">

        <div class="section-head">
            <div>
                <div class="section-badge">RECENT</div>
                <h2>Riwayat Verifikasi</h2>
            </div>
        </div>

        @if(isset($recentVerifications) && count($recentVerifications))
            <div class="history-list">

                @foreach($recentVerifications as $item)
                <div class="history-item">

                    <div class="history-avatar">
                        {{ substr($item->customer->name ?? 'C', 0, 1) }}
                    </div>

                    <div class="history-info">
                        <div class="history-name">
                            {{ $item->customer->name ?? 'Customer' }}
                        </div>

                        <div class="history-meta">
                            {{ \Carbon\Carbon::parse($item->event_date)->format('d M Y') }}
                        </div>
                    </div>

                    <div class="history-status success">
                        Verified
                    </div>

                </div>
                @endforeach

            </div>
        @else
            <div class="empty-history">
                <div class="empty-icon">📷</div>
                <h3>Belum Ada Verifikasi</h3>
                <p>
                    Riwayat scan QR yang berhasil akan muncul di sini.
                </p>
            </div>
        @endif

    </div>

</div>

<style>

.verification-grid{
    display: grid;
    grid-template-columns: 1.15fr .85fr;
    gap: 1.5rem;
    align-items: start;
}

.verify-card,
.history-card{
    border-radius: 26px;
    border: 1px solid #EFE7E3;
    padding: 1.8rem;
    background: #fff;
}

.section-head{
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.4rem;
}

.section-head h2{
    font-size: 1.2rem;
    font-weight: 700;
    color: #2B2B2B;
}

.section-badge{
    display: inline-block;
    padding: .35rem .7rem;
    border-radius: 999px;
    background: rgba(79,4,4,.08);
    color: #4f0404;
    font-size: .7rem;
    font-weight: 700;
    letter-spacing: .08em;
    margin-bottom: .6rem;
}

.scanner-wrapper{
    overflow: hidden;
    border-radius: 22px;
    border: 1px solid #EEE4DF;
    background: #FAF8F6;
    padding: 1rem;
}

#reader{
    width: 100%;
}

.divider-line{
    position: relative;
    text-align: center;
    margin: 1.4rem 0;
}

.divider-line::before{
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    width: 100%;
    height: 1px;
    background: #EEE4DF;
}

.divider-line span{
    position: relative;
    z-index: 2;
    background: #fff;
    padding: 0 .9rem;
    font-size: .8rem;
    color: #9A8D8A;
}

.manual-input{
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.manual-input input{
    width: 100%;
    padding: 1rem 1.1rem;
    border-radius: 16px;
    border: 1.5px solid #E8E2DD;
    font-size: 1rem;
    outline: none;
    transition: .2s ease;
    text-align: center;
    text-transform: uppercase;
    font-family: inherit;
}

.manual-input input:focus{
    border-color: #4f0404;
    box-shadow: 0 0 0 4px rgba(79,4,4,.08);
}

.verify-btn{
    width: 100%;
    padding: 1rem;
    border-radius: 16px;
    font-size: .95rem;
}

.verify-result{
    display: none;
    margin-top: 1.4rem;
    border-radius: 18px;
    padding: 1rem 1.1rem;
    font-size: .92rem;
    line-height: 1.7;
}

.history-list{
    display: flex;
    flex-direction: column;
    gap: .9rem;
}

.history-item{
    display: flex;
    align-items: center;
    gap: .9rem;
    padding: 1rem;
    border-radius: 18px;
    background: #FAF8F6;
    border: 1px solid #F0E8E4;
}

.history-avatar{
    width: 50px;
    height: 50px;
    border-radius: 16px;
    background: linear-gradient(135deg, #4f0404, #8E3C3C);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    flex-shrink: 0;
}

.history-info{
    flex: 1;
    min-width: 0;
}

.history-name{
    font-size: .93rem;
    font-weight: 700;
    color: #2B2B2B;
    margin-bottom: .2rem;
}

.history-meta{
    font-size: .8rem;
    color: #8B7E7B;
}

.history-status{
    padding: .45rem .85rem;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 700;
}

.history-status.success{
    background: rgba(88,166,107,.12);
    color: #2F7A43;
}

.empty-history{
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon{
    font-size: 3rem;
    margin-bottom: .8rem;
}

.empty-history h3{
    font-size: 1.1rem;
    margin-bottom: .4rem;
}

.empty-history p{
    color: #8B7E7B;
    font-size: .9rem;
    max-width: 260px;
    margin: auto;
    line-height: 1.7;
}

@media(max-width: 980px){

    .verification-grid{
        grid-template-columns: 1fr;
    }

}

</style>
@endsection

@push('scripts')
<script>

function onScanSuccess(decodedText) {
    document.getElementById('verifyCode').value = decodedText;
    processVerify(decodedText);
    html5QrcodeScanner.clear();
}

let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    {
        fps: 10,
        qrbox: {
            width: 240,
            height: 240
        }
    }
);

html5QrcodeScanner.render(onScanSuccess);

async function processVerify(code) {

    if (!code) return;

    const btn = document.getElementById('verifyBtn');

    btn.disabled = true;
    btn.innerHTML = 'Memverifikasi...';

    try {

        const res = await fetch('{{ route("mua.verification.verify") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                verification_code: code
            }),
        });

        const json = await res.json();

        showResult(json.success, json.message);

        if(json.success){
            setTimeout(() => {
                location.reload();
            }, 1800);
        }

    } catch(error){

        showResult(false, 'Terjadi kesalahan. Silakan coba lagi.');

    }

    btn.disabled = false;
    btn.innerHTML = 'Verifikasi Sekarang';
}

function showResult(success, html){

    const el = document.getElementById('verifyResult');

    el.style.display = 'block';
    el.innerHTML = html;

    if(success){
        el.style.background = 'rgba(88,166,107,.10)';
        el.style.color = '#2F7A43';
        el.style.border = '1px solid rgba(88,166,107,.25)';
    } else {
        el.style.background = 'rgba(183,110,110,.10)';
        el.style.color = '#A03F3F';
        el.style.border = '1px solid rgba(183,110,110,.25)';
    }
}

</script>
@endpush