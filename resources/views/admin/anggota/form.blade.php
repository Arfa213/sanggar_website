@extends('admin.layouts.app')
@section('title', $mode==='create' ? 'Tambah Anggota' : 'Edit Anggota')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>{{ $mode==='create' ? 'Tambah Anggota Baru' : 'Edit: '.$anggota->name }}</h1>
    </div>
    <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div style="max-width:640px">
<form method="POST"
      action="{{ $mode==='create' ? route('admin.anggota.store') : route('admin.anggota.update',$anggota->id) }}"
      enctype="multipart/form-data">
@csrf
@if($mode==='edit') @method('PUT') @endif

<div class="card" style="margin-bottom:16px">
    <div class="card-header"><span class="card-title">Foto Profil</span></div>
    <div class="card-body" style="display:flex;align-items:center;gap:20px">
        <div style="width:80px;height:80px;border-radius:50%;background:var(--bg);display:flex;align-items:center;justify-content:center;overflow:hidden;border:1px solid var(--border)">
            @if($anggota->foto)
                <img src="{{ asset('storage/'.$anggota->foto) }}" style="width:100%;height:100%;object-fit:cover" id="previewFoto">
            @else
                <div id="placeholderFoto" style="font-size:2rem;font-weight:900;color:var(--muted)">?</div>
                <img src="" id="previewFoto" style="width:100%;height:100%;object-fit:cover;display:none">
            @endif
        </div>
        <div style="flex:1">
            <input type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(this)">
            <p style="font-size:.75rem;color:var(--muted);margin-top:4px">Rekomendasi: Persegi 1:1, Max 2MB</p>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewFoto');
            const placeholder = document.getElementById('placeholderFoto');
            preview.src = e.target.result;
            preview.style.display = 'block';
            if(placeholder) placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<div class="card">
    <div class="card-header"><span class="card-title">Data Anggota</span></div>
    <div class="card-body">
        @if($errors->any())
        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:14px;margin-bottom:20px;color:#DC2626;font-size:.875rem">
            <ul style="padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="form-group" style="margin-bottom:16px">
            <label>Nama Lengkap <span class="required">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name',$anggota->name) }}" required>
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label>Email <span class="required">*</span></label>
            <input type="email" name="email" class="form-control" value="{{ old('email',$anggota->email) }}" required>
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label>No. HP / WhatsApp</label>
            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp',$anggota->no_hp) }}">
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label>Alamat</label>
            <textarea name="alamat" class="form-control" rows="3">{{ old('alamat',$anggota->alamat) }}</textarea>
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="aktif"    {{ old('status',$anggota->status)==='aktif'    ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ old('status',$anggota->status)==='nonaktif' ? 'selected' : '' }}>Non-aktif</option>
            </select>
        </div>
        <div class="form-group" style="margin-bottom:16px">
            <label>Password {{ $mode==='edit' ? '(kosongkan jika tidak diubah)' : '' }} <span class="required">{{ $mode==='create' ? '*' : '' }}</span></label>
            <input type="password" name="password" class="form-control" {{ $mode==='create' ? 'required' : '' }} placeholder="Minimal 8 karakter">
        </div>
        @if($mode==='create')
        <div class="form-group">
            <label>Konfirmasi Password <span class="required">*</span></label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>
        @endif
    </div>
</div>
<div style="margin-top:16px;display:flex;gap:12px">
    <button type="submit" class="btn btn-primary">💾 {{ $mode==='create' ? 'Tambah Anggota' : 'Simpan Perubahan' }}</button>
    <a href="{{ route('admin.anggota.index') }}" class="btn btn-secondary">Batal</a>
</div>
</form>
</div>
@endsection