@extends('layouts.app')
@section('title', $profil->nama_sanggar ?? 'Beranda')
@section('content')

{{-- HERO --}}
<section class="hero">
    <div class="container hero-inner">
        <div class="hero-text">
            <span class="badge">Sanggar Seni Tradisional</span>
            <h1 class="hero-title">{{ $profil->tagline ?? 'Melestarikan Budaya Melalui Seni' }}</h1>
            <p class="hero-desc">
                Bergabunglah dengan komunitas pecinta seni tari tradisional Indonesia.
                Belajar, berkreasi, dan lestarikan warisan budaya bersama kami.
            </p>
            <a href="{{ route('register') }}" class="btn-primary">Daftar Anggota →</a>
        </div>
        <div class="hero-image">
            <div class="hero-img-wrapper">
                @php $heroFoto = $galeri->where('seksi','hero')->first(); @endphp
                @if($heroFoto)
                    <img src="{{ asset('storage/'.$heroFoto->file) }}"
                         alt="{{ $profil->nama_sanggar }}"
                         class="hero-placeholder">
                @else
                    <div class="img-placeholder hero-placeholder">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                @endif
                <div class="hero-badge-float">
                    <span class="float-number">{{ $profil->jumlah_penghargaan ?? 0 }}+</span>
                    <span class="float-title">Penghargaan</span>
                    <span class="float-sub">Tingkat Nasional</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="stats">
    <div class="container stats-grid">
        <div class="stat-item">
            <div class="stat-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-number">{{ $profil->jumlah_anggota ?? 0 }}+</div>
            <div class="stat-label">Anggota Aktif</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
            </div>
            <div class="stat-number">{{ $profil->jumlah_penghargaan ?? 0 }}+</div>
            <div class="stat-label">Penghargaan</div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div class="stat-number">{{ $profil->jumlah_event ?? 0 }}+</div>
            <div class="stat-label">Menghadiri Event</div>
        </div>
    </div>
</section>

{{-- TENTANG KAMI --}}
<section class="about">
    <div class="container about-inner">
        <div class="about-image">
            @php $aboutFoto = $galeri->where('seksi','about')->first(); @endphp
            @if($aboutFoto)
                <img src="{{ asset('storage/'.$aboutFoto->file) }}"
                     alt="Tentang {{ $profil->nama_sanggar }}"
                     class="about-placeholder"
                     style="object-fit:cover;width:100%;border-radius:var(--radius)">
            @elseif($profil->foto_sejarah)
                <img src="{{ asset('storage/'.$profil->foto_sejarah) }}"
                     alt="Tentang {{ $profil->nama_sanggar }}"
                     class="about-placeholder"
                     style="object-fit:cover;width:100%;border-radius:var(--radius)">
            @else
                <div class="img-placeholder about-placeholder">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </div>
            @endif
        </div>
        <div class="about-text">
            <span class="badge">Tentang Kami</span>
            <h2>{{ $profil->nama_sanggar }}</h2>
            @if($profil->tahun_berdiri)
                <p class="about-subtitle">BERDIRI SEJAK {{ $profil->tahun_berdiri }}</p>
            @endif
            <p class="about-desc">
                {{ Str::limit($profil->sejarah ?? 'Sanggar seni tari tradisional yang berdedikasi melestarikan kekayaan budaya Indonesia.', 280) }}
            </p>
            <a href="{{ route('profile') }}" class="btn-primary">Selengkapnya →</a>
        </div>
    </div>
</section>

