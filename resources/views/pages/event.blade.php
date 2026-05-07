@extends('layouts.app')
@section('title', 'Event & Pentas')
@section('content')

{{-- HERO --}}
<section class="page-hero">
    <div class="page-hero__bg"></div>
    <div class="container page-hero__inner">
        <span class="badge">Jejak Prestasi</span>
        <h1 class="page-hero__title">Event &amp; Pentas</h1>
        <p class="page-hero__sub">Rekam jejak perjalanan sanggar dalam berbagai event bergengsi dari tingkat lokal hingga internasional.</p>
        <div class="event-hero-stats">
            <div class="ehs-item"><strong>{{ $stats['total'] }}</strong><span>Total Event</span></div>
            <div class="ehs-sep"></div>
            <div class="ehs-item"><strong>{{ $stats['internasional'] }}</strong><span>Internasional</span></div>
            <div class="ehs-sep"></div>
            <div class="ehs-item"><strong>{{ $stats['nasional_lokal'] }}</strong><span>Nasional &amp; Lokal</span></div>
            <div class="ehs-sep"></div>
            <div class="ehs-item"><strong>{{ $stats['penghargaan'] }}</strong><span>Penghargaan</span></div>
        </div>
    </div>
</section>

{{-- FILTER --}}
@if($stats['total'] > 1)
<div class="filter-bar" id="filterBar">
    <div class="container filter-bar__inner">
        <button class="filter-btn active" data-filter="semua">Semua</button>
        <button class="filter-btn" data-filter="internasional">🌏 Internasional</button>
        <button class="filter-btn" data-filter="nasional">🇮🇩 Nasional</button>
        <button class="filter-btn" data-filter="festival">🎭 Festival</button>
        <button class="filter-btn" data-filter="pentas">🎤 Pentas</button>
        <button class="filter-btn" data-filter="kompetisi">🏆 Kompetisi</button>
    </div>
</div>
@endif

{{-- EVENT UNGGULAN --}}
@if($featured->count())
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="badge">Highlight</span>
            <h2 class="section-heading">Event Unggulan</h2>
        </div>
        <div class="featured-events">
            @foreach($featured->take(3) as $i => $ev)
            @php $catColor=['internasional'=>'event-cat--internasional','nasional'=>'event-cat--nasional','festival'=>'event-cat--festival','pentas'=>'event-cat--pentas','kompetisi'=>'event-cat--kompetisi']; @endphp
            <div class="fe-card {{ $i===0 ? 'fe-card--large' : '' }}" data-cat="{{ $ev->kategori }}">
                <div class="{{ $i===0 ? 'fe-image' : 'fe-image fe-image--sm' }}">
                    @if($ev->foto)
                        <img src="{{ asset('storage/'.$ev->foto) }}" alt="{{ $ev->nama }}"
                             style="width:100%;height:100%;object-fit:cover">
                    @else
                        <div class="img-placeholder" style="height:100%;border-radius:0">
                            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </div>
                    @endif
                </div>
                <div class="fe-body">
                    <div class="fe-meta">
                        <span class="event-cat {{ $catColor[$ev->kategori] ?? '' }}">{{ $ev->level }}</span>
                        <span class="event-year">{{ $ev->tahun }}</span>
                    </div>
                    <h3 class="fe-title">{{ $ev->nama }}</h3>
                    @if($ev->deskripsi)
                    <p class="fe-desc">{{ Str::limit($ev->deskripsi, $i===0 ? 200 : 100) }}</p>
                    @endif
                    @if($ev->penghargaan && count($ev->penghargaan))
                    <div class="fe-awards">
                        @foreach($ev->penghargaan as $aw)
                        <span class="award-badge">{{ $aw }}</span>
                        @endforeach
                    </div>
                    @endif
                    <div class="fe-footer">
                        <span>📍 {{ $ev->lokasi }}</span>
                        @if($ev->jumlah_penonton)<span>👥 {{ number_format($ev->jumlah_penonton) }}+ Penonton</span>@endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- TIMELINE SEMUA EVENT --}}
