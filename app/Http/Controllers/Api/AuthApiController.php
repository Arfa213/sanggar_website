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
}
