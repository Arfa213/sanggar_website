<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanggar Mulya Bhakti - @yield('title', 'Melestarikan Budaya Melalui Seni')</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/logosanggar.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

    {{-- NAVBAR --}}
    <nav class="navbar" id="navbar">
        <div class="container navbar-inner">
            <a href="{{ route('home') }}" class="navbar-brand">Sanggar Mulya Bhakti</a>
            <ul class="navbar-menu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Beranda</a></li>
                <li><a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">Profile</a></li>
                <li><a href="{{ route('event') }}" class="{{ request()->routeIs('event') ? 'active' : '' }}">Event</a></li>
                <li><a href="{{ route('digital-archive') }}" class="{{ request()->routeIs('digital-archive') ? 'active' : '' }}">Digital Archive</a></li>
            </ul>
            <div class="navbar-actions">
                <a href="{{ route('login') }}" class="btn-masuk">Masuk</a>
                <a href="{{ route('register') }}" class="btn-daftar">Daftar Anggota</a>
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
            <li><a href="{{ route('profile') }}">Profile</a></li>
            <li><a href="{{ route('event') }}">Event</a></li>
            <li><a href="{{ route('digital-archive') }}">Digital Archive</a></li>
            <li><a href="{{ route('login') }}">Masuk</a></li>
            <li><a href="{{ route('register') }}" class="btn-daftar">Daftar Anggota</a></li>
        </ul>
    </div>

    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="footer">
        <div class="container footer-grid">
            <div class="footer-brand">
                <h3>Sanggar Mulya Bhakti</h3>
                <p>Melestarikan budaya Indonesia melalui seni tari tradisional dengan sentuhan modern.</p>
            </div>
            <div class="footer-links">
                <h4>Link Cepat</h4>
                <ul>
                    <li><a href="{{ route('profile') }}">Tentang Kami</a></li>
                    <li><a href="{{ route('profile') }}">Profile sanggar</a></li>
                    <li><a href="{{ route('event') }}">Event</a></li>
                    <li><a href="{{ route('digital-archive') }}">Digital Archive</a></li>
                </ul>
            </div>
            <div class="footer-kontak">
                <h4>Kontak</h4>
                <ul>
                    <li>📍 Indramayu, Jawa Barat</li>
                    <li>📞 +62 812 3456 7890</li>
                    <li>✉️ wangiindriyaantahamahadi@gmail.com</li>
                </ul>
            </div>
            <div class="footer-sosmed">
                <h4>Ikuti Kami</h4>
                <div class="sosmed-icons">
                    <a href="#" aria-label="Instagram">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="#" aria-label="Facebook">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                    </a>
                    <a href="#" aria-label="YouTube">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 0 0-1.95 1.96A29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58A2.78 2.78 0 0 0 3.41 19.54C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/></svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© {{ date('Y') }} Sanggar Mulya Bhakti. All rights reserved.</p>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>