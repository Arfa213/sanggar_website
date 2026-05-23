@extends('layouts.app')
@section('title', 'Profile Sanggar')
@section('content')

{{-- HERO --}}
<section class="page-hero">
    <div class="page-hero__bg"></div>
    <div class="container page-hero__inner">
        <span class="badge">Profile Sanggar</span>
        <h1 class="page-hero__title">{{ $profil->nama_sanggar }}</h1>
        <p class="page-hero__sub">{{ $profil->tagline ?? 'Melestarikan warisan budaya tari tradisional Indonesia.' }}</p>
        <div class="page-hero__nav">
            <a href="#sejarah"   class="phero-nav-link">Sejarah</a>
            <a href="#visi-misi" class="phero-nav-link">Visi &amp; Misi</a>
            <a href="#pelatih"   class="phero-nav-link">Pelatih</a>
            <a href="#pengelola" class="phero-nav-link">Pengelola</a>
            <a href="#kegiatan"  class="phero-nav-link">Kegiatan</a>
        </div>
    </div>
</section>

{{-- SEJARAH --}}
<section class="section" id="sejarah">
    <div class="container">
        <div class="split-layout">
            <div class="split-image">
                @if($profil->foto_sejarah)
                    <img src="{{ asset('storage/'.$profil->foto_sejarah) }}"
                         alt="Sejarah {{ $profil->nama_sanggar }}"
                         class="img-placeholder--tall"
                         style="width:100%;height:480px;object-fit:cover;border-radius:var(--radius);box-shadow:var(--shadow-md)">
                @else
                    <div class="img-placeholder img-placeholder--tall">
                        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                @endif
                @if($profil->tahun_berdiri)
                <div class="floating-stat">
                    <span class="fs-year">EST.</span>
                    <span class="fs-num">{{ $profil->tahun_berdiri }}</span>
                    <span class="fs-label">Tahun Berdiri</span>
                </div>
                @endif
            </div>
            <div class="split-text">
                <span class="badge">Sejarah Kami</span>
                <h2 class="section-heading" style="text-align:left;margin-bottom:16px">Perjalanan Panjang Pelestarian Budaya</h2>
                @foreach(explode("\n", $profil->sejarah ?? '') as $para)
                    @if(trim($para))
                        <p style="margin-bottom:14px">{{ trim($para) }}</p>
                    @endif
                @endforeach
                <div class="stat-row">
                    <div class="stat-pill"><strong>{{ $profil->jumlah_anggota ?? 0 }}+</strong><span>Anggota</span></div>
                    <div class="stat-pill"><strong>{{ $profil->jumlah_penghargaan ?? 0 }}+</strong><span>Penghargaan</span></div>
                    <div class="stat-pill"><strong>{{ $profil->jumlah_event ?? 0 }}+</strong><span>Event</span></div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- VISI MISI --}}
<section class="section section--alt" id="visi-misi">
    <div class="container">
        <div class="section-header">
            <span class="badge">Arah &amp; Tujuan</span>
            <h2 class="section-heading">Visi &amp; Misi</h2>
        </div>
        <div class="vm-grid">
            <div class="vm-card--visi">
                <div class="vm-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </div>
                <h3>Visi</h3>
                <p>{{ $profil->visi ?? '-' }}</p>
            </div>
            <div class="vm-misi">
                <h3>Misi</h3>
                <ul class="misi-list">
                    @foreach($profil->misi ?? [] as $i => $m)
                    <li>
                        <span class="misi-num">{{ str_pad($i+1, 2, '0', STR_PAD_LEFT) }}</span>
                        <div>{{ $m }}</div>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- PELATIH --}}
<section class="section" id="pelatih">
    <div class="container">
        <div class="section-header">
            <span class="badge">Tim Kami</span>
            <h2 class="section-heading">Para Pelatih</h2>
            <p class="section-sub">Dipandu oleh para seniman berpengalaman yang berdedikasi dalam mencetak penari-penari berbakat.</p>
        </div>
        @if($pelatih->count())
        <div class="people-grid">
            @foreach($pelatih as $p)
            <div class="person-card">
                @if($p->foto)
                    <img src="{{ asset('storage/'.$p->foto) }}" alt="{{ $p->nama }}"
                         class="img-placeholder--circle"
                         style="object-fit:cover;width:100px;height:100px;border-radius:50%">
                @else
                    <div class="img-placeholder img-placeholder--circle">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                @endif
                <div class="person-name">{{ $p->nama }}</div>
                <span class="person-jabatan">{{ $p->jabatan }}</span>
                <div class="person-tags">
                    @if($p->spesialisasi)<span class="ptag">{{ $p->spesialisasi }}</span>@endif
                    @if($p->pengalaman)<span class="ptag ptag--muted">{{ $p->pengalaman }}</span>@endif
                </div>
                @if($p->bio)
                    <p style="font-size:.8rem;color:var(--muted);text-align:center;line-height:1.6">{{ $p->bio }}</p>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <p style="text-align:center;color:var(--muted)">Belum ada data pelatih.</p>
        @endif
    </div>
