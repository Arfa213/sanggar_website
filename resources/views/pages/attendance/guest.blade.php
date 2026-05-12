<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran & Booking - Sanggar Mulya Bhakti</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #C65D2E;
            --dark: #1A1A1A;
            --bg: #FDF8F5;
            --border: #E8E0D8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
        }
        .card {
            background: #fff;
            width: 100%;
            max-width: 500px;
            border-radius: 32px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            border: 1px solid var(--border);
        }
        .logo { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 900; color: var(--dark); margin-bottom: 24px; text-align: center; }
        h1 { font-size: 1.5rem; margin-bottom: 12px; font-weight: 800; text-align: center; }
        p { color: #666; font-size: .9rem; line-height: 1.6; margin-bottom: 30px; text-align: center; }
        
        .form-group { text-align: left; margin-bottom: 20px; }
        label { display: block; font-size: .85rem; font-weight: 700; margin-bottom: 8px; color: #333; }
        input, textarea, select {
            width: 100%;
            padding: 14px 18px;
            border: 1.5px solid var(--border);
            border-radius: 16px;
            font-family: inherit;
            font-size: .95rem;
            transition: all .2s;
            outline: none;
            background: #fff;
        }
        input:focus, textarea:focus, select:focus { border-color: var(--primary); background: #fff; }
        
        .time-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 8px; margin-top: 10px; }
        .time-btn {
            padding: 10px;
            border: 1.5px solid var(--border);
            border-radius: 12px;
            font-size: .8rem;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: all .2s;
        }
        .time-btn.active { background: var(--primary); color: #fff; border-color: var(--primary); }

        .btn {
            background: var(--primary);
            color: #fff;
            border: none;
            width: 100%;
            padding: 18px;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .3s;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(198, 93, 46, 0.2);
        }
        .btn:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(198, 93, 46, 0.3); }
        
        .success-card { text-align: center; }
        .success-icon { width: 64px; height: 64px; background: #E8F5E9; color: #2E7D32; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; font-size: 1.5rem; }
    </style>
</head>
<body>

<div class="card">
    <div class="logo">Sanggar Mulya Bhakti</div>
    
    @if(session('success'))
        <div class="success-card">
            <div class="success-icon">✓</div>
            <h1 style="color: #2E7D32">Booking Berhasil!</h1>
            <p>{{ session('success') }}</p>
            <p style="font-size: .8rem; background: #F0FDF4; padding: 12px; border-radius: 12px;">Mohon tunjukkan halaman ini atau sebutkan nama Anda saat tiba di sanggar.</p>
            <button onclick="window.location.href='/'" class="btn" style="background: var(--dark)">Kembali ke Beranda</button>
        </div>
    @elseif(session('error'))
        <div style="background: #FEF2F2; color: #DC2626; padding: 16px; border-radius: 16px; margin-bottom: 24px; font-size: .85rem; font-weight: 600;">
            ⚠️ {{ session('error') }}
        </div>
        @include('pages.attendance.guest_form')
    @else
        <h1>Pendaftaran & Booking</h1>
        <p>Silakan isi data untuk booking sesi latihan fleksibel atau kunjungan sanggar.</p>
        @include('pages.attendance.guest_form')
    @endif
</div>

<script>
    function setJam(jam, el) {
        document.getElementById('jamInput').value = jam;
        document.querySelectorAll('.time-btn').forEach(btn => btn.classList.remove('active'));
        el.classList.add('active');
    }
</script>

</body>
</html>
