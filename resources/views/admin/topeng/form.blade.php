@extends('admin.layouts.app')
@section('title', $mode==='create' ? 'Tambah Topeng' : 'Edit Topeng')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>{{ $mode==='create' ? 'Tambah Topeng Baru' : 'Edit: '.$topeng->nama }}</h1>
    </div>
    <a href="{{ route('admin.topeng.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<form method="POST"
      action="{{ $mode==='create' ? route('admin.topeng.store') : route('admin.topeng.update',$topeng->id) }}"
      enctype="multipart/form-data">
@csrf
@if($mode==='edit') @method('PUT') @endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start">
    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
            <div class="card-header"><span class="card-title">Informasi Topeng</span></div>
            <div class="card-body">
                @if($errors->any())
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:14px;margin-bottom:20px;color:#DC2626;font-size:.875rem">
                    <ul style="padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif
                <div class="form-grid">
                    <div class="form-group">
                        <label>Nama Topeng <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-control" value="{{ old('nama',$topeng->nama) }}" placeholder="Contoh: Topeng Kelana" required>
                    </div>
                    <div class="form-group">
                        <label>Warna <span class="required">*</span></label>
                        <input type="text" name="warna" class="form-control" value="{{ old('warna',$topeng->warna) }}" placeholder="Contoh: Merah, Putih, Hijau" required>
                    </div>
                    <div class="form-group" style="grid-column: span 2">
                        <label>Karakter / Watak <span class="required">*</span></label>
                        <input type="text" name="karakter" class="form-control" value="{{ old('karakter',$topeng->karakter) }}" placeholder="Contoh: Gagah Perkasa, Sabar dan Tulus" required>
                    </div>
                </div>
                <div class="form-group" style="margin-top:16px">
                    <label>Filosofi</label>
                    <textarea name="filosofi" class="form-control" rows="4" placeholder="Jelaskan makna dan filosofi di balik topeng ini...">{{ old('filosofi',$topeng->filosofi) }}</textarea>
                </div>
                <div class="form-group" style="margin-top:16px">
                    <label>Deskripsi Singkat</label>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Tuliskan deskripsi tambahan...">{{ old('deskripsi',$topeng->deskripsi) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
            <div class="card-header"><span class="card-title">Foto Topeng</span></div>
            <div class="card-body">
                <div class="file-upload-area">
                    <input type="file" name="foto" accept="image/*">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <p><strong>Klik atau seret</strong> foto ke sini</p>
                </div>
                <div class="file-preview" style="{{ $topeng->foto ? '' : 'display:none' }}">
                    <div style="position:relative; width:100%; aspect-ratio:1/1; overflow:hidden; border-radius:12px; border:1px solid var(--border); background:var(--bg); margin-top:12px;">
                        <img src="{{ $topeng->foto ? asset('storage/'.$topeng->foto) : '' }}" alt="Pratinjau" style="width:100%; height:100%; object-fit:contain;">
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><span class="card-title">Pengaturan</span></div>
            <div class="card-body" style="display:flex;flex-direction:column;gap:14px">
                <label class="form-check">
                    <input type="checkbox" name="aktif" value="1" {{ old('aktif', $topeng->aktif ?? true) ? 'checked' : '' }}>
                    <span class="form-check-label">Tampilkan di website</span>
                </label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
            💾 {{ $mode==='create' ? 'Simpan Topeng' : 'Perbarui Topeng' }}
        </button>
        <a href="{{ route('admin.topeng.index') }}" class="btn btn-secondary" style="width:100%;justify-content:center">Batal</a>
    </div>
</div>
</form>
@endsection
