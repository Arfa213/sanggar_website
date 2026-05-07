@extends('admin.layouts.app')
@section('title','Kelola Profil Sanggar')
@section('content')

@if(session('success'))
<div class="alert alert-success" style="margin-bottom:20px;padding:14px 20px;background:#F0FDF4;border:1px solid #86EFAC;border-radius:12px;color:#15803D;display:flex;gap:10px;align-items:center">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="page-header" style="margin-bottom:24px">
    <div class="page-header-text">
        <h1>Kelola Profil Sanggar</h1>
        <p>Atur informasi dasar, pelatih, pengelola, dan jadwal latihan.</p>
    </div>
</div>

{{-- TABS --}}
<div style="display:flex;gap:4px;border-bottom:2px solid #E8E0D8;margin-bottom:28px">
    @foreach([
        ['profil',    'Profil Sanggar'],
        ['pelatih',   'Pelatih'],
        ['pengelola', 'Pengelola'],
        ['jadwal',    'Jadwal Latihan'],
    ] as [$tab, $label])
    <button class="tab-btn {{ $loop->first ? 'active' : '' }}" data-tab="{{ $tab }}"
        style="padding:10px 20px;border:none;background:none;font-size:.875rem;font-weight:700;cursor:pointer;color:var(--muted);border-bottom:2px solid transparent;margin-bottom:-2px;transition:all .2s"
        onclick="switchTab('{{ $tab }}')">
        {{ $label }}
    </button>
    @endforeach
</div>

{{-- ══════════ TAB: PROFIL ══════════ --}}
<div id="tab-profil" class="tab-content">
<div class="card">
    <div class="card-header"><span class="card-title">Informasi Dasar Sanggar</span></div>
    <div class="card-body">
    <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data">
        @csrf
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
            <div class="form-group">
                <label>Nama Sanggar <span class="required">*</span></label>
                <input type="text" name="nama_sanggar" class="form-control" required value="{{ old('nama_sanggar',$profil->nama_sanggar) }}">
            </div>
            <div class="form-group">
                <label>Tagline</label>
                <input type="text" name="tagline" class="form-control" value="{{ old('tagline',$profil->tagline) }}">
            </div>
            <div class="form-group">
                <label>Tahun Berdiri</label>
                <input type="text" name="tahun_berdiri" class="form-control" value="{{ old('tahun_berdiri',$profil->tahun_berdiri) }}">
            </div>
            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp',$profil->no_hp) }}">
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email',$profil->email) }}">
            </div>
            <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="instagram" class="form-control" value="{{ old('instagram',$profil->instagram) }}">
            </div>
            <div class="form-group" style="grid-column:span 2">
                <label>Alamat</label>
                <input type="text" name="alamat" class="form-control" value="{{ old('alamat',$profil->alamat) }}">
            </div>
            <div class="form-group" style="grid-column:span 2">
                <label>Sejarah <span class="required">*</span></label>
                <textarea name="sejarah" class="form-control" rows="5" required>{{ old('sejarah',$profil->sejarah) }}</textarea>
            </div>
            <div class="form-group" style="grid-column:span 2">
                <label>Visi <span class="required">*</span></label>
                <textarea name="visi" class="form-control" rows="3" required>{{ old('visi',$profil->visi) }}</textarea>
            </div>
            <div class="form-group" style="grid-column:span 2">
                <label>Misi <span class="required">*</span></label>
                <div id="misi-list">
                    @foreach(old('misi',$profil->misi ?? []) as $i => $m)
                    <div style="display:flex;gap:8px;margin-bottom:8px">
                        <input type="text" name="misi[]" class="form-control" value="{{ $m }}" placeholder="Misi {{ $i+1 }}">
                        <button type="button" onclick="this.parentElement.remove()" style="background:#FEF2F2;border:1px solid #FECACA;color:#DC2626;padding:0 12px;border-radius:8px;cursor:pointer">✕</button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addMisi()" style="background:var(--primary-pale);border:1px solid rgba(198,93,46,.3);color:var(--primary);font-size:.8rem;font-weight:700;padding:8px 16px;border-radius:8px;cursor:pointer;margin-top:6px">+ Tambah Misi</button>
            </div>
            <div class="form-group">
                <label>Jumlah Anggota</label>
                <input type="number" name="jumlah_anggota" class="form-control" value="{{ old('jumlah_anggota',$profil->jumlah_anggota) }}">
            </div>
            <div class="form-group">
                <label>Jumlah Penghargaan</label>
                <input type="number" name="jumlah_penghargaan" class="form-control" value="{{ old('jumlah_penghargaan',$profil->jumlah_penghargaan) }}">
            </div>
            <div class="form-group">
                <label>Foto Profil (Logo/Avatar)</label>
                @if($profil->foto_profil)
                    <img src="{{ asset('storage/'.$profil->foto_profil) }}" style="width:80px;height:80px;border-radius:50%;object-fit:cover;display:block;margin-bottom:8px">
                @endif
                <input type="file" name="foto_profil" class="form-control" accept="image/*">
                <span class="hint" style="font-size:.75rem;margin-top:4px;display:block;color:var(--primary)">Akan muncul sebagai logo sanggar di sidebar dan navigasi.</span>
            </div>
            <div class="form-group">
                <label>Foto Sejarah (Background/Hero Profil)</label>
                @if($profil->foto_sejarah)
                    <img src="{{ asset('storage/'.$profil->foto_sejarah) }}" style="width:120px;height:80px;object-fit:cover;display:block;margin-bottom:8px;border-radius:8px">
                @endif
                <input type="file" name="foto_sejarah" class="form-control" accept="image/*">
                <span class="hint" style="font-size:.75rem;margin-top:4px;display:block;color:var(--primary)">Akan muncul di halaman profil sanggar pada bagian sejarah.</span>
            </div>
        </div>
        <div style="margin-top:20px">
            <button type="submit" class="btn btn-primary">💾 Simpan Profil</button>
        </div>
    </form>
    </div>
