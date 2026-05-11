@extends('admin.layouts.app')
@section('title', $mode==='create' ? 'Tambah Tarian' : 'Edit Tarian')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>{{ $mode==='create' ? 'Tambah Tarian Baru' : 'Edit: '.$tarian->nama }}</h1>
    </div>
    <a href="{{ route('admin.tarian.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<form method="POST"
      action="{{ $mode==='create' ? route('admin.tarian.store') : route('admin.tarian.update',$tarian->id) }}"
      enctype="multipart/form-data">
@csrf
@if($mode==='edit') @method('PUT') @endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start">
    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
            <div class="card-header"><span class="card-title">Informasi Tarian</span></div>
            <div class="card-body">
                @if($errors->any())
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:14px;margin-bottom:20px;color:#DC2626;font-size:.875rem">
                    <ul style="padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Tarian <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama',$tarian->nama) }}" placeholder="Tari Topeng Kelana" required>
                    </div>
                    <div class="form-group">
                        <label>Asal Daerah <span class="required">*</span></label>
                        <input type="text" name="asal" class="form-control" value="{{ old('asal',$tarian->asal) }}" placeholder="Indramayu, Jawa Barat" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori <span class="required">*</span></label>
                        <select name="kategori" class="form-control" required>
                            @foreach(['sakral','hiburan','penyambutan','ritual','perang'] as $k)
                            <option value="{{ $k }}" {{ old('kategori',$tarian->kategori)===$k?'selected':'' }}>{{ ucfirst($k) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kegiatan <span class="required">*</span></label>
                        <select name="jenis_kegiatan" class="form-control" required>
                            @foreach(['tari'=>'🩰 Tari','gamelan'=>'🥁 Gamelan','drama'=>'🎭 Drama','srimpi'=>'🌸 Srimpi'] as $val => $label)
                            <option value="{{ $val }}" {{ old('jenis_kegiatan',$tarian->jenis_kegiatan ?? 'tari')===$val?'selected':'' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Durasi</label>
                        <input type="text" name="durasi" class="form-control" value="{{ old('durasi',$tarian->durasi) }}" placeholder="15–30 menit">
                    </div>
                    <div class="form-group">
                        <label>Fungsi / Kegunaan</label>
                        <input type="text" name="fungsi" class="form-control" value="{{ old('fungsi',$tarian->fungsi) }}" placeholder="Pertunjukan Seni & Ritual">
                    </div>
                    <div class="form-group">
                        <label>Kostum</label>
                        <input type="text" name="kostum" class="form-control" value="{{ old('kostum',$tarian->kostum) }}" placeholder="Topeng merah, baju kebesaran">
                    </div>
                </div>
                <div class="form-group" style="margin-top:16px">
                    <label>Deskripsi Lengkap <span class="required">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="6" placeholder="Tuliskan cerita dan sejarah lengkap tarian ini..." required>{{ old('deskripsi',$tarian->deskripsi) }}</textarea>
                </div>
                <div class="form-group" style="margin-top:16px">
                    <label>URL Video YouTube (Embed)</label>
                    <input type="url" name="video_url" class="form-control" value="{{ old('video_url',$tarian->video_url) }}" placeholder="https://www.youtube.com/embed/xxxxx">
                    <span class="hint">Gunakan URL embed YouTube: youtube.com/embed/ID_VIDEO</span>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
            <div class="card-header"><span class="card-title">Foto Tarian</span></div>
            <div class="card-body">
                <div class="file-upload-area">
                    <input type="file" name="foto" accept="image/*">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <p><strong>Klik atau seret</strong> foto ke sini</p>
                </div>
                <div class="file-preview" style="{{ $tarian->foto ? '' : 'display:none' }}">
                    <div style="position:relative; width:100%; aspect-ratio:16/9; overflow:hidden; border-radius:12px; border:1px solid var(--border); background:var(--bg); margin-top:12px;">
                        <img src="{{ $tarian->foto ? asset('storage/'.$tarian->foto) : '' }}" alt="Pratinjau" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title">Pengaturan</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
                <label class="form-check">
                    <input type="checkbox" name="unggulan" value="1" {{ old('unggulan',$tarian->unggulan) ? 'checked' : '' }}>
                    <span class="form-check-label">★ Tandai sebagai Unggulan</span>
                </label>
                <label class="form-check">
                    <input type="checkbox" name="aktif" value="1" {{ old('aktif', $tarian->aktif ?? true) ? 'checked' : '' }}>
                    <span class="form-check-label">Tampilkan di website</span>
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
            💾 {{ $mode==='create' ? 'Simpan Tarian' : 'Perbarui Tarian' }}
        </button>
        <a href="{{ route('admin.tarian.index') }}" class="btn btn-secondary" style="width:100%;justify-content:center">Batal</a>
    </div>
</div>
</form>
@endsection