</section>

{{-- PENGELOLA --}}
<section class="section section--alt" id="pengelola">
    <div class="container">
        <div class="section-header">
            <span class="badge">Struktur Organisasi</span>
            <h2 class="section-heading">Pengelola Sanggar</h2>
        </div>
        @php
        $icons = [
            'crown'     => '<path d="M2 20h20M5 20V10l7-7 7 7v10"/>',
            'edit'      => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
            'briefcase' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>',
            'star'      => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
            'calendar'  => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
            'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        ];
        @endphp
        @if($pengelola->count())
        <div class="org-grid">
            @foreach($pengelola as $pg)
            <div class="org-card">
                <div class="org-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="2">{!! $icons[$pg->ikon] ?? $icons['star'] !!}</svg>
                </div>
                @if($pg->foto)
                    <img src="{{ asset('storage/'.$pg->foto) }}" alt="{{ $pg->nama }}"
                         class="img-placeholder--circle img-placeholder--sm"
                         style="object-fit:cover;width:64px;height:64px;border-radius:50%">
                @else
                    <div class="img-placeholder img-placeholder--circle img-placeholder--sm">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                @endif
                <div class="org-name">{{ $pg->nama }}</div>
                <span class="org-jabatan">{{ $pg->jabatan }}</span>
            </div>
            @endforeach
        </div>
        @else
        <p style="text-align:center;color:var(--muted)">Belum ada data pengelola.</p>
        @endif
    </div>
</section>

{{-- KEGIATAN & JADWAL --}}
<section class="section" id="kegiatan">
    <div class="container">
        <div class="section-header">
            <span class="badge">Aktivitas</span>
            <h2 class="section-heading">Informasi Kegiatan &amp; Event</h2>
        </div>

        {{-- Jadwal Latihan --}}
        @if($jadwal->count())
        <div class="kegiatan-block">
            <div class="kegiatan-block__header">
                <div class="kb-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h3>Jadwal Latihan Rutin</h3>
            </div>
            <div class="jadwal-grid">
                @foreach($jadwal as $j)
                <div class="jadwal-card">
                    <span class="jadwal-hari">{{ $j->hari }}</span>
                    <div class="jadwal-detail">
                        <span class="jadwal-kelas">{{ $j->kelas }}</span>
                        <span class="jadwal-meta">⏰ {{ $j->jam_mulai }} – {{ $j->jam_selesai }} &nbsp;·&nbsp; 📍 {{ $j->tempat }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Event Selesai / Track Record --}}
        @if($pastEvents->count())
        <div class="kegiatan-block" style="margin-top:48px">
            <div class="kegiatan-block__header">
                <div class="kb-icon kb-icon--purple">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                </div>
                <h3>Rekam Jejak &amp; Portofolio Event</h3>
            </div>
            <div class="event-upcoming-list">
                @foreach($pastEvents->take(5) as $ev)
                <div class="eu-item" style="border-left: 4px solid #C65D2E; background: #fff;">
                    <div class="eu-date" style="background: #FAF9F6; color: #1e1b4b;">
                        <span class="eu-day">{{ $ev->tanggal->format('d') }}</span>
                        <span class="eu-month">{{ $ev->tanggal->isoFormat('MMM YYYY') }}</span>
                    </div>
                    <div class="eu-info">
                        <h4>{{ $ev->nama }}</h4>
                        <span class="eu-meta">📍 {{ $ev->lokasi }}</span>
                        @if($ev->nama_pengaju)
                            <span class="eu-meta" style="color: #6366f1; margin-top: 4px; display: block; font-weight: 600;">Kolaborasi dengan: {{ $ev->nama_pengaju }}</span>
                        @endif
                    </div>
                    <div class="eu-right">
                        <span class="eu-tipe" style="background: #f1f5f9; color: #475569;">{{ ucfirst(str_replace('_', ' ', $ev->kategori)) }}</span>
                        @if($ev->hasil)<span class="eu-status" style="background: #fef3c7; color: #d97706;">🏆 {{ $ev->hasil }}</span>@endif
                    </div>
                </div>
                @endforeach
            </div>
            @if($pastEvents->count() > 5)
            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ route('digital-archive') }}" class="btn-cta" style="background: transparent; color: #C65D2E; border: 2px solid #C65D2E; padding: 10px 24px;">Lihat Seluruh Arsip Event</a>
            </div>
            @endif
        </div>
        @endif

    </div>
</section>

<section class="cta">
    <div class="container cta-inner">
        <h2>Ingin Bergabung Bersama Kami?</h2>
        <p>Daftarkan diri Anda dan mulai perjalanan seni budaya bersama {{ $profil->nama_sanggar }}.</p>
        <a href="{{ route('register') }}" class="btn-cta">Daftar Sekarang</a>
    </div>
</section>
@endsection
