<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

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
            'tipe_anggota' => $tipeAnggota,
            'password'     => Hash::make($request->password),
            'role'         => 'anggota',
            'status'       => 'aktif',
        ]);

        $token = $user->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    // ── GET USER ─────────────────────────────────────────────
    public function me(Request $request)
    {
        return response()->json(['data' => $request->user()]);
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
            'user'    => $user,
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
            $user->update(['foto' => $path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui.',
            'user'    => $user,
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
                    'name'         => $googleName,
                    'google_id'    => $googleId,
                    'password'     => Hash::make(\Illuminate\Support\Str::random(32)),
                    'role'         => 'anggota',
                    'status'       => 'aktif',
                    'tipe_anggota' => 'anggota_tetap',
                    'foto'         => $payload['picture'] ?? null,
                ]
            );

            // Update google_id jika user sudah ada tapi belum punya google_id
            if (!$user->google_id && $googleId) {
                $user->update(['google_id' => $googleId]);
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
