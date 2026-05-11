@extends('admin.layouts.app')
@section('title', 'Kelola Kehadiran')

@section('content')

@if(session('success'))
<div style="background:#F0FDF4;border:1px solid #86EFAC;border-radius:12px;padding:14px 20px;margin-bottom:20px;color:#15803D;display:flex;align-items:center;gap:10px">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="page-header">
    <div class="page-header-text">
        <h1>Kelola Kehadiran</h1>
        <p>Buat sesi barcode atau input manual kehadiran anggota.</p>
    </div>
    <a href="{{ route('admin.kehadiran.laporan') }}" class="btn btn-secondary">
        📊 Lihat Laporan
    </a>
</div>

{{-- STATISTIK HARI INI --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px">
    <div style="background:#fff;border-radius:14px;border:1px solid #F0EBE5;padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:44px;height:44px;background:#E8F5E9;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.25rem">✓</div>
        <div>
            <div style="font-size:1.75rem;font-weight:900;color:#2E7D32">{{ $statsHariIni['hadir'] ?? 0 }}</div>
            <div style="font-size:.8rem;color:#7A7A7A">Hadir Hari Ini</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid #F0EBE5;padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:44px;height:44px;background:#FFF3E0;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.25rem">~</div>
        <div>
            <div style="font-size:1.75rem;font-weight:900;color:#E65100">{{ $statsHariIni['izin'] ?? 0 }}</div>
            <div style="font-size:.8rem;color:#7A7A7A">Izin Hari Ini</div>
        </div>
    </div>
    <div style="background:#fff;border-radius:14px;border:1px solid #F0EBE5;padding:20px;display:flex;align-items:center;gap:16px">
        <div style="width:44px;height:44px;background:#FEF2F2;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.25rem">✗</div>
        <div>
            <div style="font-size:1.75rem;font-weight:900;color:#DC2626">{{ $statsHariIni['alpa'] ?? 0 }}</div>
            <div style="font-size:.8rem;color:#7A7A7A">Alpa Hari Ini</div>
        </div>
    </div>
</div>

{{-- TAB SWITCHER --}}
<div style="display:flex;gap:0;margin-bottom:24px;background:#F5F3F1;border-radius:12px;padding:4px;width:fit-content">
    <button onclick="switchTab('barcode')" id="tab-barcode"
        style="padding:8px 20px;border:none;border-radius:10px;font-weight:700;font-size:.875rem;cursor:pointer;background:#C65D2E;color:#fff;transition:all .2s">
        📷 Sesi Barcode (QR)
    </button>
    <button onclick="switchTab('manual')" id="tab-manual"
        style="padding:8px 20px;border:none;border-radius:10px;font-weight:700;font-size:.875rem;cursor:pointer;background:transparent;color:#7A7A7A;transition:all .2s">
        ✏️ Input Manual
    </button>
</div>

{{-- ══ PANEL BARCODE ════════════════════════════════════════ --}}
<div id="panel-barcode">
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:24px">
        <div style="padding:18px 24px;border-bottom:1px solid #F0EBE5;background:#FDF8F5">
            <h3 style="font-size:1rem;font-weight:700;color:#1A1A1A">Buat Sesi Kehadiran Barcode</h3>
            <p style="font-size:.8rem;color:#7A7A7A;margin-top:2px">Admin membuat sesi → QR Code ditampilkan → Anggota scan menggunakan HP.</p>
        </div>
        <div style="padding:24px">
            <form method="POST" action="{{ route('admin.kehadiran.buat-sesi') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:16px;align-items:end">
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:#1A1A1A;display:block;margin-bottom:6px">Jadwal Latihan <span style="color:#C65D2E">*</span></label>
                        <select name="jadwal_id" required style="width:100%;padding:10px 14px;border:1.5px solid #E8E0D8;border-radius:10px;font-size:.875rem;background:#FAF8F6;outline:none">
                            <option value="">-- Pilih jadwal --</option>
                            @foreach($jadwal as $j)
                            <option value="{{ $j->id }}">{{ $j->hari }} · {{ $j->jam_mulai }}–{{ $j->jam_selesai }} · {{ $j->kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:#1A1A1A;display:block;margin-bottom:6px">Kelas / Kegiatan <span style="color:#C65D2E">*</span></label>
                        <select name="tarian_id" required style="width:100%;padding:10px 14px;border:1.5px solid #E8E0D8;border-radius:10px;font-size:.875rem;background:#FAF8F6;outline:none">
                            <option value="">-- Pilih kegiatan --</option>
                            @foreach($tarian as $t)
                            <option value="{{ $t->id }}">{{ $t->nama }} ({{ ucfirst($t->jenis_kegiatan ?? 'tari') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:#1A1A1A;display:block;margin-bottom:6px">Tanggal <span style="color:#C65D2E">*</span></label>
                        <input type="date" name="tanggal" required value="{{ $today }}" style="width:100%;padding:10px 14px;border:1.5px solid #E8E0D8;border-radius:10px;font-size:.875rem;background:#FAF8F6;outline:none">
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:#1A1A1A;display:block;margin-bottom:6px">Durasi Sesi (menit)</label>
                        <input type="number" name="durasi" value="120" min="15" max="480" style="width:100%;padding:10px 14px;border:1.5px solid #E8E0D8;border-radius:10px;font-size:.875rem;background:#FAF8F6;outline:none">
                    </div>
                    <button type="submit"
                        style="background:#C65D2E;color:#fff;font-weight:700;padding:11px 24px;border-radius:50px;border:none;cursor:pointer;white-space:nowrap;font-size:.875rem">
                        📷 Buat QR Code →
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sesi aktif hari ini --}}
    @if($sesiAktif->count())
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden">
        <div style="padding:16px 24px;border-bottom:1px solid #F0EBE5">
            <h3 style="font-size:1rem;font-weight:700;color:#1A1A1A">🟢 Sesi Barcode Aktif Hari Ini</h3>
        </div>
        <div style="padding:8px 24px 16px">
            @foreach($sesiAktif as $s)
            @php $expired = $s->expires_at && now()->isAfter($s->expires_at); @endphp
            <div style="display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #FAF8F6">
                <div style="width:40px;height:40px;background:{{ $expired ? '#FEF2F2' : '#E8F5E9' }};border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem">
                    {{ $expired ? '⏰' : '📷' }}
                </div>
                <div style="flex:1">
                    <div style="font-weight:700;font-size:.875rem">{{ $s->jadwal->hari }} · {{ $s->tarian->nama }}</div>
                    <div style="font-size:.75rem;color:#7A7A7A">
                        Berakhir: {{ $s->expires_at ? $s->expires_at->format('H:i') : '-' }}
                        @if($expired) <span style="color:#DC2626;font-weight:700"> · SUDAH BERAKHIR</span>@endif
                    </div>
                </div>
                <a href="{{ route('admin.kehadiran.sesi', $s->id) }}"
                    style="background:#C65D2E;color:#fff;font-weight:700;padding:7px 18px;border-radius:20px;font-size:.8rem;text-decoration:none">
                    Lihat QR
                </a>
                <form method="POST" action="{{ route('admin.kehadiran.tutup-sesi', $s->id) }}" style="display:inline">
                    @csrf
                    <button type="submit" onclick="return confirm('Tutup sesi ini?')"
                        style="background:#F3F4F6;color:#DC2626;font-weight:700;padding:7px 14px;border-radius:20px;border:1px solid #FECACA;font-size:.8rem;cursor:pointer">
                        Tutup
                    </button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @else
    <div style="background:#FAFAF8;border-radius:16px;border:1.5px dashed #E8E0D8;padding:40px;text-align:center;color:#7A7A7A">
        <div style="font-size:2.5rem;margin-bottom:8px">📷</div>
        <p style="font-weight:600;margin-bottom:4px">Belum ada sesi barcode aktif hari ini</p>
        <p style="font-size:.8rem">Buat sesi di atas untuk mulai absensi scan QR.</p>
    </div>
    @endif
</div>

{{-- ══ PANEL MANUAL ═════════════════════════════════════════ --}}
<div id="panel-manual" style="display:none">
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden;margin-bottom:24px">
        <div style="padding:18px 24px;border-bottom:1px solid #F0EBE5;background:#FAFAF8">
            <h3 style="font-size:1rem;font-weight:700;color:#1A1A1A">Input Kehadiran Manual</h3>
            <p style="font-size:.8rem;color:#7A7A7A;margin-top:2px">Pilih jadwal dan tanggal lalu input status kehadiran satu per satu.</p>
        </div>
        <div style="padding:24px">
            <form method="POST" action="{{ route('admin.kehadiran.input') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:16px;align-items:end">
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:#1A1A1A;display:block;margin-bottom:6px">Jadwal Latihan <span style="color:#C65D2E">*</span></label>
                        <select name="jadwal_id" required style="width:100%;padding:10px 14px;border:1.5px solid #E8E0D8;border-radius:10px;font-size:.875rem;background:#FAF8F6;outline:none">
                            <option value="">-- Pilih jadwal --</option>
                            @foreach($jadwal as $j)
                            <option value="{{ $j->id }}">{{ $j->hari }} · {{ $j->jam_mulai }}–{{ $j->jam_selesai }} · {{ $j->kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:#1A1A1A;display:block;margin-bottom:6px">Kelas / Kegiatan <span style="color:#C65D2E">*</span></label>
                        <select name="tarian_id" required style="width:100%;padding:10px 14px;border:1.5px solid #E8E0D8;border-radius:10px;font-size:.875rem;background:#FAF8F6;outline:none">
                            <option value="">-- Pilih kegiatan --</option>
                            @foreach($tarian as $t)
                            <option value="{{ $t->id }}">{{ $t->nama }} ({{ ucfirst($t->jenis_kegiatan ?? 'tari') }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label style="font-size:.8rem;font-weight:700;color:#1A1A1A;display:block;margin-bottom:6px">Tanggal <span style="color:#C65D2E">*</span></label>
                        <input type="date" name="tanggal" required value="{{ $today }}" style="width:100%;padding:10px 14px;border:1.5px solid #E8E0D8;border-radius:10px;font-size:.875rem;background:#FAF8F6;outline:none">
                    </div>
                    <button type="submit" style="background:#C65D2E;color:#fff;font-weight:700;padding:11px 24px;border-radius:50px;border:none;cursor:pointer;white-space:nowrap;font-size:.875rem">
                        Mulai Input →
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sesi manual hari ini --}}
    @if($sesiHariIni->count())
    <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden">
        <div style="padding:16px 24px;border-bottom:1px solid #F0EBE5">
            <h3 style="font-size:1rem;font-weight:700;color:#1A1A1A">Sesi yang Sudah Diinput Hari Ini</h3>
        </div>
        <div style="padding:8px 24px 16px">
            @foreach($sesiHariIni as $s)
            <div style="display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #FAF8F6">
                <div style="width:36px;height:36px;background:#E8F5E9;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#2E7D32;font-size:1rem">✓</div>
                <div style="flex:1">
                    <span style="font-weight:600;font-size:.875rem">{{ $s->jadwal->hari }}</span>
                    <span style="color:#7A7A7A;font-size:.8rem"> · {{ $s->tarian->nama }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
function switchTab(tab) {
    document.getElementById('panel-barcode').style.display = tab === 'barcode' ? 'block' : 'none';
    document.getElementById('panel-manual').style.display  = tab === 'manual'  ? 'block' : 'none';
    document.getElementById('tab-barcode').style.background = tab === 'barcode' ? '#C65D2E' : 'transparent';
    document.getElementById('tab-barcode').style.color      = tab === 'barcode' ? '#fff'    : '#7A7A7A';
    document.getElementById('tab-manual').style.background  = tab === 'manual'  ? '#C65D2E' : 'transparent';
    document.getElementById('tab-manual').style.color       = tab === 'manual'  ? '#fff'    : '#7A7A7A';
}
</script>

@endsection