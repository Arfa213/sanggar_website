@extends('admin.layouts.app')
@section('title','Galeri & Media')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Galeri & Media</h1>
        <p>Kelola foto dan video yang ditampilkan di berbagai bagian website.</p>
    </div>
</div>

{{-- UPLOAD FORM --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px">
    <div class="card">
        <div class="card-header"><span class="card-title">Upload Media Baru</span></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.galeri.store') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
            <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:12px;margin-bottom:16px;color:#DC2626;font-size:.875rem">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
            @endif
            
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label>Seksi / Bagian Tampilan <span class="required">*</span></label>
                    <select name="seksi" class="form-control" required>
                        <option value="hero">🌟 Hero / Banner Utama (Header)</option>
                        <option value="digital_archive">🎭 Digital Archive (Dokumentasi Seni)</option>
                        <option value="dokumentasi">📸 Dokumentasi (Kegiatan Sanggar)</option>
                        <option value="about">ℹ️ Tentang Kami (Profil)</option>
                    </select>
                    <span class="hint" style="font-size:.75rem;margin-top:4px;display:block;color:var(--primary)">Pilih di mana media ini akan muncul.</span>
                </div>
                <div class="form-group">
                    <label>Tipe Media</label>
                    <select name="tipe" class="form-control">
                        <option value="foto">🖼 Foto / Gambar</option>
                        <option value="video">🎬 Video (MP4)</option>
                    </select>
                </div>
                <div class="form-group" style="grid-column:span 2">
                    <label>Judul Media (Opsional)</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Penampilan Tari Topeng 2024">
                </div>
                <div class="form-group" style="grid-column:span 2">
                    <label>File Media <span class="required">*</span></label>
                    <input type="file" name="file" class="form-control" accept="image/*,video/mp4" required>
                    <span class="hint">Foto: JPG/PNG/WebP max 10MB | Video: MP4 max 20MB</span>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;margin-top:16px">Unggah ke Galeri ↑</button>
            </form>
        </div>
    </div>

    <div class="card" style="background: linear-gradient(to bottom, #FFFBF9, #FFF8F6);">
        <div class="card-header"><span class="card-title">💡 Panduan Seksi</span></div>
        <div class="card-body" style="font-size:.85rem;line-height:1.5">
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:12px">
                <li><strong>🌟 Hero / Banner</strong>: Gambar besar yang muncul di paling atas halaman utama (Beranda).</li>
                <li><strong>🎭 Digital Archive</strong>: Dokumentasi khusus untuk arsip seni, tarian, dan sejarah tradisional.</li>
                <li><strong>📸 Dokumentasi</strong>: Foto atau video kegiatan sehari-hari, latihan, atau event umum sanggar.</li>
                <li><strong>ℹ️ Tentang Kami</strong>: Gambar yang ditampilkan pada bagian profil atau sejarah sanggar.</li>
            </ul>
        </div>
    </div>
</div>

{{-- GALERI TABS BY SECTION --}}
<div data-tabs>
<div class="tabs">
    <button class="tab-btn active" data-tab="gt-dokumentasi">📸 Dokumentasi</button>
    <button class="tab-btn" data-tab="gt-digital_archive">🎭 Digital Archive</button>
    <button class="tab-btn" data-tab="gt-hero">🌟 Hero/Banner</button>
    <button class="tab-btn" data-tab="gt-about">ℹ️ Tentang Kami</button>
</div>

@foreach(['dokumentasi','digital_archive','hero','about'] as $seksi)
@php $items = $grouped[$seksi] ?? collect(); @endphp
<div id="gt-{{ $seksi }}" class="tab-panel {{ $seksi==='dokumentasi'?'active':'' }}">
    @if($items->isEmpty())
    <div class="empty-state">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        <h3>Belum ada media</h3>
        <p>Upload foto/video untuk seksi ini menggunakan form di atas.</p>
    </div>
    @else
    <div class="galeri-grid">
        @foreach($items as $item)
        <div class="galeri-item">
            @if($item->tipe === 'foto')
                <img src="{{ asset('storage/'.$item->file) }}" alt="{{ $item->judul }}">
            @else
                <div class="galeri-placeholder">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/></svg>
                    <span>{{ $item->judul ?? 'Video' }}</span>
                </div>
            @endif
            <div class="galeri-delete-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
            </div>
            <div class="galeri-item-overlay">
                <div class="overlay-content">
                    @if($item->judul)
                    <p class="overlay-title">{{ Str::limit($item->judul, 30) }}</p>
                    @endif
                    <form method="POST" action="{{ route('admin.galeri.destroy',$item->id) }}" style="display:inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-delete" title="Hapus media ini" onclick="return confirm('Yakin hapus media ini?')">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
                <span class="chip chip--{{ $item->aktif ? 'green' : 'gray' }}" style="font-size:.65rem">{{ $item->aktif ? 'Tampil' : 'Sembunyi' }}</span>
            </div>
        </div>
        @endforeach
    </div>
    <p style="margin-top:16px;font-size:.8rem;color:var(--muted)">{{ $items->count() }} media di seksi ini</p>
    @endif
</div>
@endforeach
</div>

@endsection