@extends('layouts.member')
@section('title', 'Dashboard Saya')

@section('content')

{{-- WELCOME HEADER --}}
<div class="m-page-header" style="display:flex;align-items:center;gap:20px;margin-bottom:30px">
    @if($user->foto)
        <img src="{{ asset('storage/'.$user->foto) }}" style="width:80px;height:80px;border-radius:20px;object-fit:cover;box-shadow:var(--shadow-md);border:3px solid #fff">
    @else
        <div style="width:80px;height:80px;background:var(--primary);color:#fff;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:900;box-shadow:var(--shadow-md);border:3px solid #fff;font-family:'Playfair Display',serif">
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
    @endif
    <div>
        <div class="m-badge">Member Area</div>
        <h1 style="margin-top:4px">Halo, {{ explode(' ', $user->name)[0] }}! 👋</h1>
        <p>Selamat datang di dashboard anggota Sanggar Mulya Bhakti.</p>
    </div>
</div>

{{-- STAT CARDS --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px">

    <div style="background:#fff;border-radius:14px;border:1px solid #E8E0D8;padding:18px;display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;background:#FDF0EA;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900;color:#1A1A1A;font-family:'Playfair Display',serif;line-height:1">{{ $jadwalAktif->count() }}</div>
            <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">Kelas Aktif</div>
        </div>
    </div>

    <div style="background:#fff;border-radius:14px;border:1px solid #E8E0D8;padding:18px;display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;background:#E8F5E9;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900;color:#2E7D32;font-family:'Playfair Display',serif;line-height:1">{{ $hadir }}</div>
            <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">Hadir Bulan Ini</div>
        </div>
    </div>

    <div style="background:#fff;border-radius:14px;border:1px solid #E8E0D8;padding:18px;display:flex;align-items:center;gap:14px">
        @php $pColor = $persenHadir >= 75 ? '#2E7D32' : ($persenHadir >= 50 ? '#E65100' : '#DC2626');
             $pBg    = $persenHadir >= 75 ? '#E8F5E9' : ($persenHadir >= 50 ? '#FFF3E0' : '#FEF2F2'); @endphp
        <div style="width:44px;height:44px;background:{{ $pBg }};border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $pColor }}" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900;color:{{ $pColor }};font-family:'Playfair Display',serif;line-height:1">{{ $persenHadir }}%</div>
            <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">Tingkat Kehadiran</div>
        </div>
    </div>

    <div style="background:#fff;border-radius:14px;border:1px solid #E8E0D8;padding:18px;display:flex;align-items:center;gap:14px">
        <div style="width:44px;height:44px;background:#E8F4FD;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1565C0" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
            <div style="font-size:1.6rem;font-weight:900;color:#1565C0;font-family:'Playfair Display',serif;line-height:1">{{ $totalHadirAll }}</div>
            <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">Total Kehadiran</div>
        </div>
    </div>

</div>

{{-- GRID UTAMA --}}
<div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

<div>

    {{-- JADWAL AKTIF --}}
    <div style="background:#fff;border-radius:20px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:24px;box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        <div style="padding:20px 28px;border-bottom:1px solid #F0EBE5;display:flex;align-items:center;justify-content:space-between;background:#fcfcfc;">
            <div>
                <div style="font-size:.65rem;font-weight:800;color:#C65D2E;letter-spacing:1px;text-transform:uppercase;margin-bottom:2px">LATIHAN SAYA</div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.15rem;font-weight:800;color:#1A1A1A">Kelas yang Sedang Diikuti</h3>
            </div>
            <a href="{{ route('penjadwalan') }}"
               style="background:#C65D2E;color:#fff;font-size:.75rem;font-weight:700;padding:8px 16px;border-radius:50px;text-decoration:none;display:flex;align-items:center;gap:6px">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kelas
            </a>
        </div>

        @if($jadwalAktif->isEmpty())
        <div style="padding:60px 40px;text-align:center">
            <div style="width:72px;height:72px;background:#FDF0EA;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <p style="font-weight:800;color:#1A1A1A;margin-bottom:6px;font-size:1.1rem">Belum ada kelas terdaftar</p>
            <p style="font-size:.875rem;color:#7A7A7A;margin-bottom:24px;max-width:300px;margin-inline:auto;">Pilih tarian yang ingin kamu pelajari dan daftarkan diri!</p>
            <a href="{{ route('penjadwalan') }}"
               style="display:inline-block;background:#C65D2E;color:#fff;font-size:.875rem;font-weight:700;padding:12px 28px;border-radius:50px;text-decoration:none">
                Pilih Kelas Tari →
            </a>
        </div>
        @else
        <div style="padding: 10px 0;">
            @foreach($jadwalAktif as $p)
            <div style="display:flex;align-items:center;gap:18px;padding:16px 28px;border-bottom:1px solid #FAFAF8">
                {{-- Hari box --}}
                <div style="width:56px;height:56px;background:#C65D2E;border-radius:14px;display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0">
                    <span style="color:#fff;font-size:.65rem;font-weight:800;letter-spacing:.5px;text-transform:uppercase">{{ strtoupper(substr($p->jadwal->hari,0,3)) }}</span>
                    <span style="color:rgba(255,255,255,.7);font-size:.6rem;font-weight:700">{{ $p->jadwal->jam_mulai }}</span>
                </div>
                <div style="flex:1;min-width:0">
                    <div style="font-weight:800;font-size:1rem;color:#1A1A1A;margin-bottom:2px">{{ $p->tarian->nama }}</div>
                    <div style="font-size:.8rem;color:#7A7A7A;font-weight:500;">
                        📍 {{ $p->jadwal->tempat }}
                        &nbsp;·&nbsp;
                        ⏰ <span style="color:var(--dark)">{{ $p->jadwal->jam_mulai }}–{{ $p->jadwal->jam_selesai }}</span>
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:12px;flex-shrink:0">
                    <span style="background:#E8F5E9;color:#2E7D32;font-size:.7rem;font-weight:800;padding:5px 12px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px">Aktif</span>
                    <form method="POST" action="{{ route('penjadwalan.batalkan', $p->id) }}">
                        @csrf
                        <button type="submit" onclick="return confirm('Batalkan pendaftaran Tari {{ $p->tarian->nama }}?')"
                            style="background:none;border:1px solid #FECACA;color:#DC2626;font-size:.7rem;font-weight:800;padding:6px 12px;border-radius:10px;cursor:pointer;transition:all .2s;"
                            onmouseover="this.style.background='#FEF2F2'" onmouseout="this.style.background='none'">
                            Batal
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ABSENSI TERAKHIR --}}
    @if($absensiTerakhir->count())
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:20px">
        <div style="padding:16px 20px;border-bottom:1px solid #F0EBE5;display:flex;align-items:center;justify-content:space-between">
            <div>
                <div style="font-size:.65rem;font-weight:700;color:#C65D2E;letter-spacing:1px;text-transform:uppercase;margin-bottom:2px">REKAP</div>
                <h3 style="font-family:'Playfair Display',serif;font-size:1.05rem;font-weight:700">Absensi Terakhir</h3>
            </div>
            <a href="{{ route('penjadwalan.kehadiran') }}"
               style="font-size:.78rem;color:#C65D2E;font-weight:700;text-decoration:none">
                Lihat semua →
            </a>
        </div>
        @foreach($absensiTerakhir as $ab)
        @php
            $abColor = ['hadir'=>'#2E7D32','izin'=>'#E65100','alpa'=>'#DC2626'][$ab->status] ?? '#7A7A7A';
            $abBg    = ['hadir'=>'#E8F5E9','izin'=>'#FFF3E0','alpa'=>'#FEF2F2'][$ab->status]  ?? '#F3F4F6';
            $abIcon  = ['hadir'=>'✓','izin'=>'~','alpa'=>'✗'][$ab->status] ?? '?';
        @endphp
        <div style="display:flex;align-items:center;gap:12px;padding:11px 20px;border-bottom:1px solid #FAFAF8">
            <div style="width:32px;height:32px;background:{{ $abBg }};border-radius:8px;display:flex;align-items:center;justify-content:center;font-weight:900;color:{{ $abColor }};font-size:.9rem;flex-shrink:0">
                {{ $abIcon }}
            </div>
            <div style="flex:1;min-width:0">
                <div style="font-size:.85rem;font-weight:600;color:#1A1A1A">{{ $ab->tarian->nama }}</div>
                <div style="font-size:.75rem;color:#7A7A7A">{{ $ab->tanggal->isoFormat('D MMM YYYY') }} · {{ $ab->jadwal->hari }}</div>
            </div>
            <span style="font-size:.75rem;font-weight:700;color:{{ $abColor }}">{{ ucfirst($ab->status) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- AI DANCE CONSULTANT --}}
    <div style="background: linear-gradient(135deg, #1A1A1A 0%, #333333 100%); border-radius: 16px; padding: 24px; color: #fff; position: relative; overflow: hidden; margin-bottom: 24px;">
        <div style="position: relative; z-index: 2;">
            <div style="font-size: .65rem; font-weight: 700; color: #C65D2E; letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 8px;">PANDUAN PINTAR</div>
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; margin-bottom: 8px; line-height: 1.2;">Bingung Pilih Latihan Tari Apa?</h3>
            <p style="font-size: .875rem; opacity: .8; margin-bottom: 20px; max-width: 80%;">Tanya AI kami untuk mendapatkan rekomendasi tarian yang paling cocok dengan karakter dan keinginanmu.</p>
            
            <div style="display: flex; gap: 10px; background: rgba(255,255,255,0.1); padding: 6px; border-radius: 50px; border: 1px solid rgba(255,255,255,0.2);">
                <input type="text" id="aiPreference" placeholder="Contoh: Saya suka tarian yang enerjik dan gagah..." 
                    style="flex: 1; background: transparent; border: none; padding: 10px 18px; color: #fff; font-size: .875rem; outline: none;">
                <button onclick="getAiRecommendation()" id="btnAiRecommend" style="background: #C65D2E; color: #fff; border: none; padding: 10px 24px; border-radius: 50px; font-weight: 700; cursor: pointer; transition: all .2s;">
                    Tanya AI
                </button>
            </div>

            <div id="aiResult" style="display: none; margin-top: 20px; padding: 20px; background: rgba(198, 93, 46, 0.1); border: 1px solid rgba(198, 93, 46, 0.3); border-radius: 12px; animation: fadeIn .5s ease;">
                <div style="font-weight: 800; font-size: 1.1rem; color: #C65D2E; margin-bottom: 8px;" id="aiTarian"></div>
                <p style="font-size: .875rem; line-height: 1.6; color: #eee; margin-bottom: 15px;" id="aiAlasan"></p>
                <a href="{{ route('penjadwalan') }}" style="display: inline-block; color: #fff; text-decoration: none; font-size: .8rem; font-weight: 700; border-bottom: 2px solid #C65D2E; padding-bottom: 2px;">Daftar Kelas Ini Sekarang →</a>
            </div>
        </div>

        {{-- Dekorasi background --}}
        <div style="position: absolute; right: -20px; bottom: -20px; opacity: 0.1; transform: rotate(-15deg);">
            <svg width="150" height="150" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
        </div>
    </div>

    <script>
        function getAiRecommendation() {
            const pref = document.getElementById('aiPreference').value;
            const btn = document.getElementById('btnAiRecommend');
            const resultDiv = document.getElementById('aiResult');
            
            if (!pref) return alert('Beri tahu kami keinginanmu dulu ya!');

            btn.disabled = true;
            btn.innerText = 'Menganalisis...';
            
            fetch("{{ route('chatbot.recommend') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ preference: pref })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('aiTarian').innerText = '✨ Rekomendasi: ' + data.tarian;
                    document.getElementById('aiAlasan').innerText = data.alasan;
                    resultDiv.style.display = 'block';
                    resultDiv.scrollIntoView({ behavior: 'smooth' });
                } else {
                    alert('Maaf, AI sedang sibuk. Coba lagi nanti ya!');
                }
            })
            .catch(() => alert('Terjadi kesalahan. Pastikan koneksi internet lancar.'))
            .finally(() => {
                btn.disabled = false;
                btn.innerText = 'Tanya AI';
            });
        }
    </script>

    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>

