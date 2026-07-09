<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OtpRegistrationMail;

class AuthApiController extends Controller
{
    // ── LOGIN ────────────────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        $user  = Auth::user();
        
        if (is_null($user->email_verified_at)) {
            // Admin: bypass OTP, langsung verifikasi
            if ($user->role === 'admin') {
                $user->email_verified_at = now();
                $user->save();
            }
            // Akun lama (sebelum sistem OTP): auto-verifikasi
            elseif ($user->created_at < '2026-05-21') {
                $user->email_verified_at = now();
                $user->save();
            }
            // Akun baru yang belum verifikasi: kirim OTP
            else {
                $otp = rand(100000, 999999);
                Cache::put('otp_register_' . $user->id, $otp, now()->addMinutes(10));
                try {
                    Mail::to($user->email)->send(new OtpRegistrationMail($otp));
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim email OTP ke {$user->email}: " . $e->getMessage());
                }
                Log::info("OTP untuk {$user->email} adalah: {$otp}");

                return response()->json([
                    'success' => false,
                    'message' => 'Email belum diverifikasi. Kami telah mengirimkan OTP baru ke email Anda.',
                    'needs_verification' => true,
                    'user_id' => $user->id,
                ], 403);
            }
        }

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    // ── REGISTER ─────────────────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'alamat'                => 'nullable|string|max:500',
            'no_hp'                 => 'nullable|string|max:20',
            'tipe_anggota'          => 'nullable|string|in:tetap,sementara,anggota_tetap,pengunjung,private',
            'password'              => ['required', 'confirmed', Password::min(8)],
        ], [
            'email.unique'          => 'Email sudah terdaftar.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        $inputTipe = $request->tipe_anggota ?? 'tetap';
        $tipeAnggota = 'anggota_tetap';
        if ($inputTipe === 'sementara' || $inputTipe === 'pengunjung') {
            $tipeAnggota = 'pengunjung';
        } elseif ($inputTipe === 'private') {
            $tipeAnggota = 'private';
        }

        $user = User::create([
            'name'         => $request->name,
            'email'        => $request->email,
            'alamat'       => $request->alamat,
            'no_hp'        => $request->no_hp,
            'tipe_anggota' => $tipeAnggota,
            'password'     => Hash::make($request->password),
            'role'         => 'anggota',
            'status'       => 'aktif',
        ]);

        $otp = rand(100000, 999999);
        Cache::put('otp_register_' . $user->id, $otp, now()->addMinutes(10));
        
        try {
            Mail::to($user->email)->send(new OtpRegistrationMail($otp));
        } catch (\Exception $e) {
            Log::error("Gagal mengirim email OTP ke {$user->email}: " . $e->getMessage());
        }
        Log::info("OTP untuk {$user->email} adalah: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil. Silakan cek email Anda untuk kode verifikasi OTP.',
            'needs_verification' => true,
            'user_id' => $user->id,
        ], 201);
    }

    // ── VERIFY OTP ───────────────────────────────────────────
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'otp'     => 'required|numeric',
        ]);

        $cachedOtp = Cache::get('otp_register_' . $request->user_id);

        if (!$cachedOtp || $cachedOtp != $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid atau sudah kadaluarsa.',
            ], 400);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
        }

        $user->email_verified_at = now();
        $user->save();
        Cache::forget('otp_register_' . $user->id);

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Verifikasi berhasil.',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    // ── RESEND OTP ───────────────────────────────────────────
    public function resendOtp(Request $request)
    {
        $request->validate(['user_id' => 'required']);
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
        }
        if ($user->email_verified_at) {
            return response()->json(['success' => false, 'message' => 'Email sudah terverifikasi.'], 400);
        }

        $otp = rand(100000, 999999);
        Cache::put('otp_register_' . $user->id, $otp, now()->addMinutes(10));
        
        try {
            Mail::to($user->email)->send(new OtpRegistrationMail($otp));
        } catch (\Exception $e) {
            Log::error("Gagal mengirim email OTP ke {$user->email}: " . $e->getMessage());
        }
        Log::info("OTP untuk {$user->email} adalah: {$otp}");

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru telah dikirim ke email Anda.',
        ]);
    }

    // ── GET USER ─────────────────────────────────────────────
    public function me(Request $request)
    {
        return response()->json(['data' => $this->getUserWithLencana($request->user())]);
    }

    private function getUserWithLencana($user)
    {
        $lencana = \App\Models\RaporPagelaran::with('tarian')
            ->where('user_id', $user->id)
            ->where('lulus', true)
            ->get()
            ->map(function ($item) {
                return [
                    'tarian_id'     => $item->tarian_id,
                    'nama_tarian'   => $item->tarian->nama ?? 'Tarian',
                    'tanggal_lulus' => $item->created_at ? $item->created_at->toDateString() : null,
                    'foto_tarian'   => ($item->tarian && $item->tarian->foto) ? asset('storage/' . $item->tarian->foto) : null,
                ];
            });

        $userData = $user->toArray();
        $userData['lencana'] = $lencana;
        return $userData;
    }

    // ── LOGOUT ───────────────────────────────────────────────
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Berhasil keluar.',
        ]);
    }

    // ── UPDATE PROFILE ─────────────────────────────────────────
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'telepon' => 'nullable|string|max:20',
            'no_hp'   => 'nullable|string|max:20',
            'alamat'  => 'nullable|string|max:500',
        ]);

        $data = $request->only('name', 'email', 'alamat');
        if ($request->has('no_hp')) {
            $data['no_hp'] = $request->no_hp;
        } elseif ($request->has('telepon')) {
            $data['no_hp'] = $request->telepon;
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $this->getUserWithLencana($user),
        ]);
    }

    // ── UPDATE FOTO PROFIL ──────────────────────────────────────
    public function updateFoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = $request->user();

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto && \Storage::disk('public')->exists($user->foto)) {
                \Storage::disk('public')->delete($user->foto);
            }
            $path = $request->file('foto')->store('profil_anggota', 'public');
            $user->foto = $path; // Ini akan otomatis menyimpan 'profil_anggota/namafile.jpg'
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui.',
            'user'    => $this->getUserWithLencana($user),
        ]);
    }

    // ── UPDATE PASSWORD ────────────────────────────────────────
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password'     => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $request->user();

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }

    // ── LOGIN WITH GOOGLE (Firebase Token) ─────────────────────
    // Menerima Firebase ID Token dari Flutter, verifikasi ke Google,
    // lalu buat/temukan user dan kembalikan Sanctum token.
    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'firebase_token' => 'required|string',
        ]);

        try {
            // Verifikasi Firebase ID Token langsung ke Google API
            $firebaseToken = $request->firebase_token;
            $response = \Illuminate\Support\Facades\Http::get(
                "https://www.googleapis.com/oauth2/v3/tokeninfo?id_token={$firebaseToken}"
            );

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google tidak valid.',
                ], 401);
            }

            $payload = $response->json();

            // Pastikan token ditujukan untuk project Firebase yang benar
            $validAudiences = [
                '611713677810-86g03v381kvd8lua78c650t8elicjtce.apps.googleusercontent.com',
                // Tambahkan client_id lain jika ada
            ];
            if (!in_array($payload['aud'] ?? '', $validAudiences) && ($payload['azp'] ?? '') !== '611713677810') {
                // Fallback: cek issuer
                $iss = $payload['iss'] ?? '';
                if (!in_array($iss, ['accounts.google.com', 'https://accounts.google.com'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Token tidak valid untuk aplikasi ini.',
                    ], 401);
                }
            }

            $googleEmail = $payload['email'] ?? null;
            $googleName  = $payload['name']  ?? 'Pengguna Google';
            $googleId    = $payload['sub']   ?? null;

            if (!$googleEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat mengambil email dari akun Google.',
                ], 400);
            }

            // Cari user berdasarkan email, atau buat baru jika belum ada
            $user = User::firstOrCreate(
                ['email' => $googleEmail],
                [
                    'name'              => $googleName,
                    'google_id'         => $googleId,
                    'password'          => Hash::make(\Illuminate\Support\Str::random(32)),
                    'role'              => 'anggota',
                    'status'            => 'aktif',
                    'tipe_anggota'      => 'anggota_tetap',
                    'foto'              => $payload['picture'] ?? null,
                    'email_verified_at' => now(),
                ]
            );

            // Update google_id & email_verified_at jika user sudah ada tapi belum punya
            $updates = [];
            if (!$user->google_id && $googleId) {
                $updates['google_id'] = $googleId;
            }
            if (is_null($user->email_verified_at)) {
                $updates['email_verified_at'] = now();
            }
            if (!empty($updates)) {
                $user->update($updates);
            }

            // Hapus token lama agar tidak menumpuk (opsional)
            $user->tokens()->where('name', 'flutter-google')->delete();

            $token = $user->createToken('flutter-google')->plainTextToken;

            return response()->json([
                'success' => true,
                'token'   => $token,
                'user'    => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }
}
