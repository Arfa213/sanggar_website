@extends('layouts.app')

@section('title', 'Dashboard Pengunjung')

@section('content')
<div class="guest-dashboard-page">
    <div class="container">
        
        <!-- Welcome Section -->
        <header class="guest-header">
            <div class="welcome-card">
                <div class="welcome-content">
                    <span class="badge badge--purple">Dashboard Anggota Sementara</span>
                    <h1>Halo, {{ explode(' ', $user->name)[0] }}! 👋</h1>
                    <p>Selamat datang kembali di Sanggar Mulya Bhakti. Berikut adalah jadwal sesi latihan Anda.</p>
                    
                    <div class="guest-stats">
                        <div class="stat-item">
                            <span class="stat-value">{{ $totalSesiBooking }}</span>
                            <span class="stat-label">Total Sesi</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">{{ $totalHadir }}</span>
                            <span class="stat-label">Sesi Hadir</span>
                        </div>
                        <div class="stat-item progress-stat">
                            <div class="circular-progress" style="--progress: {{ $persenHadir }}%">
                                <span class="progress-value">{{ $persenHadir }}%</span>
                            </div>
                            <span class="stat-label">Kehadiran</span>
                        </div>
                    </div>
                </div>
                <div class="welcome-image">
                    <img src="https://illustrations.popsy.co/amber/creative-work.svg" alt="Artistic Dance">
                </div>
            </div>
        </header>

        <div class="dashboard-grid">
            <!-- Sessions List -->
            <section class="sessions-section">
                <div class="section-header">
                    <h2>📅 Jadwal Latihan Anda</h2>
                    <p>Daftar sesi yang Anda ambil di Sanggar Mulya Bhakti.</p>
                </div>

                <div class="sessions-timeline">
                    @forelse($sesiBooking as $sesi)
                        @php 
                            $dateObj = \Carbon\Carbon::parse($sesi->tanggal_latihan);
                            $isPassed = $dateObj->isPast() && !$dateObj->isToday();
                            $statusLabel = $sesi->status === 'aktif' ? 'Terkonfirmasi' : ($sesi->status === 'nonaktif' ? 'Menunggu Admin' : 'Selesai');
                            $statusClass = $sesi->status === 'aktif' ? 'status--confirmed' : 'status--pending';
                        @endphp
                        
                        <div class="session-card {{ $isPassed ? 'session--passed' : '' }}">
                            <div class="session-date">
                                <span class="day">{{ $dateObj->format('d') }}</span>
                                <span class="month">{{ $dateObj->format('M') }}</span>
                            </div>
                            <div class="session-info">
                                <h3>{{ $sesi->tarian->nama ?? 'Sesi Latihan Khusus' }}</h3>
                                <div class="session-meta">
                                    <span>🕒 {{ $sesi->jam_latihan }}</span>
                                    <span>📍 Aula Sanggar</span>
                                </div>
                                @if($sesi->catatan)
                                    <p class="session-note">"{{ $sesi->catatan }}"</p>
                                @endif
                            </div>
                            <div class="session-status">
                                <span class="badge-status {{ $statusClass }}">
                                    {{ $isPassed ? 'Sudah Lewat' : $statusLabel }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-sessions">
                            <div style="text-align:center; padding: 40px; background:white; border-radius:24px;">
                                <p style="color:var(--muted); margin-bottom:20px;">Anda belum memiliki jadwal sesi latihan.</p>
                                <a href="{{ route('register') }}" class="btn-daftar" style="display:inline-block">Daftar Sesi Baru</a>
                            </div>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Sidebar Info -->
            <aside class="dashboard-sidebar">
                <div class="qr-quick-access">
                    <h3>Presensi Cepat</h3>
                    <p>Scan barcode di sanggar untuk mencatat kehadiran Anda pada sesi aktif.</p>
                    <button onclick="openScanner()" class="btn-scan" style="border:none; cursor:pointer;">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 7V5a2 2 0 0 1 2-2h2"/><path d="M17 3h2a2 2 0 0 1 2 2v2"/><path d="M21 17v2a2 2 0 0 1-2 2h-2"/><path d="M7 21H5a2 2 0 0 1-2-2v-2"/><rect x="7" y="7" width="10" height="10" rx="1"/></svg>
                        Buka Kamera & Scan
                    </button>
                </div>

                <div class="events-card">
                    <h3>Event Mendatang</h3>
                    <div class="mini-event-list">
                        @foreach($eventMendatang as $ev)
                        <div class="mini-event">
                            <div class="me-date">{{ \Carbon\Carbon::parse($ev->tanggal)->format('d M') }}</div>
                            <div class="me-info">
                                <strong>{{ $ev->nama_event }}</strong>
                                <small>{{ $ev->lokasi }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </aside>
        </div>

    </div>
</div>

<!-- Scanner Modal -->
<div id="scannerModal" class="modal-scanner" style="display:none">
    <div class="modal-scanner-content">
        <div class="modal-scanner-header">
            <h3>Scan Barcode Kelas</h3>
            <button onclick="closeScanner()" class="modal-close-btn">✕</button>
        </div>
        <div class="modal-scanner-body">
            <div id="reader-container">
                <div id="reader"></div>
                <div class="scanner-overlay">
                    <div class="scanner-line"></div>
                </div>
            </div>
            <div id="scan-feedback" class="scan-feedback"></div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let html5QrCode;

async function openScanner() {
    const modal = document.getElementById('scannerModal');
    modal.style.display = 'flex';
    
    const feedback = document.getElementById('scan-feedback');
    feedback.innerHTML = "Mencari kamera...";
    feedback.className = "scan-feedback";

    try {
        html5QrCode = new Html5Qrcode("reader");
        const config = { fps: 15, qrbox: { width: 250, height: 250 } };
        
        await html5QrCode.start(
            { facingMode: "environment" }, 
            config, 
            onScanSuccess
        );
        feedback.innerHTML = "Kamera aktif. Arahkan ke QR Code.";
    } catch (err) {
        console.error(err);
        feedback.innerHTML = "Gagal akses kamera. Pastikan izin kamera aktif.";
        feedback.className = "scan-feedback error";
    }
}

async function closeScanner() {
    if (html5QrCode && html5QrCode.isScanning) {
        await html5QrCode.stop();
    }
    document.getElementById('scannerModal').style.display = 'none';
}

function onScanSuccess(decodedText) {
    const feedback = document.getElementById('scan-feedback');
    feedback.innerHTML = "Memproses token...";
    feedback.className = "scan-feedback processing";

    fetch("{{ route('member.kehadiran.process') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ barcode_token: decodedText })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            feedback.innerHTML = "✓ " + data.message;
            feedback.className = "scan-feedback success";
            html5QrCode.stop();
            setTimeout(() => window.location.reload(), 2000);
        } else {
            feedback.innerHTML = "✕ " + data.message;
            feedback.className = "scan-feedback error";
        }
    })
    .catch(err => {
        feedback.innerHTML = "Error: Terjadi kesalahan sistem.";
        feedback.className = "scan-feedback error";
    });
}
</script>

