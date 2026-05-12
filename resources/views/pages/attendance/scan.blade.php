@extends('layouts.member')

@section('title', 'Scan Kehadiran')

@section('content')
<div class="m-page-header">
    <div class="m-badge">Presensi Digital</div>
    <h1>Scan Kehadiran</h1>
    <p>Arahkan kamera Anda ke QR Code di kelas untuk mencatat kehadiran.</p>
</div>

<div style="display: flex; justify-content: center; margin-top: 20px;">
    <div style="width: 100%; max-width: 500px; background: #fff; border-radius: 20px; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
        <div style="padding: 20px; background: var(--dark); color: #fff; text-align: center;">
            <div style="font-size: .75rem; font-weight: 700; opacity: .7; text-transform: uppercase; letter-spacing: 1px;">Kamera Siap</div>
        </div>
        
        <div style="padding: 30px; text-align: center;">
            <!-- Scanner Container -->
            <div id="reader-container" style="position: relative; width: 100%; aspect-ratio: 1/1; background: #f9f9f9; border-radius: 15px; overflow: hidden; border: 2px solid var(--border); margin-bottom: 25px;">
                <div id="reader" style="width: 100%;"></div>
                
                <!-- Overlay Scanning Animation -->
                <div id="scan-overlay" style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;">
                    <div class="scan-line"></div>
                </div>

                <div id="placeholder-info" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: var(--muted); text-align: center; width: 80%;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 15px; opacity: .3;"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                    <div style="font-size: .875rem;">Klik tombol di bawah untuk mengaktifkan kamera</div>
                </div>
            </div>

            <div id="result-message" style="display: none; padding: 15px; border-radius: 12px; margin-bottom: 20px; font-size: .875rem; font-weight: 600;"></div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
                <button id="start-btn" style="width: 100%; background: var(--primary); color: #fff; border: none; padding: 16px; border-radius: 12px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: all .2s; box-shadow: 0 4px 12px rgba(198,93,46,0.2);">
                    Mulai Kamera
                </button>
                <a href="{{ route('dashboard') }}" style="text-decoration: none; color: var(--muted); font-size: .875rem; font-weight: 600; padding: 10px;">
                    Batal & Kembali
                </a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    #reader video {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .scan-line {
        height: 3px;
        background: var(--primary);
        position: absolute;
        width: 100%;
        top: 0;
        left: 0;
        box-shadow: 0 0 15px 5px rgba(198,93,46, 0.4);
        animation: scan 2.5s linear infinite;
    }
    @keyframes scan {
        0% { top: 0; }
        100% { top: 100%; }
    }
    #reader__dashboard_section_csr button {
        display: none !important; /* Hide default html5-qrcode buttons */
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrCode;
    const startBtn = document.getElementById('start-btn');
    const scanOverlay = document.getElementById('scan-overlay');
    const placeholderInfo = document.getElementById('placeholder-info');
    const resultMsg = document.getElementById('result-message');

    function onScanSuccess(decodedText, decodedResult) {
        html5QrCode.stop().then(() => {
            scanOverlay.style.display = 'none';
            processAttendance(decodedText);
        }).catch((err) => console.error(err));
    }

    function processAttendance(token) {
        resultMsg.style.display = 'block';
        resultMsg.style.background = 'var(--primary-pale)';
        resultMsg.style.color = 'var(--primary)';
        resultMsg.innerHTML = 'Memproses kehadiran...';

        fetch("{{ route('member.kehadiran.process') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ token: token })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                resultMsg.style.background = '#F0FDF4';
                resultMsg.style.color = '#15803D';
                resultMsg.innerHTML = '✓ ' + data.message;
                setTimeout(() => window.location.href = "{{ route('dashboard') }}", 2000);
            } else {
                resultMsg.style.background = '#FEF2F2';
                resultMsg.style.color = '#DC2626';
                resultMsg.innerHTML = '✕ ' + data.message;
                startBtn.style.display = 'block';
            }
        })
        .catch(error => {
            resultMsg.style.background = '#FEF2F2';
            resultMsg.style.color = '#DC2626';
            resultMsg.innerHTML = 'Terjadi kesalahan sistem.';
            startBtn.style.display = 'block';
        });
    }

    startBtn.addEventListener('click', function() {
        startBtn.style.display = 'none';
        placeholderInfo.style.display = 'none';
        scanOverlay.style.display = 'block';
        
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 15, qrbox: { width: 250, height: 250 } };

        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess)
            .catch(err => {
                startBtn.style.display = 'block';
                scanOverlay.style.display = 'none';
                placeholderInfo.style.display = 'block';
                alert("Kamera tidak dapat diakses.");
            });
    });
</script>
@endpush
@endsection
