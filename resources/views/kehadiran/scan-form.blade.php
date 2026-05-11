<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absensi Sanggar Mulya Bhakti</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #FDF8F5 0%, #F5EAE2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 24px; padding: 36px 32px; max-width: 440px; width: 100%; box-shadow: 0 20px 60px rgba(0,0,0,.12); }
        .logo { text-align: center; margin-bottom: 24px; }
        .logo-icon { width: 64px; height: 64px; background: linear-gradient(135deg, #C65D2E, #E8864A); border-radius: 18px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-bottom: 12px; }
        h1 { font-size: 1.4rem; font-weight: 800; color: #1A1A1A; margin-bottom: 4px; }
        .subtitle { font-size: .875rem; color: #7A7A7A; margin-bottom: 28px; }
        .info-box { background: #FDF8F5; border-radius: 14px; padding: 16px; margin-bottom: 24px; }
        .info-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; }
        .info-row:last-child { margin-bottom: 0; }
        .info-label { font-size: .8rem; color: #7A7A7A; }
        .info-value { font-size: .875rem; font-weight: 700; color: #1A1A1A; }
        .chip { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .75rem; font-weight: 700; }
        .chip-green { background: #E8F5E9; color: #2E7D32; }
        .chip-orange { background: #FFF3E0; color: #C65D2E; }
        label { font-size: .875rem; font-weight: 600; color: #1A1A1A; display: block; margin-bottom: 6px; }
        select, input { width: 100%; padding: 12px 16px; border: 1.5px solid #E8E0D8; border-radius: 12px; font-size: .9rem; font-family: inherit; background: #FAF8F6; outline: none; transition: border-color .2s; appearance: none; -webkit-appearance: none; }
        select:focus, input:focus { border-color: #C65D2E; }
        .btn { width: 100%; padding: 14px; background: linear-gradient(135deg, #C65D2E, #E8864A); color: #fff; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; margin-top: 20px; transition: transform .15s, box-shadow .15s; font-family: inherit; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(198,93,46,.35); }
        .btn:active { transform: translateY(0); }
        .search-wrap { position: relative; }
        .search-wrap input { padding-right: 40px; }
        .timer { text-align: center; font-size: .8rem; color: #7A7A7A; margin-top: 16px; }
        .timer strong { color: #C65D2E; }
    </style>
</head>
<body>
<div class="card">
    <div class="logo">
        <div class="logo-icon">🎭</div>
        <h1>Absensi Digital</h1>
        <p class="subtitle">Sanggar Mulya Bhakti</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Kelas / Kegiatan</span>
            <span class="info-value">{{ $sesi->tarian->nama }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jenis</span>
            <span class="chip chip-orange">{{ ucfirst($sesi->tarian->jenis_kegiatan ?? 'Tari') }}</span>
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
            <span class="info-label">Status Sesi</span>
            <span class="chip chip-green">🟢 Aktif</span>
        </div>
    </div>

    <form method="POST" action="{{ route('kehadiran.proses', $token) }}">
        @csrf
        <div style="margin-bottom: 16px">
            <label for="user_id">Pilih Nama Kamu *</label>
            <div class="search-wrap">
                <select name="user_id" id="user_id" required>
                    <option value="">-- Pilih nama --</option>
                    @foreach($sesi->tarian->pendaftaran()->with('user')->where('status','aktif')->get() as $p)
                    <option value="{{ $p->user_id }}">{{ $p->user->name }}</option>
                    @endforeach
                </select>
            </div>
            <p style="font-size:.75rem;color:#7A7A7A;margin-top:6px">⚠️ Pilih nama kamu yang benar. Kehadiran akan langsung tercatat.</p>
        </div>

        <button type="submit" class="btn">✓ Konfirmasi Hadir</button>
    </form>

    @if($sesi->expires_at)
    <div class="timer">
        Sesi berakhir pukul <strong>{{ $sesi->expires_at->format('H:i') }}</strong>
    </div>
    @endif
</div>
</body>
</html>
