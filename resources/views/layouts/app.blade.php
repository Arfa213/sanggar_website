<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteProfil->nama_sanggar ?? 'Sanggar Mulya Bhakti' }} — @yield('title', $siteProfil->tagline ?? 'Melestarikan Budaya Melalui Seni')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar" id="navbar">
        <div class="container navbar-inner">
            <a href="{{ route('home') }}" class="navbar-brand">
                {{ $siteProfil->nama_sanggar ?? 'Sanggar Mulya Bhakti' }}
            </a>

            <ul class="navbar-menu">
                <li><a href="{{ route('home') }}"           class="{{ request()->routeIs('home')            ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('profile') }}"        class="{{ request()->routeIs('profile')         ? 'active' : '' }}">Profil</a></li>
                <li><a href="{{ route('event') }}"          class="{{ request()->routeIs('event')           ? 'active' : '' }}">Event</a></li>
                <li><a href="{{ route('digital-archive') }}" class="{{ request()->routeIs('digital-archive') ? 'active' : '' }}">Arsip Digital</a></li>
                @auth
                <li><a href="{{ route('penjadwalan') }}"   class="{{ request()->routeIs('penjadwalan*')    ? 'active' : '' }}">Jadwal</a></li>
                @endauth
            </ul>

            <div class="navbar-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn-masuk" style="display:flex;align-items:center;gap:10px;padding:5px 15px 5px 5px">
                        @if(Auth::user()->foto)
                            <img src="{{ asset('storage/'.Auth::user()->foto) }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:2px solid var(--primary-pale)">
                        @else
                            <div style="width:32px;height:32px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.8rem">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif
                        <span style="max-width:100px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ explode(' ', Auth::user()->name)[0] }}</span>
                    </a>
                    @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="btn-daftar">Admin Panel</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-daftar" style="border:none;cursor:pointer;background:transparent">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"    class="btn-masuk">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-daftar">Daftar Anggota</a>
                @endauth
            </div>

            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    {{-- MOBILE MENU --}}
    <div class="mobile-menu" id="mobileMenu">
        <ul>
            <li><a href="{{ route('home') }}">Beranda</a></li>
            <li><a href="{{ route('profile') }}">Profil</a></li>
            <li><a href="{{ route('event') }}">Event</a></li>
            <li><a href="{{ route('digital-archive') }}">Arsip Digital</a></li>
            @auth
            <li><a href="{{ route('dashboard') }}">Dashboard Saya</a></li>
            <li><a href="{{ route('penjadwalan') }}">Jadwal Latihan</a></li>
            @if(Auth::user()->role === 'admin')
            <li><a href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
            @endif
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="background:none;border:none;font-size:1rem;cursor:pointer;color:inherit;padding:0">Keluar</button>
                </form>
            </li>
            @else
            <li><a href="{{ route('login') }}">Masuk</a></li>
            <li><a href="{{ route('register') }}" class="btn-daftar">Daftar Anggota</a></li>
            @endauth
        </ul>
    </div>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
    <div style="position:fixed;top:80px;right:20px;z-index:9999;background:#F0FDF4;border:1px solid #86EFAC;border-radius:12px;padding:12px 18px;color:#15803D;font-size:.875rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.1);max-width:380px;display:flex;align-items:center;gap:8px" id="flash-success">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
        <button onclick="document.getElementById('flash-success').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#15803D;font-size:1rem;line-height:1">✕</button>
    </div>
    @endif
    @if(session('error'))
    <div style="position:fixed;top:80px;right:20px;z-index:9999;background:#FEF2F2;border:1px solid #FECACA;border-radius:12px;padding:12px 18px;color:#DC2626;font-size:.875rem;font-weight:600;box-shadow:0 4px 16px rgba(0,0,0,.1);max-width:380px;display:flex;align-items:center;gap:8px" id="flash-error">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
        <button onclick="document.getElementById('flash-error').remove()" style="margin-left:auto;background:none;border:none;cursor:pointer;color:#DC2626;font-size:1rem;line-height:1">✕</button>
    </div>
    @endif

    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <h3>{{ $siteProfil->nama_sanggar ?? 'Sanggar Mulya Bhakti' }}</h3>
                <p>{{ $siteProfil->tagline ?? 'Melestarikan budaya Indonesia melalui seni tari tradisional.' }}</p>
            </div>
            <div class="footer-links">
                <h4>Link Cepat</h4>
                <ul>
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('profile') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('event') }}">Event & Pentas</a></li>
                    <li><a href="{{ route('digital-archive') }}">Arsip Digital</a></li>
                    @auth
                    <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ route('penjadwalan') }}">Jadwal Latihan</a></li>
                    @else
                    <li><a href="{{ route('register') }}">Daftar Anggota</a></li>
                    @endauth
                </ul>
            </div>
            <div class="footer-kontak">
                <h4>Kontak</h4>
                <ul>
                    @if($siteProfil->alamat ?? false)<li>📍 {{ $siteProfil->alamat }}</li>@endif
                    @if($siteProfil->no_hp ?? false)<li>📞 {{ $siteProfil->no_hp }}</li>@endif
                    @if($siteProfil->email ?? false)<li>✉️ {{ $siteProfil->email }}</li>@endif
                </ul>
            </div>
            <div class="footer-sosmed">
                <h4>Ikuti Kami</h4>
                <div class="sosmed-icons">
                    <a href="{{ $siteProfil->instagram ? 'https://instagram.com/'.ltrim($siteProfil->instagram,'@') : '#' }}" aria-label="Instagram" target="_blank">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="{{ $siteProfil->facebook ? 'https://facebook.com/'.$siteProfil->facebook : '#' }}" aria-label="Facebook" target="_blank">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="{{ $siteProfil->youtube ? 'https://youtube.com/@'.$siteProfil->youtube : '#' }}" aria-label="YouTube" target="_blank">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© {{ date('Y') }} {{ $siteProfil->nama_sanggar ?? 'Sanggar Mulya Bhakti' }}. All rights reserved.</p>
        </div>
    </footer>

    {{-- CHATBOT WIDGET --}}
    @include('components.chatbot')

    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Auto-hide flash after 4 seconds --}}
    <script>
    setTimeout(function() {
        document.getElementById('flash-success')?.remove();
        document.getElementById('flash-error')?.remove();
    }, 4000);
    </script>
</body>
</html>
