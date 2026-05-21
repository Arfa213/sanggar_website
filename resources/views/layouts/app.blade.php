<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $siteProfil->nama_sanggar ?? 'Sanggar Mulya Bhakti' }} — @yield('title', $siteProfil->tagline ?? 'Melestarikan Budaya Melalui Seni')</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logosanggar.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
    <style>
        /* Navbar Dropdown CSS */
        .nav-user-dropdown:hover .nav-user-menu {
            opacity: 1 !important;
            visibility: visible !important;
            transform: translateY(0) !important;
        }
        .nav-user-btn:hover {
            background: var(--primary-pale) !important;
        }
    </style>
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar" id="navbar">
        <div class="container navbar-inner">
            <a href="{{ route('home') }}" class="navbar-brand" style="display:flex; align-items:center; gap:10px;">
                <img src="{{ asset('assets/images/logosanggar.png') }}" alt="Logo" style="height:36px; object-fit:contain;">
                <span>{{ $siteProfil->nama_sanggar ?? 'Sanggar Mulya Bhakti' }}</span>
            </a>

            <ul class="navbar-menu">
                <li><a href="{{ route('home') }}"           class="{{ request()->routeIs('home')            ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('profile') }}"        class="{{ request()->routeIs('profile')         ? 'active' : '' }}">Profil</a></li>
                <li><a href="{{ route('event') }}"          class="{{ request()->routeIs('event')           ? 'active' : '' }}">Event</a></li>
                <li><a href="{{ route('digital-archive') }}" class="{{ request()->routeIs('digital-archive') ? 'active' : '' }}">Arsip Digital</a></li>
            </ul>

            <div class="navbar-actions">
                @auth
                    {{-- DROPDOWN USER --}}
                    <div class="nav-user-dropdown" style="position:relative; margin-left: 10px;">
                        <button class="nav-user-btn" style="background:none; border:none; display:flex; align-items:center; gap:12px; cursor:pointer; padding:6px 12px; border-radius:50px; transition:all .2s;">
                            @if(Auth::user()->foto)
                                <img src="{{ asset('storage/'.Auth::user()->foto) }}" style="width:34px;height:34px;border-radius:50%;object-fit:cover;border:2px solid var(--primary-pale)">
                            @else
                                <div style="width:34px;height:34px;background:var(--primary);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:.85rem">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <div style="text-align:left; line-height:1.2;">
                                <div style="font-size:.875rem; font-weight:700; color:var(--dark);">{{ explode(' ', Auth::user()->name)[0] }}</div>
                                <div style="font-size:.65rem; color:var(--muted); text-transform:uppercase; letter-spacing:.5px; font-weight:700;">{{ Auth::user()->role }}</div>
                            </div>
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-left:4px; opacity:.4;"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>

                        {{-- DROPDOWN MENU --}}
                        <div class="nav-user-menu" style="position:absolute; top:100%; right:0; width:220px; background:#fff; border:1px solid var(--border); border-radius:14px; box-shadow:0 10px 30px rgba(0,0,0,0.12); margin-top:10px; opacity:0; visibility:hidden; transform:translateY(10px); transition:all .25s cubic-bezier(0.68, -0.55, 0.265, 1.55); z-index:1000; overflow:hidden;">
                            <div style="padding:16px; background: #FAF8F6; border-bottom:1px solid var(--border);">
                                <div style="font-size:.85rem; font-weight:800; color:var(--dark);">{{ Auth::user()->name }}</div>
                                <div style="font-size:.72rem; color:var(--muted); margin-top:2px; overflow:hidden; text-overflow:ellipsis;">{{ Auth::user()->email }}</div>
                            </div>
                            <div style="padding:8px 0;">
                                @if(Auth::user()->role !== 'admin')
                                <a href="{{ route('dashboard') }}" style="display:flex; align-items:center; gap:12px; padding:10px 16px; text-decoration:none; color:var(--dark); font-size:.875rem; font-weight:500; transition:all .2s;" onmouseover="this.style.background='var(--primary-pale)'; this.style.color='var(--primary)'" onmouseout="this.style.background='none'; this.style.color='var(--dark)'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                                    Dashboard Member
                                </a>
                                @endif
                                @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" style="display:flex; align-items:center; gap:12px; padding:10px 16px; text-decoration:none; color:var(--dark); font-size:.875rem; font-weight:500; transition:all .2s;" onmouseover="this.style.background='var(--primary-pale)'; this.style.color='var(--primary)'" onmouseout="this.style.background='none'; this.style.color='var(--dark)'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                    Admin Panel
                                </a>
                                @endif

                                @if(Auth::user()->tipe_anggota !== 'pengunjung' && Auth::user()->role !== 'admin')
                                <a href="{{ route('member.profile') }}" style="display:flex; align-items:center; gap:12px; padding:10px 16px; text-decoration:none; color:var(--dark); font-size:.875rem; font-weight:500; transition:all .2s;" onmouseover="this.style.background='var(--primary-pale)'; this.style.color='var(--primary)'" onmouseout="this.style.background='none'; this.style.color='var(--dark)'">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Pengaturan Profil
                                </a>
                                @endif
                            </div>
                            <div style="padding:8px 0; border-top:1px solid var(--border); background: #FFF5F5;">
                                <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                                    @csrf
                                    <button type="submit" style="width:100%; display:flex; align-items:center; gap:12px; padding:10px 16px; border:none; background:none; color:#DC2626; font-size:.875rem; font-weight:800; cursor:pointer; text-align:left; transition:all .2s;" onmouseover="this.style.background='rgba(220,38,38,0.05)'" onmouseout="this.style.background='none'">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                        Keluar Akun
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
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
            @if(Auth::user()->role !== 'admin')
            <li><a href="{{ route('dashboard') }}">Dashboard Saya</a></li>
            @endif
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/sweetalert-integration.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    {{-- Auto-hide flash after 4 seconds --}}
    <script>
    setTimeout(function() {
        document.getElementById('flash-success')?.remove();
        document.getElementById('flash-error')?.remove();
    }, 4000);

    // Toggle Dropdown on Click (Mobile Support)
    document.addEventListener('click', function(event) {
        const dropdown = document.querySelector('.nav-user-dropdown');
        if (!dropdown) return;
        
        const btn = dropdown.querySelector('.nav-user-btn');
        const menu = dropdown.querySelector('.nav-user-menu');
        
        if (btn.contains(event.target)) {
            const isVisible = menu.style.visibility === 'visible';
            menu.style.opacity = isVisible ? '0' : '1';
            menu.style.visibility = isVisible ? 'hidden' : 'visible';
            menu.style.transform = isVisible ? 'translateY(10px)' : 'translateY(0)';
        } else if (!menu.contains(event.target)) {
            menu.style.opacity = '0';
            menu.style.visibility = 'hidden';
            menu.style.transform = 'translateY(10px)';
        }
    });
    </script>
</body>
</html>
