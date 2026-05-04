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

            <div style="display:flex; gap:24px; align-items:flex-start; margin-bottom:30px; flex-wrap:wrap;">
                {{-- Foto Profil --}}
                <div style="flex-shrink:0;">
                    <div style="width:120px; height:120px; background:var(--primary-pale); border-radius:50%; overflow:hidden; position:relative; margin-bottom:12px; border:4px solid #fff; box-shadow:0 4px 12px rgba(0,0,0,.08);">
                        @if($user->foto)
                            <img src="{{ asset('storage/'.$user->foto) }}" alt="Foto" style="width:100%; height:100%; object-fit:cover;" id="fotoPreview">
                        @else
                            <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:var(--primary); color:#fff; font-size:2.5rem; font-weight:800; font-family:var(--font-display);">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <img src="" alt="Preview" style="width:100%; height:100%; object-fit:cover; display:none; position:absolute; top:0; left:0;" id="fotoPreview">
                        @endif
                    </div>
                    <div>
                        <label for="fotoInput" style="display:block; text-align:center; background:var(--bg-soft); color:var(--dark); font-size:.8rem; font-weight:700; padding:8px 12px; border-radius:50px; cursor:pointer; border:1px solid var(--border); transition:all .2s;">
                            Ubah Foto
                        </label>
                        <input type="file" name="foto" id="fotoInput" accept="image/png, image/jpeg, image/jpg" style="display:none;" onchange="previewImage(this)">
                    </div>
                    @error('foto') <span style="color:#DC2626; font-size:.75rem; display:block; text-align:center; margin-top:4px;">{{ $message }}</span> @enderror
                </div>

                {{-- Basic Info --}}
                <div style="flex:1; min-width:280px;">
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
</section>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('fotoPreview');
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
