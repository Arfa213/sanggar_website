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

                    <!-- Type Selector -->
                    <div class="auth-type-selector">
                        <div class="type-option active" onclick="setRegType('tetap', this)">
                            <span class="type-icon">👑</span>
                            <div class="type-text">
                                <strong>Anggota Tetap</strong>
                                <small>Latihan rutin mingguan</small>
                            </div>
                        </div>
                        <div class="type-option" onclick="setRegType('sementara', this)">
                            <span class="type-icon">⭐</span>
                            <div class="type-text">
                                <strong>Anggota Sementara</strong>
                                <small>Sesi private fleksibel</small>
                            </div>
                        </div>
                        <input type="hidden" name="tipe_anggota" id="tipe_anggota" value="tetap">
                    </div>

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

                    <!-- Fields for Anggota Tetap -->
                    <div id="alamat-field">
                        <div class="form-group">
                            <label for="alamat">Alamat <span class="required">*</span></label>
                            <textarea
                                id="alamat"
                                name="alamat"
                                class="form-input form-textarea @error('alamat') is-error @enderror"
                                placeholder="Masukan alamat Lengkap"
                                rows="3"
                            >{{ old('alamat') }}</textarea>
                            @error('alamat')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <!-- Fields for Anggota Sementara -->
                    <div id="sementara-fields" style="display:none">
                        <div class="form-group">
                            <label for="no_hp">Nomor WhatsApp <span class="required">*</span></label>
                            <input
                                type="tel"
                                id="no_hp"
                                name="no_hp"
                                class="form-input"
                                placeholder="0812..."
                                value="{{ old('no_hp') }}"
                            >
                        </div>
                        <div class="form-group">
                            <label for="tarian_id">Tarian yang Ingin Dipelajari <span class="required">*</span></label>
                            <select id="tarian_id" name="tarian_id" class="form-input" style="appearance:auto">
                                <option value="">— Pilih Tarian —</option>
                                @foreach($tarian as $t)
                                    <option value="{{ $t->id }}" {{ old('tarian_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sessions-wrap">
                            <label>Pilih Sesi Latihan <span class="required">*</span></label>
                            <div id="sessions-container">
                                <!-- Sesi akan ditambahkan di sini via JS -->
                            </div>
                            <button type="button" class="btn-add-session" onclick="addSession()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                Tambah Sesi Latihan
                            </button>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:24px">
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

                    <button type="submit" class="btn-submit" style="margin-top:12px">Daftar Sekarang</button>

                </form>

                {{-- DIVIDER --}}
                <div class="auth-divider">
                    <span>atau</span>
                </div>

                {{-- GOOGLE SIGN-IN BUTTON --}}
                <a href="{{ route('auth.google') }}" class="btn-google">
                    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                         alt="Google" width="20" height="20">
                    <span>Daftar dengan Google</span>
                </a>

                <p class="form-switch">
                    Sudah punya akun?
                    <a href="{{ route('login') }}">Masuk di sini</a>
                </p>
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

let sessionCount = 0;

function setRegType(type, el) {
    document.querySelectorAll('.type-option').forEach(opt => opt.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('tipe_anggota').value = type;

    if (type === 'sementara') {
        document.getElementById('alamat-field').style.display = 'none';
        document.getElementById('sementara-fields').style.display = 'block';
        document.getElementById('alamat').required = false;
        document.getElementById('no_hp').required = true;
        document.getElementById('tarian_custom').required = true;
        if (sessionCount === 0) addSession();
    } else {
        document.getElementById('alamat-field').style.display = 'block';
        document.getElementById('sementara-fields').style.display = 'none';
        document.getElementById('alamat').required = true;
        document.getElementById('no_hp').required = false;
        document.getElementById('tarian_custom').required = false;
    }
}

function addSession() {
    sessionCount++;
    const container = document.getElementById('sessions-container');
    const div = document.createElement('div');
    div.className = 'session-item';
    div.id = `session-${sessionCount}`;
    div.innerHTML = `
        <div class="session-header">
            <span>Sesi #${sessionCount}</span>
            <button type="button" class="btn-remove-session" onclick="removeSession(${sessionCount})">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                Hapus
            </button>
        </div>
        <div class="session-grid">
            <div class="form-group">
                <label>Tanggal</label>
                <input type="date" name="sessions[${sessionCount}][tanggal]" class="form-input" required min="${new Date().toISOString().split('T')[0]}" onchange="validateDay(this)">
            </div>
            <div class="form-group">
                <label>Jam Latihan</label>
                <select name="sessions[${sessionCount}][jam]" class="form-input" required>
                    <option value="">Pilih Jam</option>
                    <option value="08:00">08:00</option>
                    <option value="10:00">10:00</option>
                    <option value="13:00">13:00</option>
                    <option value="15:00">15:00</option>
                    <option value="16:00">16:00</option>
                    <option value="17:00">17:00</option>
                    <option value="19:00">19:00</option>
                    <option value="20:00">20:00</option>
                </select>
            </div>
        </div>
    `;
    container.appendChild(div);
}

function removeSession(id) {
    if (document.querySelectorAll('.session-item').length <= 1) {
        alert("Setidaknya harus ada 1 sesi latihan.");
        return;
    }
    const el = document.getElementById(`session-${id}`);
    if (el) el.remove();
}

function validateDay(input) {
    const date = new Date(input.value);
    const day = date.getDay(); // 0 = Sunday, 5 = Friday
    const jamSelect = input.closest('.session-grid').querySelector('select');

    // Reset options
    Array.from(jamSelect.options).forEach(opt => {
        opt.disabled = false;
        opt.style.display = 'block';
    });

    if (day === 0) {
        alert("Maaf, hari Minggu sanggar libur / penuh untuk latihan rutin.");
        input.value = "";
    } else if (day === 5) {
        // Friday: Only 08:00 - 13:00
        alert("Hari Jumat hanya tersedia jam 08:00 s/d 13:00.");
        Array.from(jamSelect.options).forEach(opt => {
            if (opt.value) {
                const hour = parseInt(opt.value.split(':')[0]);
                if (hour >= 13) {
                    opt.disabled = true;
                    opt.style.display = 'none';
                }
            }
        });
        if (jamSelect.value) {
            const hour = parseInt(jamSelect.value.split(':')[0]);
            if (hour >= 13) jamSelect.value = "";
        }
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
@endsection