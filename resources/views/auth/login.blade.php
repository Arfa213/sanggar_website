@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<section class="auth-page">
    <div class="container auth-container">

        {{-- LEFT IMAGE --}}
        <div class="auth-image">
            <div class="img-placeholder auth-placeholder">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#C65D2E" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </div>
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