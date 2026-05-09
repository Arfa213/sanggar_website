@extends('layouts.app')

@section('title', 'Daftar Anggota')

@@section('content')
<section class="auth-page">
    <div class="auth-container" style="max-width: 550px;">
        
        <div class="auth-header">
            <div class="auth-logo" style="background: var(--dark);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            </div>
            <h1 class="auth-title">Daftar Anggota</h1>
            <p class="auth-desc">Bergabunglah menjadi bagian dari pelestari seni Sanggar Mulya Bhakti.</p>
        </div>

        @if(session('success'))
            <div class="alert" style="background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>Terjadi kesalahan pada input Anda.</span>
            </div>
        @endif

        <form method="POST" action="{{ route('register.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input
                    type="text"
                    name="name"
                    class="form-input"
                    placeholder="Masukkan nama sesuai identitas"
                    value="{{ old('name') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Email</label>
                <input
                    type="email"
                    name="email"
                    class="form-input"
                    placeholder="nama@email.com"
                    value="{{ old('email') }}"
                    required
                >
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Domisili</label>
                <textarea
                    name="alamat"
                    class="form-input"
                    placeholder="Contoh: Jl. Raya Indramayu No. 123"
                    rows="2"
                    required
                >{{ old('alamat') }}</textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Buat Password</label>
                <div class="input-password-wrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-input"
                        placeholder="Minimal 8 karakter"
                        required
                        oninput="checkPasswordStrength(this.value)"
                    >
                    <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">
                        <svg class="eye-open" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                
                <div class="password-requirements" id="pwRequirements" style="margin-top: 12px; padding: 12px; background: #f9f9f9; border-radius: 12px; font-size: 0.75rem; border: 1px solid #eee;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                        <div id="req-length" style="display:flex;align-items:center;gap:5px;color:#999"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/></svg> 8+ Karakter</div>
                        <div id="req-upper" style="display:flex;align-items:center;gap:5px;color:#999"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/></svg> Huruf Besar</div>
                        <div id="req-lower" style="display:flex;align-items:center;gap:5px;color:#999"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/></svg> Huruf Kecil</div>
                        <div id="req-number" style="display:flex;align-items:center;gap:5px;color:#999"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><circle cx="12" cy="12" r="10"/></svg> Angka</div>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 32px;">
                <label class="form-label">Konfirmasi Password</label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    class="form-input"
                    placeholder="Ulangi password"
                    required
                    oninput="checkPasswordMatch()"
                >
                <div id="pwMatch" style="margin-top: 8px; font-size: 0.75rem; font-weight: 700; display: none;"></div>
            </div>

            <button type="submit" class="btn-submit">Daftar Menjadi Anggota</button>

            <p class="form-switch">
                Sudah memiliki akun?
                <a href="{{ route('login') }}">Masuk di sini</a>
            </p>
        </form>
    </div>
</section>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    input.type = input.type === 'password' ? 'text' : 'password';
}

function checkPasswordStrength(pw) {
    const checks = {
        'req-length': pw.length >= 8,
        'req-upper': /[A-Z]/.test(pw),
        'req-lower': /[a-z]/.test(pw),
        'req-number': /\d/.test(pw)
    };
    for (const [id, met] of Object.entries(checks)) {
        const el = document.getElementById(id);
        el.style.color = met ? '#16a34a' : '#999';
        el.querySelector('svg').style.stroke = met ? '#16a34a' : '#999';
    }
}

function checkPasswordMatch() {
    const pw = document.getElementById('password');
    const confirm = document.getElementById('password_confirmation');
    const msg = document.getElementById('pwMatch');
    if (confirm.value.length > 0) {
        msg.style.display = 'block';
        const match = pw.value === confirm.value;
        msg.textContent = match ? '✓ Password cocok' : 'Konfirmasi password berbeda';
        msg.style.color = match ? '#16a34a' : '#dc2626';
    } else {
        msg.style.display = 'none';
    }
}
</script>
@endsection