{{-- KOLEKSI TOPENG (3D EFFECT) --}}
<section class="topeng-section" style="padding: 80px 0; background: linear-gradient(to bottom, var(--white), var(--bg-soft)); overflow: hidden;">
    <div class="container">
        <div class="section-header" style="text-align: center; margin-bottom: 50px;">
            <span class="badge">Koleksi Tradisional</span>
            <h2 style="font-family: var(--font-display); font-size: 2.5rem; font-weight: 700; color: var(--dark); margin-top: 10px;">Pancawanda: 5 Karakter Topeng</h2>
            <p style="color: var(--muted); max-width: 600px; margin: 15px auto 0;">Mengenal lima watak manusia melalui warna dan rupa topeng tradisional Indramayu.</p>
        </div>

        <div class="topeng-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 30px; perspective: 1000px;">
            @foreach($topeng as $t)
            <div class="topeng-card-3d" style="position: relative; height: 350px; transition: transform 0.6s; transform-style: preserve-3d; cursor: pointer;">
                <div class="topeng-card-inner" style="position: relative; width: 100%; height: 100%; text-align: center; transition: transform 0.8s; transform-style: preserve-3d;">
                    
                    {{-- FRONT --}}
                    <div class="topeng-front" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border-radius: 24px; background: white; box-shadow: 0 15px 35px rgba(0,0,0,0.08); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; border: 1px solid rgba(0,0,0,0.03);">
                        <div class="topeng-img-wrap" style="width: 160px; height: 160px; margin-bottom: 20px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1)); transition: transform 0.5s;">
                            @if($t->foto)
                                <img src="{{ asset('storage/'.$t->foto) }}" alt="{{ $t->nama }}" style="width: 100%; height: 100%; object-fit: contain;">
                            @else
                                <div style="width: 100%; height: 100%; background: #f0f0f0; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/><circle cx="12" cy="10" r="3"/></svg>
                                </div>
                            @endif
                        </div>
                        <h3 style="font-family: var(--font-display); font-size: 1.4rem; margin-bottom: 5px; color: var(--dark);">{{ $t->nama }}</h3>
                        <span class="badge" style="background: rgba(0,0,0,0.05); color: var(--dark); border: none;">{{ $t->warna }}</span>
                    </div>

                    {{-- BACK --}}
                    <div class="topeng-back" style="position: absolute; width: 100%; height: 100%; backface-visibility: hidden; border-radius: 24px; background: var(--primary); color: white; transform: rotateY(180deg); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px; text-align: center;">
                        <h4 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px;">{{ $t->karakter }}</h4>
                        <p style="font-size: 0.9rem; line-height: 1.6; opacity: 0.9;">{{ $t->filosofi ?? $t->deskripsi }}</p>
                        <div style="margin-top: 20px; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">Watak {{ $t->nama }}</div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
    </div>

    <style>
        .topeng-card-3d:hover .topeng-card-inner {
            transform: rotateY(180deg);
        }
        .topeng-card-3d:hover .topeng-img-wrap {
            transform: scale(1.1) translateZ(50px);
        }
        @media (max-width: 768px) {
            .topeng-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 480px) {
            .topeng-grid { grid-template-columns: 1fr; }
        }
    </style>
</section>

{{-- DIGITAL ARCHIVE --}}
<section class="archive-section" style="padding: 60px 0; overflow: hidden; background: var(--bg-soft);">
    <div class="container">
        <span class="badge">Digital Archive</span>
        <div class="archive-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
            <h2 style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 700; color: var(--dark);">Arsip Digital</h2>
            <a href="{{ route('digital-archive') }}" class="btn-lihat-sm">Lihat Semua →</a>
        </div>
    </div>

    <!-- Infinite Marquee Carousel (Pure CSS) -->
    <div class="marquee-container" style="position: relative; width: 100vw; margin-left: calc(-50vw + 50%); overflow: hidden; padding: 10px 0;">
        <style>
            @keyframes smoothMarquee {
                0% { transform: translateX(0); }
                100% { transform: translateX(calc(-50% - 8px)); }
            }
            .marquee-track {
                display: flex;
                gap: 16px;
                width: max-content;
                animation: smoothMarquee 30s linear infinite;
            }
            .marquee-track:hover {
                animation-play-state: paused;
            }
            .marquee-item {
                width: 260px;
                height: 260px;
                border-radius: 16px;
                overflow: hidden;
                flex-shrink: 0;
                box-shadow: 0 10px 20px rgba(0,0,0,0.05);
                transition: transform 0.3s ease;
            }
            .marquee-item:hover {
                transform: translateY(-5px);
            }
        </style>
        
        <div class="marquee-track">
            @php 
                $archiveFotos = $galeri->where('seksi','digital_archive'); 
                // Jika foto kurang dari 6, kita perbanyak sementara agar efek marquee-nya tidak putus
                $displayFotos = collect();
                if($archiveFotos->count() > 0) {
                    while($displayFotos->count() < 10) {
                        $displayFotos = $displayFotos->concat($archiveFotos);
                    }
                }
            @endphp

            @if($displayFotos->count())
                {{-- Loop 2x untuk ilusi infinite scrolling tanpa patah --}}
                @for($k=0; $k<2; $k++)
                    @foreach($displayFotos->take(8) as $foto)
                    <a href="{{ route('digital-archive') }}" class="marquee-item">
                        <img src="{{ asset('storage/'.$foto->file) }}"
                             alt="{{ $foto->judul ?? 'Arsip Digital' }}"
                             style="width:100%;height:100%;object-fit:cover;">
                    </a>
                    @endforeach
                @endfor
            @else
                @for($k=0; $k<12; $k++)
                <a href="{{ route('digital-archive') }}" class="marquee-item" style="display:flex;align-items:center;justify-content:center;background:var(--primary-pale);">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                </a>
                @endfor
            @endif
        </div>
    </div>