</div>
</div>

{{-- ══════════ TAB: PELATIH ══════════ --}}
<div id="tab-pelatih" class="tab-content" style="display:none">

    {{-- Form Tambah Pelatih --}}
    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Tambah Pelatih Baru</span></div>
        <div class="card-body">
        <form method="POST" action="{{ route('admin.pelatih.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group">
                    <label>Nama Pelatih <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-control" required placeholder="Nama lengkap">
                </div>
                <div class="form-group">
                    <label>Jabatan <span class="required">*</span></label>
                    <input type="text" name="jabatan" class="form-control" required placeholder="Pelatih Utama / Asisten...">
                </div>
                <div class="form-group">
                    <label>Spesialisasi</label>
                    <input type="text" name="spesialisasi" class="form-control" placeholder="Tari Topeng, Sintren...">
                </div>
                <div class="form-group">
                    <label>Pengalaman</label>
                    <input type="text" name="pengalaman" class="form-control" placeholder="15 tahun...">
                </div>
                <div class="form-group" style="grid-column:span 2">
                    <label>Bio</label>
                    <textarea name="bio" class="form-control" rows="3" placeholder="Deskripsi singkat pelatih..."></textarea>
                </div>
                <div class="form-group">
                    <label>No. HP</label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xx...">
                </div>
                <div class="form-group">
                    <label>Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:8px">+ Tambah Pelatih</button>
        </form>
        </div>
    </div>

    {{-- Daftar Pelatih --}}
    <div class="card">
        <div class="card-header"><span class="card-title">Daftar Pelatih ({{ $pelatih->count() }})</span></div>
        @if($pelatih->isEmpty())
        <div class="card-body" style="text-align:center;color:var(--muted);padding:40px">Belum ada pelatih.</div>
        @else
        <div class="table-wrap">
        <table>
            <thead><tr><th>Foto</th><th>Nama & Jabatan</th><th>Spesialisasi</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($pelatih as $p)
            <tr>
                <td>
                    @if($p->foto)
                        <img src="{{ asset('storage/'.$p->foto) }}" style="width:44px;height:44px;border-radius:50%;object-fit:cover">
                    @else
                        <div style="width:44px;height:44px;border-radius:50%;background:var(--primary-pale);display:flex;align-items:center;justify-content:center;font-weight:900;color:var(--primary)">{{ strtoupper(substr($p->nama,0,1)) }}</div>
                    @endif
                </td>
                <td>
                    <div style="font-weight:700">{{ $p->nama }}</div>
                    <div style="font-size:.8rem;color:var(--muted)">{{ $p->jabatan }}</div>
                </td>
                <td style="font-size:.85rem">{{ $p->spesialisasi ?? '-' }}</td>
                <td><span class="chip {{ $p->aktif ? 'chip--green' : 'chip--gray' }}">{{ $p->aktif ? 'Aktif' : 'Non-aktif' }}</span></td>
                <td>
                    <button onclick="toggleEditPelatih({{ $p->id }})"
                        style="background:var(--primary-pale);border:1px solid rgba(198,93,46,.2);color:var(--primary);font-size:.75rem;font-weight:700;padding:5px 12px;border-radius:8px;cursor:pointer">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.pelatih.destroy', $p->id) }}" style="display:inline" onsubmit="return confirm('Hapus pelatih {{ $p->nama }}?')">
                        @csrf
                        <button type="submit" style="background:#FEF2F2;border:1px solid #FECACA;color:#DC2626;font-size:.75rem;font-weight:700;padding:5px 12px;border-radius:8px;cursor:pointer">Hapus</button>
                    </form>
                </td>
            </tr>

            {{-- INLINE EDIT FORM --}}
            <tr id="edit-pelatih-{{ $p->id }}" style="display:none;background:#FFF8F6">
                <td colspan="5" style="padding:20px">
                <form method="POST" action="{{ route('admin.pelatih.update', $p->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px">
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Nama <span class="required">*</span></label>
                            <input type="text" name="nama" class="form-control" required value="{{ $p->nama }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Jabatan <span class="required">*</span></label>
                            <input type="text" name="jabatan" class="form-control" required value="{{ $p->jabatan }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Spesialisasi</label>
                            <input type="text" name="spesialisasi" class="form-control" value="{{ $p->spesialisasi }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Pengalaman</label>
                            <input type="text" name="pengalaman" class="form-control" value="{{ $p->pengalaman }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ $p->no_hp }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Foto Baru</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                        <div class="form-group" style="margin:0;grid-column:span 3">
                            <label style="font-size:.8rem">Bio</label>
                            <textarea name="bio" class="form-control" rows="2">{{ $p->bio }}</textarea>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px">
                        <label style="display:flex;align-items:center;gap:6px;font-size:.85rem;cursor:pointer">
                            <input type="checkbox" name="aktif" {{ $p->aktif ? 'checked' : '' }}> Aktif
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm">💾 Simpan Perubahan</button>
                        <button type="button" onclick="toggleEditPelatih({{ $p->id }})"
                            style="background:#F3F4F6;border:1px solid #D1D5DB;color:#6B7280;font-size:.8rem;padding:6px 14px;border-radius:8px;cursor:pointer">
                            Batal
                        </button>
                    </div>
                </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>

