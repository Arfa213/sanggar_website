@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<section class="auth-page">
    <div class="container auth-container">

        {{-- LEFT IMAGE --}}
        <div class="auth-image" style="background:#FAF8F6; display:flex; align-items:center; justify-content:center; padding: 40px;">
            <img src="{{ asset('assets/images/logosanggar.png') }}" alt="Logo Sanggar" style="max-width:85%; max-height:85%; object-fit:contain;">
        </div>

        {{-- RIGHT FORM --}}
        <div class="auth-form-wrap">
            <span class="badge">Selamat Datang</span>
            <h1 class="auth-title">Masuk</h1>
            <p class="auth-desc">Masuk ke akun Anda untuk mengakses dashboard</p>

            <div class="auth-card">

                {{-- SESSION ERROR --}}
                @if(session('error'))
                    <div class="alert alert-error">{{ session('error') }}</div>
                @endif

                {{-- VALIDATION ERRORS --}}
                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') is-error @enderror"
                            placeholder="Nama@gmail.com"
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input @error('password') is-error @enderror"
                                placeholder="Masukan password"
                                autocomplete="current-password"
                                required
                            >
                            <button type="button" class="toggle-pw" aria-label="Tampilkan password" onclick="togglePassword('password', this)">
                                <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="form-forgot">
                        <a href="{{ route('password.request') }}">Lupa Password ?</a>
                    </div>

                    <button type="submit" class="btn-submit">Masuk</button>

                    <p class="form-switch">
                        Belum Punya Akun?
                        <a href="{{ route('register') }}">Daftar di sini</a>
                    </p>
                </form>

                {{-- DIVIDER --}}
                <div class="auth-divider">
                    <span>atau</span>
                </div>

                {{-- GOOGLE SIGN-IN BUTTON --}}
                <a href="{{ route('auth.google') }}" class="btn-google">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                         alt="Google" width="20" height="20">
                    <span>Masuk dengan Google</span>
                </a>

                <style>
                    .auth-divider {
                        display: flex;
                        align-items: center;
                        gap: 12px;
                        margin: 20px 0 16px;
                        color: #999;
                        font-size: 13px;
                    }
                    .auth-divider::before,
                    .auth-divider::after {
                        content: '';
                        flex: 1;
                        height: 1px;
                        background: #E5E2DE;
                    }
                    .btn-google {
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        gap: 10px;
                        width: 100%;
                        padding: 12px 20px;
                        background: #fff;
                        border: 1.5px solid #E5E2DE;
                        border-radius: 12px;
                        color: #333;
                        font-size: 14px;
                        font-weight: 600;
                        text-decoration: none;
                        transition: all 0.2s ease;
                        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
                    }
                    .btn-google:hover {
                        background: #F8F7F5;
                        border-color: #C65D2E;
                        box-shadow: 0 4px 12px rgba(198,93,46,0.15);
                        transform: translateY(-1px);
                    }
                </style>
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