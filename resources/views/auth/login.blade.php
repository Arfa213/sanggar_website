@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<section class="auth-page">
    <div class="container auth-container">

        {{-- LEFT SIDE: Image/Branding --}}
        <div class="auth-image">
            <div style="position: absolute; inset: 0; background: linear-gradient(135deg, rgba(198,93,46,0.9), rgba(26,26,26,0.8)); z-index: 1;"></div>
            <div style="position: relative; z-index: 2; padding: 60px; color: white;">
                <span class="badge" style="background: rgba(255,255,255,0.2); color: white; border: none;">Warisan Budaya</span>
                <h2 style="font-family: var(--font-display); font-size: 3rem; font-weight: 900; line-height: 1.1; margin-bottom: 20px;">Melestarikan Seni,<br>Membangun Jati Diri.</h2>
                <p style="font-size: 1.1rem; opacity: 0.9; max-width: 400px; line-height: 1.6;">Masuk untuk mengelola profil Anda dan terus terhubung dengan kegiatan Sanggar Mulya Bhakti.</p>
                
                <div style="margin-top: 40px; display: flex; gap: 20px;">
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 900;">500+</div>
                        <div style="font-size: 0.8rem; opacity: 0.7;">Anggota Aktif</div>
                    </div>
                    <div style="border-left: 1px solid rgba(255,255,255,0.2); height: 40px;"></div>
                    <div style="text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 900;">20+</div>
                        <div style="font-size: 0.8rem; opacity: 0.7;">Tarian Tradisional</div>
                    </div>
                </div>
            </div>
            
            {{-- Decorative pattern or subtle image if available --}}
            <div style="position: absolute; bottom: -50px; right: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.03); border-radius: 50%; filter: blur(50px);"></div>
        </div>

        {{-- RIGHT SIDE: Form --}}
        <div class="auth-form-wrap">
            <div class="auth-card">
                <span class="badge">Selamat Datang</span>
                <h1 class="auth-title">Masuk</h1>
                <p class="auth-desc">Masuk ke akun Anda untuk mengakses dashboard</p>

                {{-- SESSION ERROR --}}
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                {{-- VALIDATION ERRORS --}}
                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <p style="margin-bottom: 4px;">• {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') is-error @enderror"
                            placeholder="nama@email.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label for="password" style="margin-bottom: 0;">Password</label>
                            <a href="{{ route('password.request') }}" style="font-size: 0.8rem; font-weight: 600; color: var(--primary);">Lupa Password?</a>
                        </div>
                        <div class="input-password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input @error('password') is-error @enderror"
                                placeholder="••••••••"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="toggle-pw" aria-label="Tampilkan password" onclick="togglePassword('password', this)">
                                <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" style="margin-top: 10px;">Masuk Sekarang</button>

                    <p class="form-switch">
                        Belum punya akun?
                        <a href="{{ route('register') }}">Daftar di sini</a>
                    </p>
                </form>
            </div>
        </div>

    </div>
</section>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const eyeOpen = btn.querySelector('.eye-open');
    const eyeClosed = btn.querySelector('.eye-closed');

    if (input.type === 'password') {
        input.type = 'text';
        eyeOpen.style.display = 'none';
        eyeClosed.style.display = 'block';
    } else {
        input.type = 'password';
        eyeOpen.style.display = 'block';
        eyeClosed.style.display = 'none';
    }
}
</script>
@endsection