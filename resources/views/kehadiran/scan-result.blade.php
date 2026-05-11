<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Absensi — Sanggar Mulya Bhakti</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #FDF8F5 0%, #F5EAE2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 24px; padding: 44px 32px; max-width: 400px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.12); text-align: center; }
        .icon { font-size: 4rem; margin-bottom: 16px; display: block; }
        h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 8px; }
        .msg { font-size: .9rem; color: #7A7A7A; margin-bottom: 28px; line-height: 1.6; }
        .info-box { background: #F5F3F1; border-radius: 14px; padding: 16px; margin-bottom: 24px; text-align: left; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { font-size: .8rem; color: #7A7A7A; }
        .info-value { font-size: .875rem; font-weight: 700; color: #1A1A1A; }
        .btn { display: inline-block; padding: 12px 28px; border-radius: 50px; font-weight: 700; text-decoration: none; font-size: .9rem; font-family: inherit; }
        .btn-back { background: #F3F4F6; color: #3D3D3D; border: 1px solid #E8E0D8; }
    </style>
</head>
<body>
<div class="card">
    @if($success)
        <span class="icon">🎉</span>
        <h1 style="color:#2E7D32">Kehadiran Tercatat!</h1>
        <p class="msg">
            Halo <strong>{{ $user->name ?? '' }}</strong>,<br>
            kamu berhasil tercatat <strong style="color:#2E7D32">HADIR</strong> pada sesi ini.
        </p>
        @if(isset($sesi))
        <div class="info-box">
            <div class="info-row">
                <span class="info-label">Kelas</span>
                <span class="info-value">{{ $sesi->tarian->nama }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jadwal</span>
                <span class="info-value">{{ $sesi->jadwal->hari }}, {{ $sesi->jadwal->jam_mulai }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tanggal</span>
                <span class="info-value">{{ $sesi->tanggal->isoFormat('D MMMM YYYY') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Waktu Scan</span>
                <span class="info-value">{{ now()->format('H:i') }} WIB</span>
            </div>
        </div>
        @endif
        <p style="font-size:.8rem;color:#7A7A7A">Halaman ini bisa ditutup ✓</p>
    @else
        <span class="icon">❌</span>
        <h1 style="color:#DC2626">Absensi Gagal</h1>
        <p class="msg">{{ $pesan ?? 'Terjadi kesalahan.' }}</p>
        @if(isset($sesi))
        <a href="{{ route('kehadiran.scan', $sesi->barcode_token) }}" class="btn btn-back">← Coba Lagi</a>
        @endif
    @endif
</div>
</body>
</html>