</section>

{{-- DOKUMENTASI (BENTO GRID) --}}
<section class="dokumentasi" style="padding: 60px 0;">
    <div class="container">
        <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 30px;">
            <div>
                <span class="badge" style="margin-bottom:8px; display:inline-block;">Kegiatan Terbaru</span>
                <h2 class="section-title" style="margin-bottom: 0; font-family: var(--font-display); font-weight: 700; color: var(--dark);">Dokumentasi Kegiatan</h2>
            </div>
            <a href="{{ route('galeri.frontend.index', 'dokumentasi') }}" class="btn-lihat-sm" style="display: inline-flex; align-items: center; gap: 8px; background: var(--primary); color: white; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 12px rgba(198,93,46,0.2);">
                Lihat Semua Galeri
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>

        <style>
            .bento-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                grid-template-rows: repeat(2, 220px);
                gap: 16px;
            }
            .bento-item {
                border-radius: 20px;
                overflow: hidden;
                position: relative;
                box-shadow: 0 8px 24px rgba(0,0,0,0.06);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .bento-item:hover {
                transform: translateY(-4px);
                box-shadow: 0 12px 30px rgba(0,0,0,0.12);
            }
            .bento-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.5s ease;
            }
            .bento-item:hover img {
                transform: scale(1.05);
            }
            /* Bento Layout Assignments */
            .bento-1 { grid-column: span 2; grid-row: span 2; }
            .bento-2 { grid-column: span 1; grid-row: span 1; }
            .bento-3 { grid-column: span 1; grid-row: span 1; }
            .bento-4 { grid-column: span 2; grid-row: span 1; }

            /* Mobile Responsiveness */
            @media (max-width: 768px) {
                .bento-grid {
                    grid-template-columns: repeat(2, 1fr);
                    grid-template-rows: auto;
                    grid-auto-rows: 200px;
                }
                .bento-1 { grid-column: span 2; grid-row: span 1; }
                .bento-4 { grid-column: span 2; grid-row: span 1; }
            }
        </style>

        <div class="bento-grid">
            @php
                $dokFotos = isset($galeri)
                    ? $galeri->where('seksi', 'dokumentasi')->where('tipe', 'foto')->where('aktif', true)->values()
                    : collect();
            @endphp

            @if($dokFotos->count() >= 4)
                <a href="{{ route('galeri.frontend.index', 'dokumentasi') }}" class="bento-item bento-1">
                    <img src="{{ asset('storage/' . $dokFotos[0]->file) }}" alt="Dokumentasi 1">
                </a>
                <a href="{{ route('galeri.frontend.index', 'dokumentasi') }}" class="bento-item bento-2">
                    <img src="{{ asset('storage/' . $dokFotos[1]->file) }}" alt="Dokumentasi 2">
                </a>
                <a href="{{ route('galeri.frontend.index', 'dokumentasi') }}" class="bento-item bento-3">
                    <img src="{{ asset('storage/' . $dokFotos[2]->file) }}" alt="Dokumentasi 3">
                </a>
                <a href="{{ route('galeri.frontend.index', 'dokumentasi') }}" class="bento-item bento-4">
                    <img src="{{ asset('storage/' . $dokFotos[3]->file) }}" alt="Dokumentasi 4">
                </a>
            @else
                {{-- Fallback placeholders if less than 4 photos --}}
                @for($i = 1; $i <= 4; $i++)
                <div class="bento-item bento-{{ $i }}" style="background: var(--primary-pale); display: flex; align-items: center; justify-content: center;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <polyline points="21 15 16 10 5 21"/>
                    </svg>
                </div>
                @endfor
            @endif
        </div>
    </div>
</section>


{{-- CTA --}}
<section class="cta">
    <div class="container cta-inner">
        <h2>Bergabung dengan Komunitas Kami</h2>
        <p>Jadilah bagian dari gerakan pelestarian budaya Indramayu. Daftarkan diri Anda sekarang dan mulai perjalanan seni Anda bersama kami.</p>
        <a href="{{ route('register') }}" class="btn-cta">Daftar Sekarang</a>
    </div>
</section>

@endsection