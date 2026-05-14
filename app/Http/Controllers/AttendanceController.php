<?php

namespace App\Http\Controllers;

use App\Models\{Kehadiran, Tarian, PendaftaranTari, User, SesiKehadiran};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    // ── Member Attendance Scanner ──────────────────────────────────
    public function processScan(Request $request)
    {
        $token = $request->barcode_token;
        $user = Auth::user();

        // 1. Cari Sesi Aktif dengan token tersebut
        $sesi = SesiKehadiran::where('barcode_token', $token)
            ->where('aktif', true)
            ->first();

        if (!$sesi) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak valid atau sudah tidak aktif.'
            ], 400);
        }

        // 2. Cek apakah user sudah absen di sesi ini
        $sudahAbsen = Kehadiran::where('user_id', $user->id)
            ->where('tanggal', $sesi->tanggal)
            ->where('jadwal_id', $sesi->jadwal_id)
            ->where('tarian_id', $sesi->tarian_id)
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mencatat kehadiran untuk sesi ini.'
            ], 400);
        }

        // 3. Simpan Kehadiran
        Kehadiran::create([
            'user_id'       => $user->id,
            'jadwal_id'     => $sesi->jadwal_id,
            'tarian_id'     => $sesi->tarian_id,
            'tanggal'       => $sesi->tanggal,
            'status'        => 'hadir',
            'metode_absen'  => 'barcode',
            'barcode_token' => $token,
            'scan_at'       => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kehadiran berhasil dicatat! Selamat berlatih.'
        ]);
    }
}
