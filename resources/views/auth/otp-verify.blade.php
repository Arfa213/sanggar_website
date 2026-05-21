@extends('layouts.app')

@section('title', 'Verifikasi OTP')

@section('content')
<section class="auth-page auth-page--center">
    <div class="container auth-container auth-container--single">
        <div class="auth-form-wrap" style="max-width:480px;margin:auto">
            <span class="badge">Verifikasi Email</span>
            <h1 class="auth-title">Masukkan Kode OTP</h1>
            <p class="auth-desc">Kode 6 digit telah dikirim ke email Anda. Berlaku 10 menit.</p>

            <div class="auth-card">

                {{-- Pesan Sukses --}}
                @if(session('success'))
                    <div class="alert alert-success" style="margin-bottom:16px">
                        {{ session('success') }}
                    </div>
                @endif

                {{-- Pesan Info (login belum verif) --}}
                @if(session('info'))
                    <div class="alert" style="background:#FFF8E1;border:1px solid #FFD54F;color:#795548;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:.875rem">
                        ℹ️ {{ session('info') }}
                    </div>
                @endif

                {{-- Error --}}
                @if($errors->any())
                    <div class="alert alert-error" style="margin-bottom:16px">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $userId }}">

                    <div style="margin-bottom:28px">
                        {{-- 6 Kotak OTP --}}
                        <div id="otp-boxes" style="display:flex;gap:10px;justify-content:center;margin:24px 0">
                            @for($i = 0; $i < 6; $i++)
                                <input
                                    type="text"
                                    maxlength="1"
                                    inputmode="numeric"
                                    pattern="[0-9]"
                                    class="otp-box"
                                    id="otp_{{ $i }}"
                                    style="width:52px;height:60px;text-align:center;font-size:1.5rem;font-weight:700;
                                           border:2px solid #E5E2DE;border-radius:12px;background:#fff;
                                           color:var(--primary, #c9a763);outline:none;transition:border-color .2s"
                                    autocomplete="off"
                                >
                            @endfor
                        </div>
                        <input type="hidden" name="otp" id="otp_combined">
                        @error('otp')
                            <p style="color:#E53935;font-size:.8rem;text-align:center;margin-top:-8px">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit" style="width:100%">
                        Verifikasi Sekarang
                    </button>
                </form>

                {{-- Kirim Ulang OTP --}}
                <div style="text-align:center;margin-top:20px">
                    <form method="POST" action="{{ route('otp.resend') }}" id="resendForm">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $userId }}">
                        <p style="color:#888;font-size:.875rem">
                            Tidak menerima kode?
                        </p>
                        <button type="submit" id="resendBtn"
                            style="background:none;border:none;cursor:pointer;color:var(--primary, #c9a763);
                                   font-weight:700;font-size:.9rem;padding:4px 0"
                            onclick="startCountdown(event)">
                            Kirim Ulang OTP
                        </button>
                        <span id="countdown" style="color:#888;font-size:.875rem;display:none"></span>
                    </form>
                </div>

                <p class="form-switch" style="text-align:center;margin-top:16px">
                    <a href="{{ route('login') }}" style="color:#888;font-size:.875rem">← Kembali ke Halaman Login</a>
                </p>
            </div>
        </div>
    </div>
</section>

<style>
.otp-box:focus {
    border-color: var(--primary, #c9a763) !important;
    box-shadow: 0 0 0 3px rgba(201,167,99,0.15);
}
.otp-box.is-filled {
    border-color: var(--primary, #c9a763);
    background: rgba(201,167,99,0.05);
}
</style>

<script>
const boxes = document.querySelectorAll('.otp-box');
const combined = document.getElementById('otp_combined');

boxes.forEach((box, idx) => {
    box.addEventListener('input', (e) => {
        const val = e.target.value.replace(/\D/g, '');
        e.target.value = val;
        if (val) {
            e.target.classList.add('is-filled');
            if (idx < 5) boxes[idx + 1].focus();
        } else {
            e.target.classList.remove('is-filled');
        }
        updateCombined();
        // Auto submit kalau sudah 6 digit
        if (getAllDigits().length === 6) {
            updateCombined();
            document.getElementById('otpForm').submit();
        }
    });

    box.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && !e.target.value && idx > 0) {
            boxes[idx - 1].focus();
            boxes[idx - 1].value = '';
            boxes[idx - 1].classList.remove('is-filled');
            updateCombined();
        }
    });

    box.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = e.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
        paste.split('').forEach((char, i) => {
            if (boxes[i]) {
                boxes[i].value = char;
                boxes[i].classList.add('is-filled');
            }
        });
        updateCombined();
        if (paste.length === 6) {
            setTimeout(() => document.getElementById('otpForm').submit(), 100);
        }
    });
});

function getAllDigits() {
    return Array.from(boxes).map(b => b.value).join('');
}

function updateCombined() {
    combined.value = getAllDigits();
}

// Fokus otomatis ke kotak pertama
boxes[0].focus();

// Countdown kirim ulang
function startCountdown(e) {
    const digits = getAllDigits();
    if (document.getElementById('resendBtn').disabled) {
        e.preventDefault();
        return;
    }

    let seconds = 60;
    const btn = document.getElementById('resendBtn');
    const countdown = document.getElementById('countdown');

    btn.disabled = true;
    btn.style.opacity = '0.4';
    countdown.style.display = 'inline';
    countdown.textContent = ` (${seconds}s)`;

    const interval = setInterval(() => {
        seconds--;
        countdown.textContent = ` (${seconds}s)`;
        if (seconds <= 0) {
            clearInterval(interval);
            btn.disabled = false;
            btn.style.opacity = '1';
            countdown.style.display = 'none';
        }
    }, 1000);
}

// Jika ada error OTP, shake animation
@if($errors->has('otp'))
const otpBoxes = document.getElementById('otp-boxes');
otpBoxes.style.animation = 'shake 0.4s ease';
setTimeout(() => otpBoxes.style.animation = '', 500);
// Kosongkan semua box jika ada error
boxes.forEach(b => { b.value = ''; b.classList.remove('is-filled'); });
combined.value = '';
boxes[0].focus();
@endif
</script>

<style>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%       { transform: translateX(-8px); }
    40%       { transform: translateX(8px); }
    60%       { transform: translateX(-6px); }
    80%       { transform: translateX(6px); }
}
</style>

@endsection
