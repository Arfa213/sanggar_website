<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sesi Berakhir — Sanggar Mulya Bhakti</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logosanggar.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #1A1A1A 0%, #2D1A0E 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 24px;
            padding: 48px 40px;
            max-width: 480px;
            width: 100%;
            text-align: center;
            box-shadow: 0 40px 80px rgba(0,0,0,.4);
            animation: fadeUp .5s ease;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .icon-wrap {
            width: 80px; height: 80px;
            background: #FDF0EA;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 24px;
        }

        .badge {
            display: inline-block;
            background: #FDF0EA;
            border: 1px solid rgba(198,93,46,.2);
            color: #C65D2E;
            font-size: .7rem;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
            letter-spacing: .8px;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 900;
            color: #1A1A1A;
            margin-bottom: 12px;
        }

        p {
            color: #7A7A7A;
            font-size: .9rem;
            line-height: 1.65;
            margin-bottom: 28px;
        }

        .btn-login {
            display: inline-block;
            background: #C65D2E;
            color: #fff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: .9rem;
            font-weight: 700;
            padding: 13px 32px;
            border-radius: 50px;
            text-decoration: none;
            transition: background .2s;
        }

        .btn-login:hover { background: #A34A22; }

        .btn-home {
            display: inline-block;
            color: #7A7A7A;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            margin-top: 14px;
            transition: color .2s;
        }

        .btn-home:hover { color: #1A1A1A; }

        .countdown {
            font-size: .8rem;
            color: #ADADAD;
            margin-top: 20px;
        }

        .countdown span {
            font-weight: 700;
            color: #C65D2E;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon-wrap">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>

        <div class="badge">419 — Sesi Berakhir</div>

        <h1>Sesi Anda Telah Berakhir</h1>

        <p>
            Halaman ini sudah tidak valid karena sesi Anda telah habis atau tidak aktif terlalu lama.
            Silakan masuk kembali untuk melanjutkan.
        </p>

        <a href="{{ route('login') }}" class="btn-login">
            🔑 Masuk Kembali
        </a>

        <br>

        <a href="{{ route('home') }}" class="btn-home">
            ← Kembali ke Beranda
        </a>

        <p class="countdown">
            Dialihkan otomatis dalam <span id="sec">5</span> detik...
        </p>
    </div>

    <script>
        // Hitung mundur dan redirect ke login
        let s = 5;
        const tick = setInterval(function() {
            s--;
            document.getElementById('sec').textContent = s;
            if (s <= 0) {
                clearInterval(tick);
                window.location.href = '{{ route("login") }}';
            }
        }, 1000);
    </script>
</body>
</html>
