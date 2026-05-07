@extends('layouts.member')
@section('title', 'Profil Saya')
@section('content')

<section style="padding-top:20px; padding-bottom:60px;">
    <div class="m-page-header">
        <span class="m-badge">Pengaturan</span>
        <h1>Profil Saya</h1>
        <p>Kelola data diri, informasi kontak, dan kata sandi Anda di sini.</p>
    </div>

    <div style="background:#fff; border-radius:20px; border:1px solid var(--border); overflow:hidden; width:100%;">
        <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" style="padding:30px;">
            @csrf

            {{-- Bagian Atas: Foto Profil Centered --}}
            <div style="display:flex; flex-direction:column; align-items:center; margin-bottom:40px; border-bottom:1px solid var(--border); padding-bottom:30px;">
                <div style="position:relative;">
                    <div style="width:140px; height:140px; background:var(--primary-pale); border-radius:50%; overflow:hidden; border:4px solid #fff; box-shadow:0 8px 20px rgba(0,0,0,.1); position:relative;">
                        @if($user->foto)
                            <img src="{{ asset('storage/'.$user->foto) }}" alt="Foto" style="width:100%; height:100%; object-fit:cover;" id="fotoPreview">
                        @else
                            <div id="fotoPlaceholder" style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:var(--primary); color:#fff; font-size:3rem; font-weight:800; font-family:var(--font-display);">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <img src="" alt="Preview" style="width:100%; height:100%; object-fit:cover; display:none; position:absolute; top:0; left:0;" id="fotoPreviewNew">
                        @endif
                    </div>
                    
                    {{-- Tombol Edit Melayang --}}
                    <label for="fotoInput" style="position:absolute; bottom:5px; right:5px; width:36px; height:36px; background:var(--primary); color:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; border:3px solid #fff; box-shadow:0 4px 10px rgba(0,0,0,0.15); transition:all .2s;"
                        onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                    </label>
                    <input type="file" name="foto" id="fotoInput" accept="image/png, image/jpeg, image/jpg, image/webp" style="display:none;" onchange="previewImage(this)">
                </div>
                
                <div style="text-align:center; margin-top:15px;">
                    <h4 style="margin:0; font-size:1.1rem; color:var(--dark);">Foto Profil</h4>
                    <p style="font-size:.8rem; color:var(--muted); margin-top:4px;">Klik ikon pensil untuk mengubah foto</p>
                    @error('foto') <span style="color:#DC2626; font-size:.75rem; display:block; margin-top:8px;">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Bagian Bawah: Form Inputs --}}
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:24px; margin-bottom:30px;">
                <div style="margin-bottom:16px;">
                    <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">Nama Lengkap <span style="color:#C65D2E">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6;">
                    @error('name') <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">Alamat Email <span style="color:#C65D2E">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6;">
                    @error('email') <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom:30px;">
                <div>
                    <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">Nomor HP / WhatsApp</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="Contoh: 081234567890"
                        style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6;">
                    @error('no_hp') <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">Alamat Lengkap</label>
                    <textarea name="alamat" rows="2" placeholder="Masukkan alamat lengkap..."
                        style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6; resize:vertical;">{{ old('alamat', $user->alamat) }}</textarea>
                    @error('alamat') <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                </div>
            </div>

            <div style="border-top:1px solid var(--border); padding-top:24px; margin-bottom:30px;">
                <h4 style="font-family:var(--font-display); font-size:1.1rem; font-weight:700; color:var(--dark); margin-bottom:16px;">Ubah Kata Sandi</h4>
                <p style="font-size:.85rem; color:var(--muted); margin-bottom:20px;">Biarkan kosong jika Anda tidak ingin mengubah kata sandi.</p>
                
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:20px;">
                    <div>
                        <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">Kata Sandi Baru</label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter"
                            style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6;">
                        @error('password') <span style="color:#DC2626; font-size:.75rem; margin-top:4px; display:block;">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label style="font-size:.85rem; font-weight:700; color:var(--dark); display:block; margin-bottom:8px;">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ketik ulang kata sandi baru"
                            style="width:100%; padding:12px 16px; border:1.5px solid var(--border); border-radius:12px; font-size:.9rem; outline:none; background:#FAF8F6;">
                    </div>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end;">
                <button type="submit" style="background:var(--primary); color:#fff; font-family:var(--font-body); font-size:.95rem; font-weight:700; padding:14px 32px; border-radius:50px; border:none; cursor:pointer; box-shadow:0 4px 12px rgba(198,93,46,.3); transition:all .2s;"
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(198,93,46,.4)';" 
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(198,93,46,.3)';">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    {{-- Bagian Pengaturan Tarian / Riwayat Kelas --}}
    <div style="margin-top:40px; background:#fff; border-radius:20px; border:1px solid var(--border); overflow:hidden; width:100%;">
        <div style="padding:24px 30px; border-bottom:1px solid var(--border); background:#FAF8F6;">
            <h3 style="font-family:var(--font-display); font-size:1.4rem; font-weight:700; color:var(--dark); margin:0;">Pengaturan Tarian</h3>
            <p style="font-size:.85rem; color:var(--muted); margin-top:4px;">Riwayat kelas dan tarian yang pernah Anda ikuti.</p>
        </div>
        
        <div style="padding:30px;">
            @if($riwayatTarian->isEmpty())
                <div style="text-align:center; padding:40px; background:var(--bg-soft); border-radius:16px;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="1.5" style="margin-bottom:12px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <p style="font-weight:600; color:var(--dark);">Belum ada riwayat tarian</p>
                    <p style="font-size:.825rem; color:var(--muted); margin-top:4px;">Daftarkan diri Anda pada kelas tari untuk mulai belajar.</p>
                    <a href="{{ route('penjadwalan') }}" style="display:inline-block; margin-top:16px; color:var(--primary); font-weight:700; text-decoration:none;">Pilih Kelas Tari →</a>
                </div>
            @else
                <div style="overflow-x:auto;">
                    <table style="width:100%; border-collapse:collapse; min-width:600px;">
                        <thead>
                            <tr style="text-align:left; border-bottom:2px solid var(--bg-soft);">
                                <th style="padding:12px 15px; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px;">Nama Tarian</th>
                                <th style="padding:12px 15px; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px;">Jadwal</th>
                                <th style="padding:12px 15px; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px;">Tanggal Daftar</th>
                                <th style="padding:12px 15px; font-size:.8rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:1px; text-align:center;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($riwayatTarian as $rt)
                            <tr style="border-bottom:1px solid var(--bg-soft); transition:background .2s;" onmouseover="this.style.background='#FAF8F6'" onmouseout="this.style.background='transparent'">
                                <td style="padding:16px 15px;">
                                    <div style="display:flex; align-items:center; gap:12px;">
                                        <div style="width:40px; height:40px; background:var(--primary-pale); border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                        </div>
                                        <div>
                                            <div style="font-weight:700; color:var(--dark); font-size:.9rem;">{{ $rt->tarian->nama }}</div>
                                            <div style="font-size:.75rem; color:var(--muted);">{{ ucfirst($rt->tarian->kategori) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td style="padding:16px 15px;">
                                    <div style="font-size:.85rem; color:var(--text);">{{ $rt->jadwal->hari }}</div>
                                    <div style="font-size:.75rem; color:var(--muted);">{{ $rt->jadwal->jam_mulai }} – {{ $rt->jadwal->jam_selesai }}</div>
                                </td>
                                <td style="padding:16px 15px;">
                                    <div style="font-size:.85rem; color:var(--text);">{{ $rt->tanggal_daftar->isoFormat('D MMMM YYYY') }}</div>
                                    <div style="font-size:.75rem; color:var(--muted);">Pukul {{ $rt->tanggal_daftar->format('H:i') }}</div>
                                </td>
                                <td style="padding:16px 15px; text-align:center;">
                                    @if($rt->status === 'aktif')
                                        <span style="background:#E8F5E9; color:#2E7D32; font-size:.7rem; font-weight:800; padding:4px 12px; border-radius:20px; text-transform:uppercase;">Aktif</span>
                                    @else
                                        <span style="background:#F3F4F6; color:#6B7280; font-size:.7rem; font-weight:800; padding:4px 12px; border-radius:20px; text-transform:uppercase;">{{ $rt->status }}</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</section>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const previewExisting = document.getElementById('fotoPreview');
            const previewNew = document.getElementById('fotoPreviewNew');
            const placeholder = document.getElementById('fotoPlaceholder');
            
            if (previewExisting) {
                previewExisting.src = e.target.result;
            }
            if (previewNew) {
                previewNew.src = e.target.result;
                previewNew.style.display = 'block';
            }
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