<section class="section section--alt">
    <div class="container">
        <div class="section-header">
            <span class="badge">Rekam Jejak</span>
            <h2 class="section-heading">Semua Event</h2>
        </div>

        @if($stats['total'] > 1 && $byYear->count())
        <div class="timeline" id="eventTimeline">
            @php $catColor=['internasional'=>'event-cat--internasional','nasional'=>'event-cat--nasional','festival'=>'event-cat--festival','pentas'=>'event-cat--pentas','kompetisi'=>'event-cat--kompetisi']; @endphp
            @foreach($byYear->sortKeysDesc() as $tahun => $evList)
            <div class="timeline-year">
                <div class="ty-label">{{ $tahun }}</div>
                <div class="ty-events">
                    @foreach($evList as $ev)
                    <div class="ty-event-card" data-cat="{{ $ev->kategori }}">
                        <div class="ty-event-left">
                            <span class="ty-bulan">{{ $ev->bulan }}</span>
                            <div class="ty-dot"></div>
                        </div>
                        <div class="ty-event-body">
                            <div class="ty-event-header">
                                <h4>{{ $ev->nama }}</h4>
                                <span class="event-cat {{ $catColor[$ev->kategori] ?? '' }}">{{ $ev->level }}</span>
                            </div>
                            <p class="ty-lokasi">📍 {{ $ev->lokasi }}</p>
                            @if($ev->hasil)
                            <span class="ty-hasil">{{ $ev->hasil }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p style="text-align:center;color:var(--muted)">Belum ada data event.</p>
        @endif
    </div>
</section>

{{-- EVENT MENDATANG --}}
@if($mendatang->count())
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="badge">Segera Hadir</span>
            <h2 class="section-heading">Event Mendatang</h2>
        </div>
        <div class="kegiatan-block">
            <div class="kegiatan-block__header">
                <div class="kb-icon kb-icon--purple">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                </div>
                <h3>Event yang Akan Diikuti</h3>
            </div>
            <div class="event-upcoming-list">
                @foreach($mendatang as $ev)
                <div class="eu-item">
                    <div class="eu-date">
                        <span class="eu-day">{{ $ev->tanggal->format('d') }}</span>
                        <span class="eu-month">{{ $ev->bulan }} {{ $ev->tahun }}</span>
                    </div>
                    <div class="eu-info">
                        <h4>{{ $ev->nama }}</h4>
                        <span class="eu-meta">📍 {{ $ev->lokasi }}</span>
                    </div>
                    <div class="eu-right">
                        <span class="eu-tipe">{{ ucfirst($ev->kategori) }}</span>
                        <span class="eu-status">✓ Terdaftar</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

{{-- PENGHARGAAN --}}
@if($awards->count())
<section class="section section--alt">
    <div class="container">
        <div class="section-header">
            <span class="badge">Prestasi</span>
            <h2 class="section-heading">Penghargaan &amp; Prestasi</h2>
        </div>
        <div class="award-grid">
            @foreach($awards as $ev)
            <div class="award-card">
                <span class="award-icon-lg">🏆</span>
                <div class="award-text">
                    <strong>{{ $ev->hasil }} — {{ $ev->nama }}</strong>
                    <span>{{ $ev->lokasi }} · {{ $ev->tahun }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="cta">
    <div class="container cta-inner">
        <h2>Jadilah Bagian dari Prestasi Kami</h2>
        <p>Bergabunglah dan torehkan prestasi bersama sanggar.</p>
        <a href="{{ route('register') }}" class="btn-cta">Daftar Anggota</a>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const btns  = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('[data-cat]');
    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            btns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const f = btn.dataset.filter;
            cards.forEach(c => {
                c.style.display = (f === 'semua' || c.dataset.cat === f) ? '' : 'none';
            });
        });
    });
});
</script>
@endsection
