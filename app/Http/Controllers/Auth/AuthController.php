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
        // Kirim daftar tarian aktif untuk dropdown anggota sementara
        $tarian = \App\Models\Tarian::where('aktif', true)->orderBy('urutan')->get();
        return view('auth.register', compact('tarian'));
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
            $rules['no_hp']      = 'required|string|max:20';
            $rules['tarian_id']  = 'required|exists:tarian,id';
            $rules['sessions']   = 'required|array|min:1';
            $rules['sessions.*.tanggal'] = 'required|date|after_or_equal:today';
            $rules['sessions.*.jam']     = 'required|string';
        } else {
            $rules['alamat'] = 'required|string|max:500';
        }

        $request->validate($rules, [
            'email.unique'        => 'Email sudah terdaftar.',
            'password.confirmed'  => 'Konfirmasi password tidak cocok.',
            'password.min'        => 'Password minimal 8 karakter.',
            'password.mixed_case' => 'Password harus mengandung huruf besar dan huruf kecil.',
            'password.numbers'    => 'Password harus mengandung setidaknya 1 angka.',
            'tarian_id.required'  => 'Harap pilih tarian yang ingin dipelajari.',
            'tarian_id.exists'    => 'Tarian yang dipilih tidak valid.',
            'sessions.required'   => 'Harap pilih setidaknya satu sesi latihan.',
        ]);

        // Hitung kadaluarsa otomatis: tanggal sesi terakhir + 3 hari
        $tglKadaluarsa = null;
        if ($isSementara && !empty($request->sessions)) {
            $tanggalTerakhir = collect($request->sessions)->pluck('tanggal')->max();
            $tglKadaluarsa   = \Carbon\Carbon::parse($tanggalTerakhir)->addDays(3)->toDateString();
        }

        // 1. Create User
        $user = User::create([
            'name'               => $request->name,
            'email'              => $request->email,
            'alamat'             => $isSementara ? null : $request->alamat,
            'no_hp'              => $request->no_hp,
            'password'           => Hash::make($request->password),
            'role'               => 'anggota',
            'status'             => 'aktif',
            'tipe_anggota'       => $isSementara ? 'pengunjung' : 'anggota_tetap',
            'tgl_kadaluarsa'     => $tglKadaluarsa,
        ]);

        // 2. If Temporary, Store Sessions with proper tarian_id
        if ($isSementara) {
            foreach ($request->sessions as $session) {
                $tanggal = $session['tanggal'];
                $jam     = $session['jam'];

                $requestedTarianId = $request->tarian_id;

                // 1. Dapatkan daftar pendaftaran aktif/pending di jam tersebut
                $existingPendaftaran = \App\Models\PendaftaranTari::with('tarian')
                    ->where('tanggal_latihan', $tanggal)
                    ->where('jam_latihan', $jam)
                    ->whereIn('status', ['aktif', 'pending'])
                    ->get();

                // 2. Hitung jumlah jenis tarian yang unik (Maksimal 2 tempat/ruangan)
                $uniqueTarianIds = $existingPendaftaran->pluck('tarian_id')->unique()->values()->toArray();
                
                // 3. Cek kapasitas tempat/ruangan
                if (!in_array($requestedTarianId, $uniqueTarianIds)) {
                    // Jika tarian yang diminta belum ada, cek apakah 2 tempat sudah penuh
                    if (count($uniqueTarianIds) >= 2) {
                        $namaTarian = $existingPendaftaran->pluck('tarian.nama')->unique()->implode(' dan ');
                        return back()->withInput()->with('error', "Maaf, seluruh tempat latihan pada {$tanggal} jam {$jam} sudah penuh oleh kelas {$namaTarian}. Silakan pilih jam lain, atau pilih salah satu tarian tersebut jika ingin bergabung.");
                    }
                }

                // 4. Cek kapasitas orang dalam kelompok tarian yang dipilih (Maksimal 5 orang)
                $countOrangDiTarian = $existingPendaftaran->where('tarian_id', $requestedTarianId)->count();
                if ($countOrangDiTarian >= 5) {
                    return back()->withInput()->with('error', "Maaf, kelompok tari yang Anda pilih pada {$tanggal} jam {$jam} sudah mencapai batas maksimal (5 orang). Silakan pilih jam lain.");
                }

                \App\Models\PendaftaranTari::create([
                    'user_id'         => $user->id,
                    'tarian_id'       => $request->tarian_id,
                    'tanggal_latihan' => $tanggal,
                    'jam_latihan'     => $jam,
                    'status'          => 'pending',
                    'tanggal_daftar'  => now()->toDateString(),
                    'catatan'         => null,
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
                'name'              => $googleUser->getName(),
                'google_id'         => $googleUser->getId(),
                'password'          => Hash::make(\Illuminate\Support\Str::random(32)),
                'role'              => 'anggota',
                'status'            => 'aktif',
                'tipe_anggota'      => 'anggota_tetap',
                'foto'              => $googleUser->getAvatar(),
                'email_verified_at' => now(),
            ]
        );

        // Update google_id & email_verified_at jika user sudah ada tapi belum punya
        $updates = [];
        if (!$user->google_id) {
            $updates['google_id'] = $googleUser->getId();
        }
        if (is_null($user->email_verified_at)) {
            $updates['email_verified_at'] = now();
        }
        if (!empty($updates)) {
            $user->update($updates);
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard');
    }
}