@extends('admin.layouts.app')
@section('title', $mode==='create' ? 'Tambah Event' : 'Edit Event')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>{{ $mode==='create' ? 'Tambah Event Baru' : 'Edit Event' }}</h1>
        <p>{{ $mode==='create' ? 'Isi form berikut untuk menambahkan event baru.' : 'Perbarui informasi event ini.' }}</p>
    </div>
    <a href="{{ route('admin.event.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<form method="POST"
      action="{{ $mode==='create' ? route('admin.event.store') : route('admin.event.update',$event->id) }}"
      enctype="multipart/form-data">
@csrf
@if($mode==='edit') @method('PUT') @endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;align-items:start">

    {{-- LEFT --}}
    <div style="display:flex;flex-direction:column;gap:20px">

        <div class="card">
            <div class="card-header"><span class="card-title">Informasi Event</span></div>
            <div class="card-body">
                @if($errors->any())
                <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:14px;margin-bottom:20px;color:#DC2626;font-size:.875rem">
                    <ul style="padding-left:16px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
                @endif

                <div class="form-group" style="margin-bottom:16px">
                    <label>Nama Event <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-control" value="{{ old('nama',$event->nama) }}" placeholder="ASEAN Cultural Festival" required>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tanggal <span class="required">*</span></label>
                        <input type="date" name="tanggal" class="form-control" value="{{ old('tanggal', $event->tanggal?->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Lokasi <span class="required">*</span></label>
                        <input type="text" name="lokasi" class="form-control" value="{{ old('lokasi',$event->lokasi) }}" placeholder="Kuala Lumpur, Malaysia" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori <span class="required">*</span></label>
                        @php $kategori_val = old('kategori', $mode === 'create' && request('kategori') ? request('kategori') : $event->kategori); @endphp
                        <select name="kategori" class="form-control" required>
                            <option value="midhang_sore" {{ $kategori_val === 'midhang_sore' ? 'selected' : '' }}>Midhang Sore</option>
                            <option value="studi_budaya" {{ $kategori_val === 'studi_budaya' ? 'selected' : '' }}>Studi Budaya</option>
                            <option value="pagelaran" {{ $kategori_val === 'pagelaran' ? 'selected' : '' }}>Pagelaran</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Level</label>
                        <select name="level" class="form-control">
                            @foreach(['Internasional','Nasional','Lokal'] as $l)
                            <option value="{{ $l }}" {{ old('level',$event->level)===$l?'selected':'' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hasil / Prestasi</label>
                        <input type="text" name="hasil" class="form-control" value="{{ old('hasil',$event->hasil) }}" placeholder="🥇 Best Performance">
                    </div>
                    <div class="form-group">
                        <label>Jumlah Penonton</label>
                        <input type="number" name="jumlah_penonton" class="form-control" value="{{ old('jumlah_penonton',$event->jumlah_penonton) }}" placeholder="2500">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="akan_datang" {{ old('status',$event->status)==='akan_datang'?'selected':'' }}>Akan Datang</option>
                            <option value="selesai"     {{ old('status',$event->status)==='selesai'?'selected':'' }}>Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" style="margin-top:16px">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Ceritakan detail event ini...">{{ old('deskripsi',$event->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Penghargaan yang Diraih</span></div>
            <div class="card-body">
                <div class="hint" style="margin-bottom:12px;font-size:.8rem;color:var(--muted)">Tambahkan penghargaan satu per satu (gunakan emoji untuk tampilan lebih menarik).</div>
                <div id="phWrap">
                    @foreach(old('penghargaan', $event->penghargaan ?? []) as $i => $ph)
                    <div class="ph-row" style="display:flex;gap:8px;margin-bottom:8px">
                        <input type="text" name="penghargaan[{{ $i }}]" class="form-control" value="{{ $ph }}" placeholder="🥇 Best Performance">
                        <button type="button" onclick="this.parentElement.remove()" class="btn btn-danger btn-sm btn-icon">✕</button>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="addPenghargaan" class="btn btn-secondary btn-sm">+ Tambah Penghargaan</button>
            </div>
        </div>

    </div>

    {{-- RIGHT --}}
    <div style="display:flex;flex-direction:column;gap:20px">
        <div class="card">
            <div class="card-header"><span class="card-title">Foto Event</span></div>
            <div class="card-body">
                <div class="file-upload-area">
                    <input type="file" name="foto" accept="image/*">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    <p><strong>Klik atau seret</strong> foto ke sini</p>
                    <p style="font-size:.75rem;color:#aaa">JPG, PNG, WebP — max 3MB</p>
                </div>
                <div class="file-preview" style="{{ $event->foto ? '' : 'display:none' }}">
                    <img src="{{ $event->foto ? asset('storage/'.$event->foto) : '' }}" alt="" style="width:100%;border-radius:8px;margin-top:12px">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><span class="card-title">Pengaturan Tiket & Peserta</span></div>
            <div class="card-body">
                <label class="form-check">
                    <input type="checkbox" name="unggulan" value="1" {{ old('unggulan',$event->unggulan) ? 'checked' : '' }}>
                    <span class="form-check-label">★ Tampilkan sebagai Event Unggulan</span>
                </label>
                <p style="font-size:.78rem;color:var(--muted);margin-top:6px;margin-bottom:15px;">Event unggulan ditampilkan lebih besar di halaman event.</p>

                <label class="form-check" style="margin-top: 10px;">
                    <input type="checkbox" id="isBerbayar" name="is_berbayar" value="1" {{ old('is_berbayar', $event->is_berbayar) ? 'checked' : '' }} onchange="document.getElementById('hargaTiketWrap').style.display = this.checked ? 'block' : 'none'">
                    <span class="form-check-label">💰 Event Berbayar (Tiket/HTM)</span>
                </label>
                <div id="hargaTiketWrap" style="margin-top: 10px; display: {{ old('is_berbayar', $event->is_berbayar) ? 'block' : 'none' }};">
                    <label style="font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; display: block;">Harga Tiket (Rp)</label>
                    <input type="number" name="harga_tiket" class="form-control" value="{{ old('harga_tiket', $event->harga_tiket) }}" placeholder="Contoh: 50000" min="0">
                </div>
            </div>
        </div>

        <div style="display:flex;flex-direction:column;gap:10px">
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
                💾 {{ $mode==='create' ? 'Simpan Event' : 'Perbarui Event' }}
            </button>
            <a href="{{ route('admin.event.index') }}" class="btn btn-secondary" style="width:100%;justify-content:center">Batal</a>
        </div>
    </div>
</div>
</form>
@endsection