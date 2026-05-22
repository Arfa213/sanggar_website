@extends('admin.layouts.app')
@section('title', 'Kelola Kehadiran')

@section('content')

<div class="page-header" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px">
    <div class="page-header-text">
        <h1 style="font-family:var(--font-display);font-size:2rem;font-weight:900">Kelola Kehadiran</h1>
        <p style="color:var(--muted);font-size:.875rem">Cetak QR Code permanen atau input absensi manual.</p>
    </div>
    <a href="{{ route('admin.kehadiran.laporan') }}" class="btn btn-secondary" style="background:var(--dark);color:#fff;text-decoration:none;padding:10px 20px;border-radius:50px;font-weight:700;font-size:.875rem;display:flex;align-items:center;gap:8px">
        📊 Lihat Laporan
    </a>
</div>

{{-- STATISTIK HARI INI --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:28px">
    <div style="background:#fff;border-radius:14px;border:1px solid var(--border);padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:40px;height:40px;background:#E8F5E9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem">✓</div>
        <div>
            <div style="font-size:1.5rem;font-weight:900;color:#2E7D32">{{ $statsHariIni['hadir'] ?? 0 }}</div>
            <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;font-weight:700">Hadir</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid var(--border);padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:40px;height:40px;background:#FFF3E0;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem">~</div>
        <div>
            <div style="font-size:1.5rem;font-weight:900;color:#E65100">{{ $statsHariIni['izin'] ?? 0 }}</div>
            <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;font-weight:700">Izin</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid var(--border);padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:40px;height:40px;background:#FEF2F2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem">✗</div>
        <div>
            <div style="font-size:1.5rem;font-weight:900;color:#DC2626">{{ $statsHariIni['alpa'] ?? 0 }}</div>
            <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;font-weight:700">Alpa</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid var(--border);padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:40px;height:40px;background:#E8F4FD;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.1rem">👥</div>
        <div>
            <div style="font-size:1.5rem;font-weight:900;color:#1565C0">{{ $statsHariIni['tamu'] ?? 0 }}</div>
            <div style="font-size:.7rem;color:var(--muted);text-transform:uppercase;font-weight:700">Tamu</div>
        </div>
    </div>
</div>

{{-- TAB SWITCHER --}}
<div style="display:flex;gap:4px;margin-bottom:24px;background:#F5F3F1;border-radius:14px;padding:6px;width:fit-content;border:1px solid var(--border)">
    <button onclick="switchTab('permanent')" id="tab-permanent" class="tab-btn active">
        🔗 QR Utama
    </button>
    <button onclick="switchTab('guest')" id="tab-guest" class="tab-btn">
        👥 QR Tamu
    </button>
    <button onclick="switchTab('manual')" id="tab-manual" class="tab-btn">
        ✏️ Input Manual
    </button>
</div>

<style>
.tab-btn {
    padding: 10px 24px;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: .875rem;
    cursor: pointer;
    background: transparent;
    color: var(--muted);
    transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    gap: 8px;
}
.tab-btn:hover { background: rgba(0, 0, 0, 0.05); color: var(--dark); transform: translateY(-1px); }
.tab-btn.active {
    background: var(--dark) !important;
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>

{{-- ══ PANEL PERMANENT ══════════════════════════════════════ --}}
<div id="panel-permanent">
    <div style="background:#fff;border-radius:16px;border:1px solid var(--border);overflow:hidden;margin-bottom:24px">
        <div style="padding:18px 24px;border-bottom:1px solid var(--border);background:#FDF8F5">
            <h3 style="font-size:1rem;font-weight:700;color:var(--dark)">QR Code Universal Sanggar (Anggota)</h3>
            <p style="font-size:.8rem;color:var(--muted);margin-top:2px">Satu kode untuk semua anggota tetap & sementara. Scan via Dashboard.</p>
        </div>
        <div style="padding:24px">
            <table style="width:100%;border-collapse:collapse;font-size:.875rem">
                <tbody>
                    @foreach($permanentQR as $qr)
                    <tr>
                        <td style="padding:16px 0">
                            <span style="font-weight:700;color:var(--dark);font-size:1.1rem">🏮 QR Code Universal</span>
                        </td>
                        <td style="text-align:right">
                            <a href="{{ route('admin.kehadiran.permanent.show', $qr->id) }}" 
                               style="background:var(--primary);color:#fff;padding:12px 28px;border-radius:50px;text-decoration:none;font-weight:700;box-shadow:0 4px 12px var(--primary-pale)">
                                🖨️ Cetak QR Anggota
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══ PANEL GUEST QR ══════════════════════════════════════ --}}
<div id="panel-guest" style="display:none">
    <div style="background:#fff;border-radius:16px;border:1px solid var(--border);overflow:hidden;margin-bottom:24px">
        <div style="padding:18px 24px;border-bottom:1px solid var(--border);background:#F0F9FF">
            <h3 style="font-size:1rem;font-weight:700;color:var(--dark)">QR Code Khusus Tamu / Pengunjung</h3>
            <p style="font-size:.8rem;color:var(--muted);margin-top:2px">Bisa di-scan menggunakan Kamera HP Biasa (Tanpa Aplikasi/Login).</p>
        </div>
        <div style="padding:24px">
            <div style="display:flex;align-items:center;justify-content:space-between">
                <div>
                    <h4 style="font-size:1.1rem;font-weight:800">👥 QR Buku Tamu Digital</h4>
                    <p style="font-size:.875rem;color:var(--muted);margin-top:4px">Tempelkan ini di resepsionis atau gerbang masuk untuk tamu umum.</p>
                </div>
                <div style="display:flex;gap:12px">
                     {{-- Kita buatkan view khusus cetak tamu --}}
                    <a href="{{ route('admin.kehadiran.permanent.show', ['id' => $permanentQR->first()->id, 'type' => 'guest']) }}" 
                       style="background:var(--dark);color:#fff;padding:12px 28px;border-radius:50px;text-decoration:none;font-weight:700">
                        🖨️ Cetak QR Tamu
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ══ PANEL MANUAL ═════════════════════════════════════════ --}}
<div id="panel-manual" style="display:none">
    <div style="background:#fff;border-radius:16px;border:1px solid var(--border);overflow:hidden;margin-bottom:24px">
        <div style="padding:18px 24px;border-bottom:1px solid var(--border);background:#FAFAF8">
            <h3 style="font-size:1rem;font-weight:700;color:var(--dark)">Input Kehadiran Manual (Anggota)</h3>
            <p style="font-size:.8rem;color:var(--muted);margin-top:2px">Gunakan ini jika anggota lupa membawa HP.</p>
        </div>
        <div style="padding:24px">
            <form method="POST" action="{{ route('admin.kehadiran.input') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:16px;align-items:end">
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:var(--dark);display:block;margin-bottom:6px">Jadwal Latihan</label>
                        <select name="jadwal_id" required style="width:100%;padding:10px;border:1.5px solid var(--border);border-radius:10px;font-size:.875rem;background:#fff;outline:none">
                            @foreach($jadwal as $j)
                            <option value="{{ $j->id }}">{{ $j->hari }} · {{ $j->kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:var(--dark);display:block;margin-bottom:6px">Kegiatan</label>
                        <select name="tarian_id" required style="width:100%;padding:10px;border:1.5px solid var(--border);border-radius:10px;font-size:.875rem;background:#fff;outline:none">
                            @foreach($tarian as $t)
                            <option value="{{ $t->id }}">{{ $t->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:var(--dark);display:block;margin-bottom:6px">Tanggal</label>
                        <input type="date" name="tanggal" required value="{{ $today }}" style="width:100%;padding:10px;border:1.5px solid var(--border);border-radius:10px;font-size:.875rem;background:#fff;outline:none">
                    </div>
                    <button type="submit" style="background:var(--primary);color:#fff;font-weight:700;padding:11px 24px;border-radius:50px;border:none;cursor:pointer;font-size:.875rem">
                        Mulai Input →
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    document.getElementById('panel-manual').style.display  = tab === 'manual'  ? 'block' : 'none';
    document.getElementById('panel-permanent').style.display = tab === 'permanent' ? 'block' : 'none';
    document.getElementById('panel-guest').style.display = tab === 'guest' ? 'block' : 'none';
    
    const tabs = ['manual', 'permanent', 'guest'];
    tabs.forEach(t => {
        const btn = document.getElementById('tab-' + t);
        if (btn) {
            if(t === tab) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        }
    });
    sessionStorage.setItem('active_kehadiran_tab', tab);
}

// Inisialisasi tab aktif dari URL hash atau sessionStorage
const hash = window.location.hash.replace('#','');
const savedTab = sessionStorage.getItem('active_kehadiran_tab');
if (['permanent', 'guest', 'manual'].includes(hash)) {
    switchTab(hash);
} else if (['permanent', 'guest', 'manual'].includes(savedTab)) {
    switchTab(savedTab);
} else {
    switchTab('permanent');
}
</script>

@endsection