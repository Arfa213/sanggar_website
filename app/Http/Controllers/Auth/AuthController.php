<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

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

        RateLimiter::hit($key, 60); // 1 minute window

        $credentials = $request->only('email', 'password');
        $remember    = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($key); // Clear on success
            $request->session()->regenerate();

            if (Auth::user()->role === 'admin') {
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

                // 2.1 Capacity Check (Max 2 sessions per slot total)
                $count = \App\Models\PendaftaranTari::where('tanggal_latihan', $tanggal)
                    ->where('jam_latihan', $jam)
                    ->whereIn('status', ['aktif', 'nonaktif']) // Count confirmed and pending
                    ->count();

                if ($count >= 2) {
                    return back()->withInput()->with('error', "Maaf, sesi pada tanggal {$tanggal} jam {$jam} sudah penuh. Silakan pilih jam lain.");
                }

                \App\Models\PendaftaranTari::create([
                    'user_id'         => $user->id,
                    'tarian_id'       => $defaultTarian ? $defaultTarian->id : null,
                    'tanggal_latihan' => $tanggal,
                    'jam_latihan'     => $jam,
                    'status'          => 'nonaktif', // Status 'nonaktif' di sini berarti 'Pending Konfirmasi'
                    'tanggal_daftar'  => now()->toDateString(),
                    'catatan'         => 'Sesi khusus: ' . $request->tarian_custom,
                ]);
            }
        }

        return redirect()->route('login')
            ->with('success', 'Pendaftaran berhasil! Booking Anda sedang menunggu konfirmasi admin. Silakan cek berkala.');
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