<style>
/* Modal Scanner Styles */
.modal-scanner {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.85);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(8px);
}
.modal-scanner-content {
    background: white;
    width: 100%;
    max-width: 500px;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
}
.modal-scanner-header {
    padding: 20px;
    background: #1e1b4b;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-close-btn { background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
.modal-scanner-body { padding: 30px; text-align: center; }
#reader-container {
    position: relative;
    width: 100%;
    aspect-ratio: 1/1;
    background: #000;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 20px;
}
#reader { width: 100% !important; height: 100% !important; }
#reader video { object-fit: cover !important; }
.scanner-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10; }
.scanner-line {
    width: 100%; height: 2px;
    background: #f97316;
    position: absolute;
    top: 0;
    box-shadow: 0 0 15px #f97316;
    animation: scanMove 3s linear infinite;
}
@keyframes scanMove { 0% { top: 0; } 100% { top: 100%; } }
.scan-feedback { padding: 12px; border-radius: 12px; font-size: 0.9rem; font-weight: 600; background: #f5f5f5; }
.scan-feedback.success { background: #dcfce7; color: #15803d; }
.scan-feedback.error { background: #fee2e2; color: #dc2626; }
.scan-feedback.processing { background: #f3e8ff; color: #7e22ce; }

/* Dashboard Styles */
.guest-dashboard-page {
    padding-top: 100px;
    padding-bottom: 60px;
    background: #fbf9f7;
    min-height: 100vh;
}
.welcome-card {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
    border-radius: 30px;
    padding: 40px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    box-shadow: 0 20px 40px rgba(30, 27, 75, 0.2);
}
.welcome-content h1 { font-size: 2.5rem; font-weight: 800; margin: 15px 0; }
.guest-stats { display: flex; gap: 30px; margin-top: 30px; align-items: center; }
.stat-value { font-size: 1.8rem; font-weight: 800; }
.stat-label { font-size: 0.75rem; text-transform: uppercase; opacity: 0.6; }
.circular-progress {
    width: 60px; height: 60px; border-radius: 50%;
    background: conic-gradient(#f97316 var(--progress), rgba(255,255,255,0.1) 0deg);
    display: flex; align-items: center; justify-content: center; position: relative;
}
.circular-progress::before { content: ''; position: absolute; width: 48px; height: 48px; background: #25225d; border-radius: 50%; }
.progress-value { position: relative; font-size: 0.85rem; font-weight: 700; }
.welcome-image img { height: 180px; }
.dashboard-grid { display: grid; grid-template-columns: 1fr 340px; gap: 40px; }
.session-card {
    background: white; border-radius: 20px; padding: 24px 28px;
    display: flex; align-items: center; gap: 28px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 20px;
}
.session--passed { filter: grayscale(1); opacity: 0.6; background: #f1f1f1; }
.session-date {
    background: #fff7ed; color: #f97316; width: 70px; height: 70px;
    border-radius: 15px; display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.session-date .day { font-size: 1.5rem; font-weight: 800; }
.session-date .month { font-size: 0.75rem; font-weight: 700; }
.badge-status { padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
.status--confirmed { background: #dcfce7; color: #15803d; }
.status--pending { background: #f3e8ff; color: #7e22ce; }
.qr-quick-access, .events-card { background: white; padding: 24px; border-radius: 24px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
.btn-scan {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; padding: 14px; background: #f97316; color: white;
    border-radius: 12px; font-weight: 700; transition: 0.3s;
}
.btn-scan:hover { background: #ea580c; transform: scale(1.02); }
.mini-event { display: flex; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f5f5f5; }
.me-date { background: #fdf2f8; color: #db2777; font-size: 0.75rem; font-weight: 800; padding: 5px; border-radius: 8px; }
@media (max-width: 992px) { .dashboard-grid { grid-template-columns: 1fr; } .welcome-image { display: none; } }
</style>
@endsection
