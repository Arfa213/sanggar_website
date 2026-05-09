<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Admin — @yield('title','Dashboard') | Sanggar Mulya Bhakti</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@stack('styles')
</head>
<body class="admin-body">

{{-- ── SIDEBAR ── --}}
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <div class="logo-icon">SMB</div>
            <div class="logo-text">
                <span class="logo-title">Sanggar Mulya</span>
                <span class="logo-sub">Admin Panel</span>
            </div>
        </div>
        <button class="sidebar-close" id="sidebarClose">✕</button>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-group">
            <span class="nav-label">UTAMA</span>
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
        </div>
        <div class="nav-group">
            <span class="nav-label">KONTEN WEB</span>
            <a href="{{ route('admin.profil.index') }}" class="nav-item {{ request()->routeIs('admin.profil*','admin.pelatih*','admin.pengelola*','admin.jadwal*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Profil Sanggar
            </a>
            <a href="{{ route('admin.event.index') }}" class="nav-item {{ request()->routeIs('admin.event*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Event & Pentas
                <span class="nav-badge">{{ \App\Models\Event::mendatang()->count() }}</span>
            </a>
            <a href="{{ route('admin.tarian.index') }}" class="nav-item {{ request()->routeIs('admin.tarian*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
                Arsip Tarian
            </a>
            <a href="{{ route('admin.topeng.index') }}" class="nav-item {{ request()->routeIs('admin.topeng*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a10 10 0 0 1 10 10c0 5.523-4.477 10-10 10S2 17.523 2 12 6.477 2 12 2z"/><path d="M12 14c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4z"/></svg>
                Koleksi Topeng
            </a>
            <a href="{{ route('admin.galeri.index') }}" class="nav-item {{ request()->routeIs('admin.galeri*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                Galeri & Media
            </a>
        </div>
        <div class="nav-group">
            <span class="nav-label">MANAJEMEN</span>
            <a href="{{ route('admin.anggota.index') }}" class="nav-item {{ request()->routeIs('admin.anggota*') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Anggota
                <span class="nav-badge">{{ \App\Models\User::where('role','anggota')->count() }}</span>
            </a>
        </div>
        <div class="nav-group">
            <span class="nav-label">KEHADIRAN</span>
            <a href="{{ route('admin.kehadiran.index') }}" class="nav-item {{ request()->routeIs('admin.kehadiran.index', 'admin.kehadiran.input') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Kelola Kehadiran
            </a>
            <a href="{{ route('admin.kehadiran.laporan') }}" class="nav-item {{ request()->routeIs('admin.kehadiran.laporan') ? 'active' : '' }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Laporan Kehadiran
            </a>
        </div>
        <div class="nav-group" style="margin-top:auto; border-top:1px solid rgba(255,255,255,.1); padding-top:16px">
            <a href="{{ route('home') }}" target="_blank" class="nav-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Lihat Website
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item nav-item--btn">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </nav>
</aside>

{{-- ── OVERLAY ── --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

{{-- ── MAIN ── --}}
<div class="admin-main">
    {{-- TOPBAR --}}
    <header class="topbar">
        <div class="topbar-left">
            <button class="topbar-toggle" id="sidebarToggle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <div class="breadcrumb">
                <span>Admin</span>
                <span class="bc-sep">›</span>
                <span class="bc-current">@yield('title','Dashboard')</span>
            </div>
        </div>
        <div class="topbar-right">
            <div class="topbar-user">
                @if(Auth::user()->foto)
                    <img src="{{ asset('storage/'.Auth::user()->foto) }}" style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:1px solid var(--border)">
                @else
                    <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</div>
                @endif
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">Administrator</span>
                </div>
            </div>
        </div>
    </header>

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
    <div class="flash flash--success" id="flashMsg">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
        {{ session('success') }}
        <button onclick="this.parentElement.remove()" class="flash-close">✕</button>
    </div>
    @endif
    @if(session('error'))
    <div class="flash flash--error" id="flashMsg">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        {{ session('error') }}
        <button onclick="this.parentElement.remove()" class="flash-close">✕</button>
    </div>
    @endif

    {{-- PAGE CONTENT --}}
    <div class="admin-content">
        @yield('content')
    </div>
</div>

<script src="{{ asset('js/admin.js') }}"></script>
@stack('scripts')
</body>
</html>