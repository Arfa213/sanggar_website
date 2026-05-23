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
                        
                        <!-- 1. MASA AKTIF KEANGGOTAAN -->
                        @if($user->tgl_kadaluarsa)
                            @php
                                $sisaHari = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($user->tgl_kadaluarsa), false);
                                $isExpired = $sisaHari < 0;
                            @endphp
                            <div class="stat-item expiry-stat">
                                <span class="stat-value" style="color: {{ $isExpired ? '#dc2626' : ($sisaHari <= 2 ? '#ea580c' : '#15803d') }};">
                                    {{ $isExpired ? 'Hangus' : floor($sisaHari) . ' Hari' }}
                                </span>
                                <span class="stat-label">Sisa Aktif</span>
                            </div>
                        @endif
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
                            if ($sesi->status === 'aktif') {
                                $statusLabel = '✅ Terkonfirmasi';
                                $statusClass = 'status--confirmed';
                            } elseif ($sesi->status === 'ditolak') {
                                $statusLabel = '❌ Ditolak';
                                $statusClass = 'status--rejected';
                            } else {
                                $statusLabel = '⏳ Menunggu Admin';
                                $statusClass = 'status--pending';
                            }
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
                            <div class="session-status" style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                                <span class="badge-status {{ $statusClass }}">
                                    {{ $isPassed ? 'Sudah Lewat' : $statusLabel }}
                                </span>
                                
                                <!-- 2. TOMBOL BATALKAN SESI -->
                                @if(!$isPassed && in_array($sesi->status, ['aktif', 'pending']))
                                    <form action="{{ route('penjadwalan.batalkan', $sesi->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pendaftaran kelas ini?');">
                                        @csrf
                                        <button type="submit" class="btn-cancel-session">Batalkan</button>
                                    </form>
                                @endif
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

                {{-- FORM TAMBAH SESI BARU --}}
                <div class="qr-quick-access" style="border:2px solid #f97316;">
                    <h3 style="color:#c2410c;margin-bottom:8px">➕ Tambah Sesi Latihan</h3>
                    <p style="font-size:.85rem;color:#7c7c7c;margin-bottom:16px">Ingin latihan di waktu lain? Ajukan sesi baru di sini.</p>

                    @if(session('success'))
                        <div style="background:#dcfce7;color:#15803d;padding:10px;border-radius:10px;font-size:.85rem;font-weight:700;margin-bottom:12px">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div style="background:#fee2e2;color:#dc2626;padding:10px;border-radius:10px;font-size:.85rem;font-weight:700;margin-bottom:12px">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('dashboard.tambah-sesi') }}">
                        @csrf
                        <div style="margin-bottom:12px">
                            <label style="font-size:.8rem;font-weight:700;color:#374151;display:block;margin-bottom:5px">Tarian</label>
                            <select name="tarian_id" required style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.875rem;background:#fff">
                                <option value="">— Pilih Tarian —</option>
                                @foreach($tarianList as $t)
                                    <option value="{{ $t->id }}">{{ $t->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div style="margin-bottom:12px">
                            <label style="font-size:.8rem;font-weight:700;color:#374151;display:block;margin-bottom:5px">Tanggal</label>
                            <input type="date" name="tanggal" required min="{{ now()->toDateString() }}"
                                   style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.875rem;background:#fff">
                        </div>
                        <div style="margin-bottom:16px">
                            <label style="font-size:.8rem;font-weight:700;color:#374151;display:block;margin-bottom:5px">Jam Latihan</label>
                            <select name="jam" required style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:.875rem;background:#fff">
                                <option value="">— Pilih Jam —</option>
                                <option value="08:00">08:00</option>
                                <option value="10:00">10:00</option>
                                <option value="13:00">13:00</option>
                                <option value="15:00">15:00</option>
                                <option value="16:00">16:00</option>
                                <option value="17:00">17:00</option>
                                <option value="19:00">19:00</option>
                                <option value="20:00">20:00</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-scan" style="background:#f97316">
                            Ajukan Sesi Latihan →
                        </button>
                    </form>
                </div>

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
                            @forelse($eventMendatang as $ev)
                            <div class="mini-event">
                                <div class="me-date">{{ \Carbon\Carbon::parse($ev->tanggal)->format('d M') }}</div>
                                <div class="me-info">
                                    <strong>{{ $ev->nama_event }}</strong>
                                    <small>{{ $ev->lokasi }}</small>
                                </div>
                            </div>
                            @empty
                                <p style="color:var(--muted);font-size:.85rem">Belum ada event mendatang.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- 3. TOMBOL UPGRADE ANGGOTA TETAP -->
                    <div class="upgrade-card">
                        <div class="upgrade-icon">👑</div>
                        <h3>Suka latihan di sini?</h3>
                        <p>Dapatkan jadwal latihan rutin tetap, bebas antrean booking, dan prioritas aula dengan menjadi <strong>Anggota Tetap</strong>!</p>
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin,%20saya%20tertarik%20untuk%20upgrade%20akun%20saya%20menjadi%20Anggota%20Tetap." target="_blank" class="btn-upgrade">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.888-.788-1.487-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.015c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.052 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                            Hubungi Admin
                        </a>
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
.status--pending { background: #fff7ed; color: #c2410c; }
.status--rejected { background: #fee2e2; color: #dc2626; }
.qr-quick-access, .events-card { background: white; padding: 24px; border-radius: 24px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.03); }
.btn-scan {
    display: flex; align-items: center; justify-content: center; gap: 10px;
    width: 100%; padding: 14px; background: #f97316; color: white;
    border-radius: 12px; font-weight: 700; transition: 0.3s;
}
.btn-scan:hover { background: #ea580c; transform: scale(1.02); }

.btn-cancel-session {
    background: transparent; border: 1.5px solid #ef4444; color: #ef4444;
    padding: 6px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700;
    cursor: pointer; transition: all 0.2s ease;
}
.btn-cancel-session:hover { background: #ef4444; color: white; }

.upgrade-card {
    background: linear-gradient(135deg, #1e293b, #0f172a);
    padding: 24px; border-radius: 24px; margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.2);
    text-align: center; color: white;
}
.upgrade-card .upgrade-icon { font-size: 2.5rem; margin-bottom: 12px; }
.upgrade-card h3 { font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; color: white; }
.upgrade-card p { font-size: 0.85rem; color: #cbd5e1; margin-bottom: 20px; line-height: 1.5; }
.upgrade-card .btn-upgrade {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; padding: 12px; background: #22c55e; color: white;
    border-radius: 12px; font-weight: 700; transition: 0.3s;
    text-decoration: none;
}
.upgrade-card .btn-upgrade:hover { background: #16a34a; transform: translateY(-2px); }

.mini-event { display: flex; gap: 15px; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #f5f5f5; }
.me-date { background: #fdf2f8; color: #db2777; font-size: 0.75rem; font-weight: 800; padding: 5px; border-radius: 8px; }
@media (max-width: 992px) { .dashboard-grid { grid-template-columns: 1fr; } .welcome-image { display: none; } }
</style>
@endsection
