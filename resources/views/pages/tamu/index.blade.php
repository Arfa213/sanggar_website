@extends('layouts.app')

@section('title', 'Buku Tamu Digital')

@section('content')
<div style="background: var(--bg-soft); min-height: 80vh; padding: 100px 0 80px;">
    <div class="container">
        <div style="max-width: 600px; margin: 0 auto;">
            
            {{-- Header --}}
            <div style="text-align: center; margin-bottom: 40px;">
                <span class="badge">Check-in Pengunjung</span>
                <h1 style="font-family: var(--font-display); font-size: 2.5rem; font-weight: 900; color: var(--dark); margin-top: 10px;">Buku Tamu Digital</h1>
                <p style="color: var(--muted); margin-top: 12px;">Silakan isi data kunjungan Anda untuk keperluan pendataan sanggar.</p>
            </div>

            {{-- Form Card --}}
            <div style="background: #fff; border-radius: 24px; padding: 40px; border: 1px solid var(--border); box-shadow: var(--shadow-md);">
                
                @if(session('success'))
                    <div style="background: #F0FDF4; border: 1px solid #86EFAC; color: #15803D; padding: 20px; border-radius: 16px; text-align: center; margin-bottom: 30px;">
                        <div style="font-size: 2rem; margin-bottom: 10px;">✅</div>
                        <h3 style="font-weight: 800; margin-bottom: 5px;">Terima Kasih!</h3>
                        <p style="font-size: .9rem;">{{ session('success') }}</p>
                    </div>
                @endif

                <form action="{{ route('tamu.store') }}" method="POST">
                    @csrf
                    
                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: .85rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Nama Lengkap <span style="color: var(--primary);">*</span></label>
                        <input type="text" name="nama" required class="form-input" placeholder="Masukkan nama lengkap Anda" style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid var(--border); font-family: inherit;" value="{{ old('nama') }}">
                        @error('nama') <p style="color: #DC2626; font-size: .75rem; margin-top: 5px; font-weight: 600;">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: .85rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Nomor WhatsApp <span style="color: var(--primary);">*</span></label>
                        <input type="text" name="no_hp" required class="form-input" placeholder="Contoh: 0812xxxx" style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid var(--border); font-family: inherit;" value="{{ old('no_hp') }}">
                        @error('no_hp') <p style="color: #DC2626; font-size: .75rem; margin-top: 5px; font-weight: 600;">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom: 24px;">
                        <label style="display: block; font-size: .85rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Instansi / Lembaga (Opsional)</label>
                        <input type="text" name="instansi" class="form-input" placeholder="Nama perusahaan atau organisasi" style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid var(--border); font-family: inherit;" value="{{ old('instansi') }}">
                        @error('instansi') <p style="color: #DC2626; font-size: .75rem; margin-top: 5px; font-weight: 600;">{{ $message }}</p> @enderror
                    </div>

                    <div style="margin-bottom: 32px;">
                        <label style="display: block; font-size: .85rem; font-weight: 700; color: var(--dark); margin-bottom: 8px;">Tujuan Kunjungan <span style="color: var(--primary);">*</span></label>
                        <textarea name="tujuan" required class="form-input" placeholder="Misal: Bertemu pelatih, melihat latihan, atau informasi pendaftaran" style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid var(--border); font-family: inherit; min-height: 100px; resize: none;">{{ old('tujuan') }}</textarea>
                        @error('tujuan') <p style="color: #DC2626; font-size: .75rem; margin-top: 5px; font-weight: 600;">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 16px; font-size: 1rem; margin-top: 10px;">Kirim</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
