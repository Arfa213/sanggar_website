@extends('admin.layouts.app')
@section('title', 'Broadcast Pengumuman')
@section('content')

<div class="page-header">
    <div class="page-header-text">
        <h1>Broadcast Pengumuman</h1>
        <p>Kirim pesan, pengumuman, atau info terbaru secara real-time ke seluruh aplikasi mobile anggota sanggar.</p>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 24px; align-items: start;">
    
    {{-- FORM KIRIM BROADCAST --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📣 Tulis Pengumuman Baru</span>
        </div>
        <div class="card-body">
            @if($errors->any())
            <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; padding:14px; margin-bottom:20px; color:#DC2626; font-size:.875rem">
                <ul style="padding-left:16px; margin: 0;">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('admin.pengumuman.store') }}">
                @csrf
                <div class="form-group" style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; font-size:.9rem; color:var(--dark);">Judul Pengumuman <span style="color:#EF4444">*</span></label>
                    <input type="text" name="judul" class="form-control" placeholder="Misal: Latihan diliburkan sementara" value="{{ old('judul') }}" required style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:inherit;">
                </div>

                <div class="form-group" style="margin-bottom:16px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; font-size:.9rem; color:var(--dark);">Kategori <span style="color:#EF4444">*</span></label>
                    <select name="tipe" class="form-control" required style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:inherit; background:#fff;">
                        <option value="announcement">📢 Pengumuman Umum</option>
                        <option value="info">💡 Informasi Penting</option>
                        <option value="event">🎭 Kegiatan / Pentas</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:20px;">
                    <label style="display:block; margin-bottom:6px; font-weight:600; font-size:.9rem; color:var(--dark);">Pesan / Konten Pengumuman <span style="color:#EF4444">*</span></label>
                    <textarea name="konten" class="form-control" rows="5" placeholder="Tulis isi pengumuman secara detail di sini..." required style="width:100%; padding:10px 14px; border:1px solid var(--border); border-radius:8px; font-family:inherit; resize:vertical;">{{ old('konten') }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-weight:800; font-size:.9rem; display:flex; align-items:center; justify-content:center; gap:8px;">
                    <span>🚀 Sebarkan Sekarang</span>
                </button>
            </form>
        </div>
    </div>

    {{-- DAFTAR RIWAYAT BROADCAST --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">🕰️ Riwayat Pengumuman</span>
        </div>
        <div class="table-wrap">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align:left; padding:12px 16px;">Pengumuman</th>
                        <th style="text-align:left; padding:12px 16px; width:120px;">Kategori</th>
                        <th style="text-align:left; padding:12px 16px; width:130px;">Tanggal Kirim</th>
                        <th style="text-align:center; padding:12px 16px; width:100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($broadcasts as $b)
                    <tr style="border-bottom: 1px solid var(--border);">
                        <td style="padding:16px;">
                            <div style="font-weight:700; color:var(--dark); font-size:.95rem; margin-bottom:4px;">{{ $b->judul }}</div>
                            <div style="font-size:.85rem; color:var(--muted); line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
                                {{ $b->konten }}
                            </div>
                        </td>
                        <td style="padding:16px; vertical-align:middle;">
                            @if($b->tipe === 'announcement')
                                <span class="chip chip--blue">📢 Pengumuman</span>
                            @elseif($b->tipe === 'info')
                                <span class="chip chip--green">💡 Info</span>
                            @else
                                <span class="chip chip--purple" style="background:#F3E8FF; color:#6B21A8;">🎭 Pentas</span>
                            @endif
                        </td>
                        <td style="padding:16px; font-size:.8rem; color:var(--muted); vertical-align:middle;">
                            {{ $b->created_at->format('d M Y, H:i') }} WIB
                        </td>
                        <td style="padding:16px; text-align:center; vertical-align:middle;">
                            <form method="POST" action="{{ route('admin.pengumuman.destroy', $b->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengumuman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding:4px 8px; font-size:.75rem;">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:40px;">
                            <div style="font-size:2.5rem; margin-bottom:12px;">📭</div>
                            <h3 style="margin-bottom:6px; color:var(--dark);">Belum ada pengumuman</h3>
                            <p style="color:var(--muted); font-size:.85rem; margin:0;">Tulis pengumuman pertama Anda di panel sebelah kiri.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
