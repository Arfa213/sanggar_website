<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Mail\OtpRegistrationMail;

class AuthController extends Controller
{
    // ─────────────────────────────────────────
    //  SHOW LOGIN FORM
    // ─────────────────────────────────────────
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // ─────────────────────────────────────────
    //  PROCESS LOGIN (with brute force protection)
    // ─────────────────────────────────────────
    public function login(Request $request)
    {
        // Rate limiting: max 5 attempts per minute
        $key = 'login_attempts:' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."]);
        }

        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        RateLimiter::hit($key, 60);

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key);
            $request->session()->regenerate();

            $user = Auth::user();

            // Admin selalu bisa login tanpa OTP
            // Akun lama (sebelum sistem OTP) juga langsung masuk & otomatis diverifikasi
            if (is_null($user->email_verified_at)) {
                if ($user->role === 'admin') {
                    // Admin: bypass OTP, langsung verifikasi
                    $user->email_verified_at = now();
                    $user->save();
                } elseif ($user->created_at < '2026-05-21') {
                    // Akun lama (sebelum OTP system): auto-verifikasi
                    $user->email_verified_at = now();
                    $user->save();
                } else {
                    // Akun baru yang belum verifikasi: kirim OTP
                    Auth::logout();
                    $otp = rand(100000, 999999);
                    Cache::put('otp_register_' . $user->id, $otp, now()->addMinutes(10));
                    try {
                        Mail::to($user->email)->send(new OtpRegistrationMail($otp));
                    } catch (\Exception $e) {
                        Log::error('Gagal kirim OTP: ' . $e->getMessage());
                    }
                    Log::info("OTP login untuk {$user->email}: {$otp}");
                    return redirect()->route('otp.verify.form', ['user_id' => $user->id])
                        ->with('info', 'Email Anda belum diverifikasi. OTP baru telah dikirim ke email Anda.');
                }
            }

            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('dashboard'));
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    // ─────────────────────────────────────────
    //  SHOW REGISTER FORM
    // ─────────────────────────────────────────
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    // ─────────────────────────────────────────
    //  PROCESS REGISTER (with strong password)
    // ─────────────────────────────────────────
    public function register(Request $request)
    {
        $isSementara = $request->tipe_anggota === 'sementara';

        $rules = [
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'password'     => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers(),
            ],
            'tipe_anggota' => 'required|in:tetap,sementara',
        ];

        if ($isSementara) {
            $rules['no_hp']          = 'required|string|max:20';
            $rules['tarian_custom']  = 'required|string|max:100';
            $rules['sessions']       = 'required|array|min:1';
            $rules['sessions.*.tanggal'] = 'required|date|after_or_equal:today';
            $rules['sessions.*.jam']     = 'required|string';
        } else {
            $rules['alamat'] = 'required|string|max:500';
        }

        $request->validate($rules, [
            'email.unique'       => 'Email sudah terdaftar.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers'   => 'Password harus mengandung setidaknya 1 angka.',
            'sessions.required'  => 'Harap pilih setidaknya satu sesi latihan.',
        ]);

        // 1. Create User
        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'alamat'       => $isSementara ? null : $request->alamat,
            'no_hp'        => $isSementara ? $request->no_hp : $request->no_hp, // Tetap allow no_hp if exists
            'password'     => Hash::make($request->password),
            'role'         => 'anggota',
            'status'       => 'aktif',
            'tipe_anggota' => $isSementara ? 'pengunjung' : 'anggota_tetap', 
        ]);

        // 2. If Temporary, Store Sessions
        if ($isSementara) {
            $defaultTarian = \App\Models\Tarian::where('aktif', true)->first();
            
            foreach ($request->sessions as $session) {
                $tanggal = $session['tanggal'];
                $jam     = $session['jam'];

                $count = \App\Models\PendaftaranTari::where('tanggal_latihan', $tanggal)
                    ->where('jam_latihan', $jam)
                    ->whereIn('status', ['aktif', 'nonaktif'])
                    ->count();

                if ($count >= 2) {
                    return back()->withInput()->with('error', "Maaf, sesi pada tanggal {$tanggal} jam {$jam} sudah penuh. Silakan pilih jam lain.");
                }

                \App\Models\PendaftaranTari::create([
                    'user_id'         => $user->id,
                    'tarian_id'       => $defaultTarian ? $defaultTarian->id : null,
                    'tanggal_latihan' => $tanggal,
                    'jam_latihan'     => $jam,
                    'status'          => 'nonaktif',
                    'tanggal_daftar'  => now()->toDateString(),
                    'catatan'         => 'Sesi khusus: ' . $request->tarian_custom,
                ]);
            }
        }

        // Kirim OTP ke email
        $otp = rand(100000, 999999);
        Cache::put('otp_register_' . $user->id, $otp, now()->addMinutes(10));
        try {
            Mail::to($user->email)->send(new OtpRegistrationMail($otp));
        } catch (\Exception $e) {
            Log::error('Gagal kirim OTP: ' . $e->getMessage());
        }
        Log::info("OTP registrasi untuk {$user->email}: {$otp}");

        return redirect()->route('otp.verify.form', ['user_id' => $user->id])
            ->with('success', 'Pendaftaran berhasil! Silakan cek email Anda untuk kode OTP verifikasi.');
    }

    // ─────────────────────────────────────────
    //  SHOW OTP VERIFY FORM (Web)
    // ─────────────────────────────────────────
    public function showOtpForm(Request $request)
    {
        $userId = $request->query('user_id');
        if (!$userId || !User::find($userId)) {
            return redirect()->route('register')->withErrors(['email' => 'Sesi tidak valid. Silakan daftar ulang.']);
        }
        return view('auth.otp-verify', ['userId' => $userId]);
    }

    // ─────────────────────────────────────────
    //  VERIFY OTP (Web)
    // ─────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'otp'     => 'required|numeric|digits:6',
        ], [
            'otp.digits' => 'Kode OTP harus 6 digit.',
            'otp.numeric' => 'Kode OTP hanya boleh angka.',
        ]);

        $cachedOtp = Cache::get('otp_register_' . $request->user_id);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return back()
                ->withInput()
                ->withErrors(['otp' => 'Kode OTP tidak valid atau sudah kadaluarsa (10 menit).']);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return redirect()->route('register')->withErrors(['email' => 'Pengguna tidak ditemukan.']);
        }

        $user->email_verified_at = now();
        $user->save();
        Cache::forget('otp_register_' . $user->id);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Email berhasil diverifikasi! Selamat datang, ' . $user->name . '!');
    }

    // ─────────────────────────────────────────
    //  RESEND OTP (Web)
    // ─────────────────────────────────────────
    public function resendOtp(Request $request)
    {
        $request->validate(['user_id' => 'required']);
        $user = User::find($request->user_id);

        if (!$user) {
            return back()->withErrors(['otp' => 'Pengguna tidak ditemukan.']);
        }
        if ($user->email_verified_at) {
            return redirect()->route('login')->with('success', 'Email Anda sudah terverifikasi. Silakan login.');
        }

        $otp = rand(100000, 999999);
        Cache::put('otp_register_' . $user->id, $otp, now()->addMinutes(10));
        try {
            Mail::to($user->email)->send(new OtpRegistrationMail($otp));
        } catch (\Exception $e) {
            Log::error('Gagal kirim ulang OTP: ' . $e->getMessage());
        }
        Log::info("Resend OTP untuk {$user->email}: {$otp}");

        return back()->with('success', 'Kode OTP baru telah dikirim ke email ' . $user->email);
    }

    // ─────────────────────────────────────────
    //  LOGOUT
    // ─────────────────────────────────────────
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', 'Anda berhasil keluar.');
    }

    // ─────────────────────────────────────────
    //  REDIRECT KE GOOGLE (OAuth Web)
    // ─────────────────────────────────────────
    public function redirectToGoogle()
    {
        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    // ─────────────────────────────────────────
    //  HANDLE CALLBACK DARI GOOGLE
    // ─────────────────────────────────────────
    public function handleGoogleCallback()
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Login Google gagal. Silakan coba lagi.']);
        }

        // Cari atau buat user berdasarkan email Google
        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name'         => $googleUser->getName(),
                'google_id'    => $googleUser->getId(),
                'password'     => Hash::make(\Illuminate\Support\Str::random(32)),
                'role'         => 'anggota',
                'status'       => 'aktif',
                'tipe_anggota' => 'anggota_tetap',
                'foto'         => $googleUser->getAvatar(),
            ]
        );

        // Update google_id jika user sudah ada tapi belum punya
        if (!$user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
}