{{-- ══════════ TAB: PENGELOLA ══════════ --}}
<div id="tab-pengelola" class="tab-content" style="display:none">

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Tambah Pengelola Baru</span></div>
        <div class="card-body">
        <form method="POST" action="{{ route('admin.pengelola.store') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                <div class="form-group">
                    <label>Nama <span class="required">*</span></label>
                    <input type="text" name="nama" class="form-control" required placeholder="Nama pengelola">
                </div>
                <div class="form-group">
                    <label>Jabatan <span class="required">*</span></label>
                    <input type="text" name="jabatan" class="form-control" required placeholder="Ketua / Sekretaris...">
                </div>
                <div class="form-group">
                    <label>Ikon (emoji) <span class="required">*</span></label>
                    <input type="text" name="ikon" class="form-control" required placeholder="👑 / 📋 / 💰">
                </div>
                <div class="form-group">
                    <label>Foto</label>
                    <input type="file" name="foto" class="form-control" accept="image/*">
                </div>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:8px">+ Tambah Pengelola</button>
        </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Daftar Pengelola ({{ $pengelola->count() }})</span></div>
        @if($pengelola->isEmpty())
        <div class="card-body" style="text-align:center;color:var(--muted);padding:40px">Belum ada pengelola.</div>
        @else
        <div class="table-wrap">
        <table>
            <thead><tr><th>Ikon</th><th>Nama & Jabatan</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($pengelola as $pg)
            <tr>
                <td style="font-size:1.5rem;text-align:center">{{ $pg->ikon }}</td>
                <td>
                    <div style="font-weight:700">{{ $pg->nama }}</div>
                    <div style="font-size:.8rem;color:var(--muted)">{{ $pg->jabatan }}</div>
                </td>
                <td><span class="chip {{ $pg->aktif ? 'chip--green' : 'chip--gray' }}">{{ $pg->aktif ? 'Aktif' : 'Non-aktif' }}</span></td>
                <td>
                    <button onclick="toggleEditPengelola({{ $pg->id }})"
                        style="background:var(--primary-pale);border:1px solid rgba(198,93,46,.2);color:var(--primary);font-size:.75rem;font-weight:700;padding:5px 12px;border-radius:8px;cursor:pointer">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.pengelola.destroy', $pg->id) }}" style="display:inline" onsubmit="return confirm('Hapus pengelola {{ $pg->nama }}?')">
                        @csrf
                        <button type="submit" style="background:#FEF2F2;border:1px solid #FECACA;color:#DC2626;font-size:.75rem;font-weight:700;padding:5px 12px;border-radius:8px;cursor:pointer">Hapus</button>
                    </form>
                </td>
            </tr>
            <tr id="edit-pengelola-{{ $pg->id }}" style="display:none;background:#FFF8F6">
                <td colspan="4" style="padding:20px">
                <form method="POST" action="{{ route('admin.pengelola.update', $pg->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:14px">
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Nama <span class="required">*</span></label>
                            <input type="text" name="nama" class="form-control" required value="{{ $pg->nama }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Jabatan <span class="required">*</span></label>
                            <input type="text" name="jabatan" class="form-control" required value="{{ $pg->jabatan }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Ikon <span class="required">*</span></label>
                            <input type="text" name="ikon" class="form-control" required value="{{ $pg->ikon }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Foto Baru</label>
                            <input type="file" name="foto" class="form-control" accept="image/*">
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:12px">
                        <label style="display:flex;align-items:center;gap:6px;font-size:.85rem;cursor:pointer">
                            <input type="checkbox" name="aktif" {{ $pg->aktif ? 'checked' : '' }}> Aktif
                        </label>
                        <button type="submit" class="btn btn-primary btn-sm">💾 Simpan Perubahan</button>
                        <button type="button" onclick="toggleEditPengelola({{ $pg->id }})"
                            style="background:#F3F4F6;border:1px solid #D1D5DB;color:#6B7280;font-size:.8rem;padding:6px 14px;border-radius:8px;cursor:pointer">
                            Batal
                        </button>
                    </div>
                </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>

