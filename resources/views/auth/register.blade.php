@extends('layouts.app')

@section('title', 'Daftar Anggota')

@section('content')
<section class="auth-page auth-page--center">
    <div class="container auth-container auth-container--single">

        <div class="auth-form-wrap auth-form-wrap--wide">
            <span class="badge">Pendaftaran</span>
            <h1 class="auth-title">Daftar Anggota</h1>
            <p class="auth-desc">Bergabunglah dengan komunitas pencinta seni traditional</p>

            <div class="auth-card">
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- SESSION ERROR --}}
@if(session('error'))
    <div class="alert alert-error">{{ session('error') }}</div>
@endif
                @if($errors->any())
                    <div class="alert alert-error">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('register.post') }}">
                    @csrf

                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="required">*</span></label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input @error('name') is-error @enderror"
                            placeholder="Masukan Nama Lengkap"
                            value="{{ old('name') }}"
                            required
                        >
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-input @error('email') is-error @enderror"
                            placeholder="Nama@gmail.com"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="alamat">Alamat <span class="required">*</span></label>
                        <textarea
                            id="alamat"
                            name="alamat"
                            class="form-input form-textarea @error('alamat') is-error @enderror"
                            placeholder="Masukan alamat Lengkap"
                            rows="3"
                            required
                        >{{ old('alamat') }}</textarea>
                        @error('alamat')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <div class="input-password-wrap">
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-input @error('password') is-error @enderror"
                                placeholder="Minimal 8 karakter, huruf besar, angka"
                                required
                                minlength="8"
                                pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$"
                                oninput="checkPasswordStrength(this.value)"
                            >
                            <button type="button" class="toggle-pw" aria-label="Tampilkan password" onclick="togglePassword('password', this)">
                                <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <div class="password-requirements" id="pwRequirements" style="margin-top:8px;padding:12px;background:#F8F7F5;border-radius:8px;font-size:.8rem;">
                            <p style="font-weight:700;color:var(--dark);margin-bottom:8px">Password harus mengandung:</p>
                            <div id="req-length" style="display:flex;align-items:center;gap:6px;margin-bottom:4px;color:#999">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                                Minimal 8 karakter
                            </div>
                            <div id="req-upper" style="display:flex;align-items:center;gap:6px;margin-bottom:4px;color:#999">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                                Huruf besar (A-Z)
                            </div>
                            <div id="req-lower" style="display:flex;align-items:center;gap:6px;margin-bottom:4px;color:#999">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                                Huruf kecil (a-z)
                            </div>
                            <div id="req-number" style="display:flex;align-items:center;gap:6px;color:#999">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>
                                Angka (0-9)
                            </div>
                        </div>
                        @error('password')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Password <span class="required">*</span></label>
                        <div class="input-password-wrap">
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                class="form-input"
                                placeholder="Ulangi password yang sama"
                                required
                                oninput="checkPasswordMatch()"
                            >
                            <button type="button" class="toggle-pw" aria-label="Tampilkan password" onclick="togglePassword('password_confirmation', this)">
                                <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="eye-closed" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <div id="pwMatch" style="margin-top:8px;font-size:.8rem;color:#999;display:none">Konfirmasi password belum sama</div>
                    </div>

                    <button type="submit" class="btn-submit">Daftar</button>

                    <p class="form-switch">
                        Sudah punya akun?
                        <a href="{{ route('login') }}">Masuk di sini</a>
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

function checkPasswordStrength(pw) {
    const checks = {
        'req-length': pw.length >= 8,
        'req-upper': /[A-Z]/.test(pw),
        'req-lower': /[a-z]/.test(pw),
        'req-number': /\d/.test(pw)
    };

    const icons = {
        true: { svg: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#22C55E" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="9 12 12 15 16 9"/></svg>', color: '#22C55E' },
        false: { svg: '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"/></svg>', color: '#EF4444' }
    };

    for (const [id, met] of Object.entries(checks)) {
        const el = document.getElementById(id);
        if (el) {
            el.innerHTML = icons[met].svg + el.innerHTML.replace(/<svg.*<\/svg>/, '').trim();
            el.style.color = icons[met].color;
        }
    }
}

function checkPasswordMatch() {
    const pw = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const msg = document.getElementById('pwMatch');

    if (confirm.value.length > 0) {
        msg.style.display = 'block';
        if (pw.value === confirm.value) {
            msg.textContent = '✓ Password sudah cocok';
            msg.style.color = '#22C55E';
        } else {
            msg.textContent = 'Konfirmasi password belum sama';
            msg.style.color = '#EF4444';
        }
    } else {
        msg.style.display = 'none';
    }
}
</script>
@endsection