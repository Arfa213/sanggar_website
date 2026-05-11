@extends('admin.layouts.app')
@section('title', 'QR Code Kehadiran')

@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>QR Code Sesi Kehadiran</h1>
        <p>Tampilkan QR Code ini kepada anggota untuk scan kehadiran.</p>
    </div>
    <div style="display:flex;gap:10px">
        <form method="POST" action="{{ route('admin.kehadiran.tutup-sesi', $sesi->id) }}" style="display:inline">
            @csrf
            <button type="submit" onclick="return confirm('Tutup sesi kehadiran ini?')"
                style="background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;font-weight:700;padding:10px 20px;border-radius:50px;cursor:pointer;font-size:.875rem">
                ✕ Tutup Sesi
            </button>
        </form>
        <a href="{{ route('admin.kehadiran.index') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

@php
    $expired = $sesi->expires_at && now()->isAfter($sesi->expires_at);
    $remaining = $sesi->expires_at ? now()->diffInMinutes($sesi->expires_at, false) : null;
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;align-items:start">

    {{-- QR CODE PANEL --}}
    <div style="background:#fff;border-radius:20px;border:1px solid #E8E0D8;padding:40px;text-align:center">
        @if($expired)
        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:16px;margin-bottom:24px;color:#DC2626;font-weight:700">
            ⏰ Sesi sudah berakhir!
        </div>
        @else
        <div style="background:#F0FDF4;border:1px solid #86EFAC;border-radius:12px;padding:12px;margin-bottom:24px;color:#15803D;font-weight:700;font-size:.875rem">
            🟢 Sesi Aktif — Tersisa ≈ {{ $remaining }} menit
        </div>
        @endif

        {{-- QR Code menggunakan Google Charts API --}}
        <div style="display:inline-block;padding:16px;background:#fff;border:2px solid #E8E0D8;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,.08)">
            <img
                src="https://api.qrserver.com/v1/create-qr-code/?size=280x280&data={{ urlencode($scanUrl) }}&bgcolor=ffffff&color=1A1A1A&margin=10"
                alt="QR Code Kehadiran"
                style="width:280px;height:280px;display:block;border-radius:8px"
                id="qr-img"
            >
        </div>

        <div style="margin-top:20px">
            <p style="font-size:.8rem;color:#7A7A7A;margin-bottom:8px">Atau scan langsung:</p>
            <code style="background:#F5F3F1;padding:8px 14px;border-radius:8px;font-size:.75rem;word-break:break-all;display:block">{{ $scanUrl }}</code>
        </div>

        <div style="margin-top:24px;display:flex;gap:10px;justify-content:center">
            <button onclick="window.print()"
                style="background:#C65D2E;color:#fff;font-weight:700;padding:10px 24px;border-radius:50px;border:none;cursor:pointer;font-size:.875rem">
                🖨 Cetak QR Code
            </button>
            <a href="{{ $scanUrl }}" target="_blank"
                style="background:#F3F4F6;color:#3D3D3D;font-weight:700;padding:10px 24px;border-radius:50px;text-decoration:none;font-size:.875rem;border:1px solid #E8E0D8">
                🔗 Test Link
            </a>
        </div>
    </div>

    {{-- INFO SESI --}}
    <div style="display:flex;flex-direction:column;gap:16px">
        <div style="background:#fff;border-radius:16px;border:1px solid #E8E0D8;overflow:hidden">
            <div style="padding:16px 24px;border-bottom:1px solid #F0EBE5;background:#FAFAF8">
                <h3 style="font-size:1rem;font-weight:700;color:#1A1A1A">Info Sesi</h3>
            </div>
            <div style="padding:20px 24px;display:flex;flex-direction:column;gap:14px">
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:.875rem;color:#7A7A7A">Jadwal</span>
                    <span style="font-weight:700;font-size:.875rem">{{ $sesi->jadwal->hari }}, {{ $sesi->jadwal->jam_mulai }}–{{ $sesi->jadwal->jam_selesai }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:.875rem;color:#7A7A7A">Kelas / Kegiatan</span>
                    <span style="font-weight:700;font-size:.875rem">{{ $sesi->tarian->nama }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:.875rem;color:#7A7A7A">Jenis</span>
                    <span class="chip chip--blue">{{ ucfirst($sesi->tarian->jenis_kegiatan ?? 'tari') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:.875rem;color:#7A7A7A">Tanggal</span>
                    <span style="font-weight:700;font-size:.875rem">{{ $sesi->tanggal->isoFormat('D MMMM YYYY') }}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:.875rem;color:#7A7A7A">Berakhir</span>
                    <span style="font-weight:700;font-size:.875rem;color:{{ $expired ? '#DC2626' : '#2E7D32' }}">
                        {{ $sesi->expires_at ? $sesi->expires_at->format('H:i') : '–' }}
                    </span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center">
                    <span style="font-size:.875rem;color:#7A7A7A">Dibuat oleh</span>
                    <span style="font-size:.875rem">{{ $sesi->dibuat_oleh ?? '–' }}</span>
                </div>
            </div>
        </div>

        {{-- Petunjuk --}}
        <div style="background:#FDF8F5;border-radius:16px;border:1px solid #F5EAE2;padding:20px 24px">
            <h4 style="font-weight:700;font-size:.9rem;margin-bottom:12px;color:#C65D2E">📋 Cara Absensi Barcode</h4>
            <ol style="padding-left:18px;color:#5A3A2E;font-size:.85rem;line-height:1.8">
                <li>Admin menampilkan QR Code ini di layar / cetak</li>
                <li>Anggota buka kamera HP atau app scan QR</li>
                <li>Scan QR Code → akan terbuka halaman absensi</li>
                <li>Anggota pilih nama mereka → klik <strong>Konfirmasi Hadir</strong></li>
                <li>Kehadiran tercatat otomatis sebagai <span style="color:#2E7D32;font-weight:700">Hadir</span></li>
            </ol>
        </div>

        {{-- Auto refresh countdown --}}
        @if(!$expired && $sesi->expires_at)
        <div style="background:#fff;border-radius:12px;border:1px solid #E8E0D8;padding:16px 20px;text-align:center">
            <div style="font-size:.8rem;color:#7A7A7A;margin-bottom:4px">Sesi berakhir dalam</div>
            <div id="countdown" style="font-size:2rem;font-weight:900;color:#C65D2E;font-variant-numeric:tabular-nums">–</div>
        </div>
        <script>
        (function() {
            const expiresAt = new Date("{{ $sesi->expires_at->toIso8601String() }}");
            function update() {
                const diff = Math.max(0, Math.floor((expiresAt - Date.now()) / 1000));
                const m = Math.floor(diff / 60).toString().padStart(2,'0');
                const s = (diff % 60).toString().padStart(2,'0');
                document.getElementById('countdown').textContent = m + ':' + s;
                if (diff <= 0) {
                    document.getElementById('countdown').textContent = 'BERAKHIR';
                    document.getElementById('countdown').style.color = '#DC2626';
                } else {
                    setTimeout(update, 1000);
                }
            }
            update();
        })();
        </script>
        @endif
    </div>
</div>

<style>
@media print {
    .page-header, nav, aside, .btn { display: none !important; }
    body * { visibility: hidden; }
    #qr-img, #qr-img * { visibility: visible; }
    #qr-img { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-60%); width: 400px !important; height: 400px !important; }
}
</style>

@endsection
