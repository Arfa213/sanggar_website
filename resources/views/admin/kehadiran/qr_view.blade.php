@extends('admin.layouts.app')

@section('title', 'Cetak QR Code')

@section('content')
@php
    $isGuest = request()->get('type') === 'guest';
    $scanUrl = $isGuest ? route('tamu.index') : $qr->barcode_token;
    $title = $isGuest ? 'BUKU TAMU DIGITAL' : 'PRESENSI KEHADIRAN ANGGOTA';
    $subtitle = $isGuest ? 'UNTUK PENGUNJUNG / TAMU' : 'UNTUK SELURUH KELAS & TARIAN';
@endphp

<div class="d-print-none" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
    <a href="{{ route('admin.kehadiran.index') }}" style="text-decoration: none; color: var(--muted); font-weight: 700; font-size: .875rem; display: flex; align-items: center; gap: 8px;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        Kembali
    </a>
    <button onclick="window.print()" style="background: var(--dark); color: #fff; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
        Cetak Sekarang
    </button>
</div>

<div style="display: flex; justify-content: center;">
    <div id="printable-qr" style="width: 100%; max-width: 600px; background: #fff; padding: 60px; border: 2px solid #000; border-radius: 0; text-align: center; position: relative;">
        
        <div style="font-family: var(--font-display); font-size: 2.5rem; font-weight: 900; color: #000; margin-bottom: 10px; line-height: 1;">
            SANGGAR MULYA BHAKTI
        </div>
        <div style="font-size: 1rem; color: #555; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 40px; border-bottom: 2px solid #000; padding-bottom: 20px;">
            {{ $title }}
        </div>

        <div style="margin-bottom:60px;">
            <div style="font-size: .875rem; color: #777; font-weight: 700; margin-bottom: 5px;">{{ $isGuest ? 'SCAN UNTUK MENGISI' : 'UNTUK SELURUH' }}</div>
            <div style="font-family: var(--font-display); font-size: 2.5rem; font-weight: 900; color: #000; line-height: 1.2; text-transform: uppercase;">
                {{ $subtitle }}
            </div>
        </div>

        <!-- THE QR CODE -->
        <div style="display: flex; justify-content: center; margin-bottom: 30px;">
            <div id="qrcode" style="padding: 20px; background: #fff; border: 5px solid #000; display: inline-block;">
                <!-- qrcode.js will render here -->
            </div>
        </div>

        @if(!$isGuest)
        <div style="margin: 0 auto 30px auto; width: 300px; text-align: left; font-size: 0.85rem; color: #666;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                <strong>QR Code Kadaluarsa dalam:</strong>
                <span id="countdown-text">10s</span>
            </div>
            <div style="width: 100%; height: 6px; background: #eee; border-radius: 3px; overflow: hidden;">
                <div id="progress-bar" style="width: 100%; height: 100%; background: #2E7D32; transition: width 1s linear;"></div>
            </div>
        </div>
        @endif

        <div style="margin-top: 40px; border-top: 2px dashed #ccc; padding-top: 30px;">
            <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 10px;">CARA ABSENSI:</div>
            <ol style="text-align: left; display: inline-block; font-size: .9rem; line-height: 1.6; color: #444;">
                @if($isGuest)
                    <li>Arahkan Kamera HP Anda ke QR Code di atas</li>
                    <li>Klik link yang muncul (Buka di Browser)</li>
                    <li>Isi Nama dan Tujuan Anda di form yang muncul</li>
                    <li>Klik <strong>Simpan Kehadiran</strong></li>
                @else
                    <li>Buka Website Sanggar di HP Anda & Login</li>
                    <li>Pilih Menu <strong>Dashboard</strong></li>
                    <li>Klik tombol <strong>"Scan Kehadiran"</strong></li>
                    <li>Arahkan kamera ke QR Code di atas</li>
                @endif
            </ol>
        </div>

        <div style="position: absolute; bottom: 20px; right: 20px; font-size: .7rem; color: #ccc;">
            Source: {{ $scanUrl }}
        </div>
    </div>
</div>

@push('styles')
<style>
    @media print {
        .admin-main { padding: 0 !important; margin: 0 !important; background: #fff !important; }
        .admin-content { padding: 0 !important; }
        .topbar, .sidebar, .sidebar-overlay, .d-print-none { display: none !important; }
        body { background: #fff !important; }
        #printable-qr { border: none !important; width: 100% !important; max-width: 100% !important; padding: 40px !important; }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    window.onload = function() {
        var isGuest = {{ $isGuest ? 'true' : 'false' }};
        var qrUrl = "{{ $scanUrl }}";
        
        var qrcode = new QRCode(document.getElementById("qrcode"), {
            text: qrUrl,
            width: 300,
            height: 300,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        if (!isGuest) {
            var timeLeft = 10;
            var countdownText = document.getElementById('countdown-text');
            var progressBar = document.getElementById('progress-bar');
            
            function updateProgress() {
                timeLeft--;
                if(timeLeft < 0) {
                    timeLeft = 10;
                    fetchNewToken();
                }
                countdownText.textContent = timeLeft + 's';
                progressBar.style.width = (timeLeft / 10 * 100) + '%';
                
                // Ubah warna jika sisa waktu sedikit
                if(timeLeft <= 3) {
                    progressBar.style.background = '#d32f2f'; // Merah
                } else {
                    progressBar.style.background = '#2E7D32'; // Hijau
                }
            }

            function fetchNewToken() {
                fetch("{{ route('admin.kehadiran.dynamic-qr-token') }}")
                    .then(response => response.json())
                    .then(data => {
                        qrcode.clear();
                        qrcode.makeCode(data.token);
                    })
                    .catch(error => console.error('Error fetching token:', error));
            }

            // Mulai loop timer
            fetchNewToken();
            setInterval(updateProgress, 1000);
        }
    };
</script>
@endpush
@endsection