{{-- ══════════ TAB: JADWAL ══════════ --}}
<div id="tab-jadwal" class="tab-content" style="display:none">

    <div class="card" style="margin-bottom:24px">
        <div class="card-header"><span class="card-title">Tambah Jadwal Latihan</span></div>
        <div class="card-body">
        <form method="POST" action="{{ route('admin.jadwal.store') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr auto;gap:14px;align-items:end">
                <div class="form-group" style="margin:0">
                    <label>Hari <span class="required">*</span></label>
                    <select name="hari" class="form-control" required>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                        <option value="{{ $h }}">{{ $h }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin:0">
                    <label>Jam Mulai <span class="required">*</span></label>
                    <input type="time" name="jam_mulai" class="form-control" required>
                </div>
                <div class="form-group" style="margin:0">
                    <label>Jam Selesai <span class="required">*</span></label>
                    <input type="time" name="jam_selesai" class="form-control" required>
                </div>
                <div class="form-group" style="margin:0">
                    <label>Nama Kelas <span class="required">*</span></label>
                    <input type="text" name="kelas" class="form-control" required placeholder="Kelas Dasar...">
                </div>
                <div class="form-group" style="margin:0">
                    <label>Tempat <span class="required">*</span></label>
                    <input type="text" name="tempat" class="form-control" required placeholder="Aula utama...">
                </div>
                <button type="submit" class="btn btn-primary">+ Tambah</button>
            </div>
        </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><span class="card-title">Jadwal Latihan ({{ $jadwal->count() }})</span></div>
        @if($jadwal->isEmpty())
        <div class="card-body" style="text-align:center;color:var(--muted);padding:40px">Belum ada jadwal.</div>
        @else
        <div class="table-wrap">
        <table>
            <thead><tr><th>Hari</th><th>Jam</th><th>Kelas</th><th>Tempat</th><th>Status</th><th>Aksi</th></tr></thead>
            <tbody>
            @foreach($jadwal as $j)
            <tr>
                <td style="font-weight:700">{{ $j->hari }}</td>
                <td>{{ $j->jam_mulai }} – {{ $j->jam_selesai }}</td>
                <td>{{ $j->kelas }}</td>
                <td style="font-size:.85rem;color:var(--muted)">{{ $j->tempat }}</td>
                <td><span class="chip {{ $j->aktif ? 'chip--green' : 'chip--gray' }}">{{ $j->aktif ? 'Aktif' : 'Non-aktif' }}</span></td>
                <td>
                    <button onclick="toggleEditJadwal({{ $j->id }})"
                        style="background:var(--primary-pale);border:1px solid rgba(198,93,46,.2);color:var(--primary);font-size:.75rem;font-weight:700;padding:5px 12px;border-radius:8px;cursor:pointer">
                        Edit
                    </button>
                    <form method="POST" action="{{ route('admin.jadwal.destroy', $j->id) }}" style="display:inline" onsubmit="return confirm('Hapus jadwal ini?')">
                        @csrf
                        <button type="submit" style="background:#FEF2F2;border:1px solid #FECACA;color:#DC2626;font-size:.75rem;font-weight:700;padding:5px 12px;border-radius:8px;cursor:pointer">Hapus</button>
                    </form>
                </td>
            </tr>
            <tr id="edit-jadwal-{{ $j->id }}" style="display:none;background:#FFF8F6">
                <td colspan="6" style="padding:16px">
                <form method="POST" action="{{ route('admin.jadwal.update', $j->id) }}">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr auto;gap:12px;align-items:end">
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Hari</label>
                            <select name="hari" class="form-control" required>
                                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                                <option value="{{ $h }}" {{ $j->hari===$h?'selected':'' }}>{{ $h }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required value="{{ $j->jam_mulai }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required value="{{ $j->jam_selesai }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Nama Kelas</label>
                            <input type="text" name="kelas" class="form-control" required value="{{ $j->kelas }}">
                        </div>
                        <div class="form-group" style="margin:0">
                            <label style="font-size:.8rem">Tempat</label>
                            <input type="text" name="tempat" class="form-control" required value="{{ $j->tempat }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">💾</button>
                    </div>
                    <div style="margin-top:10px;display:flex;align-items:center;gap:12px">
                        <label style="display:flex;align-items:center;gap:6px;font-size:.85rem;cursor:pointer">
                            <input type="checkbox" name="aktif" {{ $j->aktif ? 'checked' : '' }}> Aktif
                        </label>
                        <button type="button" onclick="toggleEditJadwal({{ $j->id }})"
                            style="background:#F3F4F6;border:1px solid #D1D5DB;color:#6B7280;font-size:.8rem;padding:5px 12px;border-radius:8px;cursor:pointer">
                            Batal
                        </button>
                    </div>
                </form>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        @endif
    </div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.style.color = 'var(--muted)';
        btn.style.borderBottomColor = 'transparent';
    });
    document.getElementById('tab-' + tab).style.display = 'block';
    const btn = document.querySelector('[data-tab="' + tab + '"]');
    btn.style.color = 'var(--primary)';
    btn.style.borderBottomColor = 'var(--primary)';
}

function toggleEditPelatih(id) {
    const row = document.getElementById('edit-pelatih-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
function toggleEditPengelola(id) {
    const row = document.getElementById('edit-pengelola-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
function toggleEditJadwal(id) {
    const row = document.getElementById('edit-jadwal-' + id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

function addMisi() {
    const list = document.getElementById('misi-list');
    const idx  = list.children.length + 1;
    const div  = document.createElement('div');
    div.style = 'display:flex;gap:8px;margin-bottom:8px';
    div.innerHTML = `<input type="text" name="misi[]" class="form-control" placeholder="Misi ${idx}">
        <button type="button" onclick="this.parentElement.remove()" style="background:#FEF2F2;border:1px solid #FECACA;color:#DC2626;padding:0 12px;border-radius:8px;cursor:pointer">✕</button>`;
    list.appendChild(div);
}

// Auto-switch ke tab aktif dari URL hash
const hash = window.location.hash.replace('#','');
if (['pelatih','pengelola','jadwal','profil'].includes(hash)) switchTab(hash);
</script>
@endsection