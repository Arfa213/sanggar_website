<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ $siteProfil->nama_sanggar ?? 'Sanggar Mulya Bhakti' }}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logosanggar.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #C65D2E;
            --primary-dark: #A34A22;
            --primary-pale: #FDF0EA;
            --dark:         #1A1A1A;
            --text:         #3D3D3D;
            --muted:        #7A7A7A;
            --border:       #E8E0D8;
            --bg:           #F5F3F0;
            --sidebar-w:    260px;
            --topbar-h:     64px;
            --font-display: 'Playfair Display', serif;
            --font-body:    'Plus Jakarta Sans', sans-serif;
        }

        .m-lang-switcher { position: relative; display: inline-block; margin-right: 5px; }
        .m-lang-switcher:hover .m-lang-menu { display: block !important; }
        .m-lang-menu {
            position: absolute; top: 100%; right: 0; width: 100px;
            background: #fff; border: 1px solid var(--border); border-radius: 10px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08); margin-top: 5px;
            display: none; z-index: 1000; overflow: hidden;
        }

        body {
            font-family: var(--font-body);
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── SIDEBAR ── */
        .m-sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--dark);
            display: flex;
            flex-direction: column;
            z-index: 100;
            transition: transform .3s ease;
        }

        .m-sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,.08);
        }

        .m-sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .m-logo-icon {
            width: 40px; height: 40px;
            background: var(--primary);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: .75rem; font-weight: 900; color: #fff;
            letter-spacing: .5px; flex-shrink: 0;
        }

        .m-logo-text { line-height: 1.2; }
        .m-logo-title { font-size: .875rem; font-weight: 700; color: #fff; }
        .m-logo-sub   { font-size: .7rem; color: rgba(255,255,255,.45); letter-spacing: .5px; }

        /* User card in sidebar */
        .m-user-card {
            margin: 16px 20px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 12px;
            padding: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .m-user-avatar {
            width: 38px; height: 38px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; font-weight: 900; color: #fff;
            flex-shrink: 0;
        }

        .m-user-name  { font-size: .8rem; font-weight: 700; color: #fff; }
        .m-user-role  {
            font-size: .68rem; color: rgba(255,255,255,.45);
            background: rgba(255,255,255,.08);
            padding: 1px 7px; border-radius: 20px; margin-top: 3px;
            display: inline-block;
        }

        /* Nav */
        .m-nav { flex: 1; overflow-y: auto; padding: 8px 0; }
        .m-nav::-webkit-scrollbar { width: 0; }

        .m-nav-group { margin-bottom: 8px; }
        .m-nav-label {
            font-size: .65rem; font-weight: 700;
            color: rgba(255,255,255,.3);
            letter-spacing: 1px; padding: 10px 20px 4px;
            text-transform: uppercase;
        }

        .m-nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 20px;
            font-size: .825rem; font-weight: 600;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .15s;
        }

        .m-nav-item:hover {
            color: #fff;
            background: rgba(255,255,255,.05);
        }

        .m-nav-item.active {
            color: #fff;
            background: rgba(198,93,46,.18);
            border-left-color: var(--primary);
        }

        .m-nav-item svg { flex-shrink: 0; }

        /* Badge di nav */
        .m-nav-badge {
            margin-left: auto;
            background: var(--primary);
            color: #fff;
            font-size: .65rem; font-weight: 800;
            padding: 2px 7px;
            border-radius: 20px;
            min-width: 20px; text-align: center;
        }

        /* Bottom actions */
        .m-sidebar-footer {
            padding: 12px 0;
            border-top: 1px solid rgba(255,255,255,.08);
        }

        /* ── TOPBAR ── */
        .m-topbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-w);
            right: 0;
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 24px;
            gap: 12px;
            z-index: 90;
        }

        .m-topbar-toggle {
            display: none;
            background: none; border: none; cursor: pointer;
            padding: 6px; border-radius: 8px;
            color: var(--dark);
        }

        .m-topbar-title {
            font-family: var(--font-display);
            font-size: 1.1rem; font-weight: 700; color: var(--dark);
        }

        .m-topbar-right {
            margin-left: auto;
            display: flex; align-items: center; gap: 10px;
        }

        .m-topbar-link {
            display: flex; align-items: center; gap: 6px;
            padding: 8px 14px;
            background: var(--primary-pale);
            border: 1px solid rgba(198,93,46,.2);
            border-radius: 50px;
            font-size: .8rem; font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            transition: all .15s;
        }

        .m-topbar-link:hover {
            background: var(--primary); color: #fff;
        }

        /* ── MAIN CONTENT ── */
        .m-main {
            margin-left: var(--sidebar-w);
            padding-top: var(--topbar-h);
            min-height: 100vh;
        }

        .m-content {
            padding: 28px 28px 60px;
        }

        /* ── FLASH ── */
        .m-flash {
            position: fixed;
            top: 80px;
            right: 24px;
            z-index: 9999;
            display: flex; align-items: center; gap: 10px;
            padding: 13px 18px;
            border-radius: 12px;
            font-size: .875rem; font-weight: 600;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: slideInRight .4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            max-width: 380px;
        }
        @keyframes slideInRight { 
            from { opacity:0; transform:translateX(30px); } 
            to { opacity:1; transform:translateX(0); } 
        }
        .m-flash--success { background: #F0FDF4; border: 1px solid #86EFAC; color: #15803D; }
        .m-flash--error   { background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; }
        .m-flash-close    { margin-left: auto; background: none; border: none; cursor: pointer; font-size: 1rem; line-height: 1; opacity: .6; color: inherit; }

        /* ── PAGE HEADER ── */
        .m-page-header {
            margin-bottom: 24px;
        }
        .m-page-header .m-badge {
            display: inline-block;
            background: var(--primary-pale);
            border: 1px solid rgba(198,93,46,.2);
            color: var(--primary);
            font-size: .68rem; font-weight: 800;
            padding: 3px 10px; border-radius: 20px;
            letter-spacing: .8px; text-transform: uppercase;
            margin-bottom: 6px;
        }
        .m-page-header h1 {
            font-family: var(--font-display);
            font-size: 1.75rem; font-weight: 900;
            color: var(--dark);
        }
        .m-page-header p { color: var(--muted); font-size: .875rem; margin-top: 4px; }

        /* ── RESPONSIVE MOBILE ── */
        .m-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 99;
        }

        @media (max-width: 900px) {
            .m-sidebar { transform: translateX(-100%); }
            .m-sidebar.open { transform: translateX(0); }
            .m-topbar { left: 0; }
            .m-topbar-toggle { display: flex; }
            .m-main { margin-left: 0; }
            .m-content { padding: 20px 16px 60px; }
            .m-overlay.show { display: block; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="m-sidebar" id="mSidebar">

    {{-- Logo --}}
    <div class="m-sidebar-header">
        <a href="{{ route('home') }}" class="m-sidebar-logo">
            <div class="m-logo-icon">SMB</div>
            <div class="m-logo-text">
                <div class="m-logo-title">Sanggar Mulya Bhakti</div>
                <div class="m-logo-sub">Member Area</div>
            </div>
        </a>
    </div>

    {{-- User card --}}
    <div class="m-user-card">
        @if(Auth::user()->foto)
            <img src="{{ asset('storage/'.Auth::user()->foto) }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid rgba(255,255,255,.2)">
        @else
            <div class="m-user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
        @endif
        <div>
            <div class="m-user-name">{{ Auth::user()->name }}</div>
            <div class="m-user-role">
                {{ Auth::user()->role === 'admin' ? '👑 Admin' : '🎭 Anggota' }}
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="m-nav">
        <div class="m-nav-group">
            <div class="m-nav-label">{{ __('Menu Utama') }}</div>
            <a href="{{ route('dashboard') }}" class="m-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                {{ __('Dashboard') }}
            </a>
            @if(Auth::user()->tipe_anggota !== 'pengunjung')
            <a href="{{ route('member.profile') }}" class="m-nav-item {{ request()->routeIs('member.profile*') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                {{ __('Profil Saya') }}
            </a>
            @endif
            <a href="{{ route('penjadwalan') }}" class="m-nav-item {{ request()->routeIs('penjadwalan') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                {{ __('Latihan') }}
                @php $kelasAktif = \App\Models\PendaftaranTari::where('user_id', Auth::id())->where('status','aktif')->count(); @endphp
                @if($kelasAktif)
                <span class="m-nav-badge">{{ $kelasAktif }}</span>
                @endif
            </a>
            <a href="{{ route('penjadwalan.kehadiran') }}" class="m-nav-item {{ request()->routeIs('penjadwalan.kehadiran') ? 'active' : '' }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                {{ __('Riwayat Kehadiran') }}
            </a>
        </div>

        <div class="m-nav-group">
            <div class="m-nav-label">{{ __('Jelajahi') }}</div>
            <a href="{{ route('digital-archive') }}" class="m-nav-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                {{ __('Arsip Tarian') }}
            </a>
            <a href="{{ route('event') }}" class="m-nav-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                {{ __('Event & Pentas') }}
            </a>
            <a href="{{ route('profile') }}" class="m-nav-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                {{ __('Profil Sanggar') }}
            </a>
        </div>

        @if(Auth::user()->role === 'admin')
        <div class="m-nav-group">
            <div class="m-nav-label">{{ __('Administrator') }}</div>
            <a href="{{ route('admin.dashboard') }}" class="m-nav-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                {{ __('Panel Admin') }}
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-left:auto"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            </a>
        </div>
        @endif
    </nav>

    {{-- Footer nav --}}
    <div class="m-sidebar-footer">
        <a href="{{ route('home') }}" class="m-nav-item">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
            {{ __('Kembali ke Website') }}
        </a>
        {{-- Form logout dengan mekanisme refresh CSRF token --}}
        <form method="POST" action="{{ route('logout') }}" id="logoutForm">
            @csrf
            <button type="button"
                onclick="submitLogout()"
                class="m-nav-item"
                style="width:100%;background:none;border:none;cursor:pointer;text-align:left;color:#FF6B6B;font-family:var(--font-body);font-size:.825rem;font-weight:700;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                {{ __('Keluar Aplikasi') }}
            </button>
        </form>
    </div>
</aside>

{{-- Overlay mobile --}}
<div class="m-overlay" id="mOverlay" onclick="closeSidebar()"></div>

{{-- TOPBAR --}}
<header class="m-topbar">
    <button class="m-topbar-toggle" onclick="toggleSidebar()">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <span class="m-topbar-title">{{ __(\Route::currentRouteName() === 'dashboard' ? 'Dashboard Saya' : 'Dashboard') }}</span>
    <div class="m-topbar-right">
        {{-- Language Switcher --}}
        <div class="m-lang-switcher">
            @php $currLocale = app()->getLocale(); @endphp
            <button class="m-topbar-link" style="background:none; border:1px solid var(--border); display:flex; align-items:center; gap:6px; cursor:pointer; padding:8px 14px; border-radius:50px; font-size:.8rem; font-weight:700; color:var(--dark);">
                @if($currLocale === 'en')
                    🇬🇧 EN
                @else
                    🇮🇩 ID
                @endif
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="opacity:.6;"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="m-lang-menu">
                <a href="{{ route('lang.switch', 'id') }}" style="display:flex; align-items:center; gap:8px; padding:10px 12px; text-decoration:none; color:var(--dark); font-size:.8rem; font-weight:600; transition:all .2s;" onmouseover="this.style.background='var(--primary-pale)'" onmouseout="this.style.background='none'">🇮🇩 ID</a>
                <a href="{{ route('lang.switch', 'en') }}" style="display:flex; align-items:center; gap:8px; padding:10px 12px; text-decoration:none; color:var(--dark); font-size:.8rem; font-weight:600; transition:all .2s;" onmouseover="this.style.background='var(--primary-pale)'" onmouseout="this.style.background='none'">🇬🇧 EN</a>
            </div>
        </div>

        @if(Auth::user()->tipe_anggota !== 'pengunjung')
        <a href="{{ route('penjadwalan') }}" class="m-topbar-link">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            {{ __('Daftar Kelas') }}
        </a>
        @endif
        <button type="button" onclick="submitLogout()" class="m-topbar-link" style="background:#FEF2F2;color:#DC2626;border-color:rgba(220,38,38,.2)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            {{ __('Keluar') }}
        </button>
    </div>
</header>

{{-- MAIN CONTENT --}}
<main class="m-main">
    <div class="m-content">

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="m-flash m-flash--success" id="mFlash">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            {{ session('success') }}
            <button class="m-flash-close" onclick="this.parentElement.remove()">✕</button>
        </div>
        @endif
        @if(session('error'))
        <div class="m-flash m-flash--error" id="mFlash">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            {{ session('error') }}
            <button class="m-flash-close" onclick="this.parentElement.remove()">✕</button>
        </div>
        @endif

        @yield('content')
    </div>
</main>

{{-- Chatbot --}}
@include('components.chatbot')

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/sweetalert-integration.js') }}"></script>

<script>
function toggleSidebar() {
    document.getElementById('mSidebar').classList.toggle('open');
    document.getElementById('mOverlay').classList.toggle('show');
}
function closeSidebar() {
    document.getElementById('mSidebar').classList.remove('open');
    document.getElementById('mOverlay').classList.remove('show');
}
setTimeout(() => document.getElementById('mFlash')?.remove(), 4000);

// Refresh CSRF token sebelum submit logout (mencegah 419 Page Expired)
function submitLogout() {
    Swal.fire({
        title: 'Keluar Aplikasi',
        text: 'Apakah Anda yakin ingin keluar dari aplikasi?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        customClass: {
            confirmButton: 'swal-btn swal-btn-confirm',
            cancelButton: 'swal-btn swal-btn-cancel',
            popup: 'swal-popup-custom'
        },
        buttonsStyling: false,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('logoutForm');
            const btn  = form.querySelector('button');
            if (btn) {
                btn.disabled = true;
                btn.style.opacity = '0.6';
            }

            // Ambil CSRF token terbaru dari meta tag (sudah di-set saat halaman dimuat)
            const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // Update hidden input CSRF di dalam form
            const csrfInput = form.querySelector('input[name="_token"]');
            if (csrfInput) csrfInput.value = token;

            form.submit();
        }
    });
}

// Cegah bfcache: reload halaman jika user kembali via tombol Back browser
// Ini mencegah CSRF token lama (dari cache) digunakan untuk logout
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        // Halaman di-restore dari bfcache — reload untuk dapat token baru
        window.location.reload();
    }
});
</script>
@stack('scripts')
</body>
</html>
