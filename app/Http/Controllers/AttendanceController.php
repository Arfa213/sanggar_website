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

        $isDynamic = false;
        $decoded = base64_decode($token, true);
        if ($decoded) {
            $parts = explode('|', $decoded);
            if (count($parts) === 3 && $parts[0] === 'DYN-QR') {
                $isDynamic = true;
                $timestamp = (int)$parts[1];
                $signature = $parts[2];
                
                // 1. Validasi Signature HMAC
                $expectedSignature = hash_hmac('sha256', "DYN-QR|" . $timestamp, config('app.key'));
                if (!hash_equals($expectedSignature, $signature)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code tidak valid atau telah dimodifikasi.'
                    ], 400);
                }
                
                // 2. Validasi Batas Waktu Kadaluarsa (15 Detik)
                $diff = time() - $timestamp;
                if ($diff > 15 || $diff < -5) {
                    return response()->json([
                        'success' => false,
                        'message' => 'QR Code sudah kadaluarsa. Silakan scan QR Code terbaru di layar admin.'
                    ], 400);
                }
            }
        }

        if ($isDynamic) {
            $today = now()->toDateString();
            
            // Cari jadwal latihan hari ini yang belum di-absen
            $pendaftaran = PendaftaranTari::where('user_id', $user->id)
                ->where('tanggal_latihan', $today)
                ->where('status', 'aktif')
                ->whereNotExists(function ($query) use ($today, $user) {
                    $query->select(\Illuminate\Support\Facades\DB::raw(1))
                          ->from('kehadiran')
                          ->whereColumn('kehadiran.tarian_id', 'pendaftaran_tari.tarian_id')
                          ->where('kehadiran.tanggal', $today)
                          ->where('kehadiran.user_id', $user->id);
                })
                ->first();

            if (!$pendaftaran) {
                // Cek apakah sebenarnya punya jadwal tapi sudah absen semua
                $hasSession = PendaftaranTari::where('user_id', $user->id)
                    ->where('tanggal_latihan', $today)
                    ->where('status', 'aktif')
                    ->exists();
                    
                if ($hasSession) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda sudah mencatat kehadiran untuk semua sesi Anda hari ini.'
                    ], 400);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki jadwal latihan yang aktif pada hari ini.'
                    ], 400);
                }
            }

            $sesi_tanggal = $today;
            $jadwal_id = $pendaftaran->jadwal_id;
            $tarian_id = $pendaftaran->tarian_id;

        } else {
            // Logika lama untuk QR Statis (SesiKehadiran)
            $sesi = SesiKehadiran::where('barcode_token', $token)
                ->where('aktif', true)
                ->first();

            if (!$sesi) {
                return response()->json([
                    'success' => false,
                    'message' => 'QR Code tidak valid atau sudah tidak aktif.'
                ], 400);
            }

            $sesi_tanggal = $sesi->tanggal;
            $jadwal_id = $sesi->jadwal_id;
            $tarian_id = $sesi->tarian_id;

            // Cek apakah user sudah absen di sesi statis ini
            $sudahAbsen = Kehadiran::where('user_id', $user->id)
                ->where('tanggal', $sesi_tanggal)
                ->where('jadwal_id', $jadwal_id)
                ->where('tarian_id', $tarian_id)
                ->exists();

            if ($sudahAbsen) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah mencatat kehadiran untuk sesi ini.'
                ], 400);
            }
        }

        // Simpan Kehadiran
        Kehadiran::create([
            'user_id'       => $user->id,
            'jadwal_id'     => $jadwal_id,
            'tarian_id'     => $tarian_id,
            'tanggal'       => $sesi_tanggal,
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