</div>

{{-- SIDEBAR KANAN --}}
<div>
    {{-- SCAN KEHADIRAN (PRIMARY ACTION) --}}
    <button onclick="openScanner()" 
       style="width:100%;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:12px;background:#1A1A1A;color:#fff;border-radius:16px;padding:18px;margin-bottom:16px;text-decoration:none;transition:all .3s;box-shadow:0 10px 20px rgba(0,0,0,.1)"
       onmouseover="this.style.transform='translateY(-3px)';this.style.background='#333'"
       onmouseout="this.style.transform='translateY(0)';this.style.background='#1A1A1A'">
        <div style="width:40px;height:40px;background:rgba(255,255,255,.1);border-radius:12px;display:flex;align-items:center;justify-content:center">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        </div>
        <div style="text-align:left">
            <div style="font-size:1rem;font-weight:900;line-height:1.2">Scan Kehadiran</div>
            <div style="font-size:.7rem;opacity:.6;margin-top:2px">Masuk kelas via Kamera</div>
        </div>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-left:auto;opacity:.5"><polyline points="9 18 15 12 9 6"/></svg>
    </button>
...
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
            <div id="reader-container-dash">
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
        feedback.innerHTML = "Gagal akses kamera: " + err;
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
/* Scanner Modal Styles */
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
    max-width: 480px;
    border-radius: 24px;
    overflow: hidden;
    animation: modalPop 0.3s ease;
}
@keyframes modalPop { from { opacity: 0; transform: scale(0.9); } to { opacity: 1; transform: scale(1); } }
.modal-scanner-header {
    padding: 18px 24px;
    background: #1A1A1A;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.modal-close-btn { background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
.modal-scanner-body { padding: 24px; text-align: center; }
#reader-container-dash {
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
    background: #C65D2E;
    position: absolute;
    top: 0;
    box-shadow: 0 0 15px #C65D2E;
    animation: scanMove 3s linear infinite;
}
@keyframes scanMove { 0% { top: 0; } 100% { top: 100%; } }
.scan-feedback { padding: 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; background: #f5f5f5; }
.scan-feedback.success { background: #E8F5E9; color: #2E7D32; }
.scan-feedback.error { background: #FEF2F2; color: #DC2626; }
.scan-feedback.processing { background: #FDF0EA; color: #C65D2E; }
</style>

    {{-- Kehadiran bulan ini --}}
    <div style="background:#C65D2E;border-radius:16px;padding:20px;color:#fff;margin-bottom:16px">
        <div style="font-size:.65rem;font-weight:700;letter-spacing:1px;opacity:.7;text-transform:uppercase;margin-bottom:10px">KEHADIRAN BULAN INI</div>
        @if($totalLatihan > 0)
        <div style="font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:900;line-height:1;margin-bottom:4px">{{ $persenHadir }}%</div>
        <div style="font-size:.8rem;opacity:.75;margin-bottom:14px">dari {{ $totalLatihan }} sesi latihan</div>
        {{-- Progress bar --}}
        <div style="height:8px;background:rgba(255,255,255,.2);border-radius:4px;overflow:hidden;margin-bottom:14px">
            <div style="height:100%;background:#fff;border-radius:4px;width:{{ $persenHadir }}%;transition:width .6s"></div>
        </div>
        <div style="display:flex;flex-direction:column;gap:7px">
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.8rem">
                <span style="opacity:.8">✓ Hadir</span>
                <span style="font-weight:700">{{ $hadir }} sesi</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.8rem">
                <span style="opacity:.8">~ Izin</span>
                <span style="font-weight:700">{{ $izin }} sesi</span>
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:.8rem">
                <span style="opacity:.8">✗ Alpa</span>
                <span style="font-weight:700">{{ $alpa }} sesi</span>
            </div>
        </div>
        @else
        <p style="opacity:.75;font-size:.875rem;margin-top:6px">Belum ada sesi latihan bulan ini.</p>
        @endif
    </div>

    {{-- Event mendatang --}}
    @if($eventMendatang->count())
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:16px">
        <div style="padding:14px 18px;border-bottom:1px solid #F0EBE5">
            <div style="font-size:.65rem;font-weight:700;color:#C65D2E;letter-spacing:1px;text-transform:uppercase;margin-bottom:2px">AKAN DATANG</div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700">Event Mendatang</h3>
        </div>
        @foreach($eventMendatang as $ev)
        <div style="display:flex;align-items:center;gap:12px;padding:12px 18px;border-bottom:1px solid #FAFAF8">
            <div style="background:#C65D2E;color:#fff;border-radius:10px;padding:6px 10px;text-align:center;flex-shrink:0;min-width:46px">
                <div style="font-size:1.1rem;font-weight:900;line-height:1">{{ $ev->tanggal->format('d') }}</div>
                <div style="font-size:.6rem;font-weight:700;opacity:.8">{{ strtoupper($ev->tanggal->isoFormat('MMM')) }}</div>
            </div>
            <div style="min-width:0">
                <div style="font-size:.825rem;font-weight:700;color:#1A1A1A;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $ev->nama }}</div>
                <div style="font-size:.75rem;color:#7A7A7A;margin-top:2px">📍 {{ Str::limit($ev->lokasi, 25) }}</div>
            </div>
        </div>
        @endforeach
        <div style="padding:10px 18px">
            <a href="{{ route('event') }}" style="font-size:.78rem;color:#C65D2E;font-weight:700;text-decoration:none">Lihat semua event →</a>
        </div>
    </div>
    @endif

    {{-- UJIAN SAYA --}}
    @if($ujianSaya->count() > 0)
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:16px">
        <div style="padding:14px 18px;border-bottom:1px solid #F0EBE5;background: linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff">
            <div style="font-size:.65rem;font-weight:700;letter-spacing:1px;opacity:.8;text-transform:uppercase;margin-bottom:2px">UJIAN MIDHANG SORE</div>
            <h3 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700;margin:0">Status Pendaftaran Ujian</h3>
        </div>
        <div style="padding:10px 14px;display:flex;flex-direction:column;gap:8px">
            @foreach($ujianSaya as $uj)
            <div style="padding:12px;background:#FAFAF8;border:1px solid #F0EBE5;border-radius:10px">
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:6px">
                    <div>
                        <div style="font-size:.825rem;font-weight:700;color:#1A1A1A">{{ $uj->tarian->nama }}</div>
                        <div style="font-size:.7rem;color:#7A7A7A">{{ $uj->event->nama }}</div>
                    </div>
                    @php
                        $badgeStyle = match($uj->status) {
                            'menunggu' => 'background:#FFF3E0;color:#E65100',
                            'diterima' => 'background:#E8F5E9;color:#2E7D32',
                            'ditolak'  => 'background:#FEF2F2;color:#DC2626',
                            default    => 'background:#F5F5F5;color:#666',
                        };
                        $badgeLabel = match($uj->status) {
                            'menunggu' => '⏳ Menunggu',
                            'diterima' => '✓ Diterima',
                            'ditolak'  => '✗ Ditolak',
                            default    => $uj->status,
                        };
                    @endphp
                    <span style="font-size:.65rem;font-weight:800;padding:3px 8px;border-radius:20px;{{ $badgeStyle }}">{{ $badgeLabel }}</span>
                </div>

                {{-- Tampilkan nilai jika sudah ada rapor --}}
                @if($uj->rapor)
                <div style="margin-top:8px;padding:8px 10px;background:{{ $uj->rapor->lulus ? '#E8F5E9' : '#FFF8E1' }};border-radius:8px">
                    <div style="font-size:.72rem;color:#555;margin-bottom:2px">Nilai Ujian</div>
                    <div style="font-size:1rem;font-weight:900;color:{{ $uj->rapor->lulus ? '#2E7D32' : '#E65100' }}">
                        {{ $uj->rapor->nilai_akhir }} — {{ $uj->rapor->predikat }}
                    </div>
                    <div style="font-size:.7rem;color:{{ $uj->rapor->lulus ? '#2E7D32' : '#E65100' }};font-weight:700">
                        {{ $uj->rapor->lulus ? '🎉 LULUS' : '📚 Belum Lulus' }}
                    </div>
                    @if($uj->rapor->lulus)
                    <a href="{{ route('ujian.sertifikat', $uj->rapor->id) }}" target="_blank"
                       style="display:inline-flex;align-items:center;gap:5px;margin-top:8px;font-size:.75rem;font-weight:700;color:#4f46e5;text-decoration:none;padding:5px 10px;background:#EEF2FF;border-radius:6px">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download Sertifikat
                    </a>
                    @endif
                </div>
                @endif

                {{-- Catatan admin jika ditolak --}}
                @if($uj->status === 'ditolak' && $uj->catatan_admin)
                <div style="margin-top:6px;font-size:.72rem;color:#b91c1c;padding:6px 8px;background:#FEF2F2;border-radius:6px">
                    <strong>Catatan Admin:</strong> {{ $uj->catatan_admin }}
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quick actions --}}
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden">
        <div style="padding:14px 18px;border-bottom:1px solid #F0EBE5">
            <h3 style="font-family:'Playfair Display',serif;font-size:1rem;font-weight:700">Aksi Cepat</h3>
        </div>
        <div style="padding:10px 14px;display:flex;flex-direction:column;gap:6px">
            <a href="{{ route('penjadwalan') }}"
               style="display:flex;align-items:center;gap:10px;padding:11px 12px;background:#FAFAF8;border:1px solid #F0EBE5;border-radius:10px;text-decoration:none;transition:all .15s"
               onmouseover="this.style.borderColor='#C65D2E'" onmouseout="this.style.borderColor='#F0EBE5'">
                <div style="width:34px;height:34px;background:#FDF0EA;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div style="font-size:.825rem;font-weight:700;color:#1A1A1A">Daftar Kelas Baru</div>
                    <div style="font-size:.72rem;color:#7A7A7A">Pilih tarian & jadwal</div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ADADAD" stroke-width="2" style="margin-left:auto"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="{{ route('penjadwalan.kehadiran') }}"
               style="display:flex;align-items:center;gap:10px;padding:11px 12px;background:#FAFAF8;border:1px solid #F0EBE5;border-radius:10px;text-decoration:none;transition:all .15s"
               onmouseover="this.style.borderColor='#C65D2E'" onmouseout="this.style.borderColor='#F0EBE5'">
                <div style="width:34px;height:34px;background:#E8F5E9;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2E7D32" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div>
                    <div style="font-size:.825rem;font-weight:700;color:#1A1A1A">Riwayat Kehadiran</div>
                    <div style="font-size:.72rem;color:#7A7A7A">Lihat rekap absensi</div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ADADAD" stroke-width="2" style="margin-left:auto"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
            <a href="{{ route('digital-archive') }}"
               style="display:flex;align-items:center;gap:10px;padding:11px 12px;background:#FAFAF8;border:1px solid #F0EBE5;border-radius:10px;text-decoration:none;transition:all .15s"
               onmouseover="this.style.borderColor='#C65D2E'" onmouseout="this.style.borderColor='#F0EBE5'">
                <div style="width:34px;height:34px;background:#E8F4FD;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1565C0" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                </div>
                <div>
                    <div style="font-size:.825rem;font-weight:700;color:#1A1A1A">Arsip Digital</div>
                    <div style="font-size:.72rem;color:#7A7A7A">Jelajahi tarian tradisional</div>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#ADADAD" stroke-width="2" style="margin-left:auto"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        </div>
    </div>

</div>
</div>

@endsection
