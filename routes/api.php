<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfilApiController;
use App\Http\Controllers\Api\PelatihApiController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\TarianApiController;
use App\Http\Controllers\Api\GaleriApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\GeminiController;
use App\Http\Controllers\Api\TopengApiController;
use App\Http\Controllers\Api\ArchiveApiController;
use App\Http\Controllers\Api\GuestApiController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ChatbotController;
use App\Models\JadwalLatihan;
use App\Models\PendaftaranTari;
use App\Models\Kehadiran;

Route::prefix('v1')->group(function () {

    // ── PUBLIC ────────────────────────────────────────────────
    Route::get('/profil',      [ProfilApiController::class,  'index']);
    Route::get('/pelatih',     [PelatihApiController::class, 'index']);
    Route::get('/events',      [EventApiController::class,   'index']);
    Route::get('/events/{id}', [EventApiController::class,   'show']);
    Route::get('/tarian',      [TarianApiController::class,  'index']);
    Route::get('/tarian/{id}', [TarianApiController::class,  'show']);
    Route::get('/galeri',      [GaleriApiController::class,  'index']);
    Route::post('/ai/chat', [GeminiController::class, 'chat']);
    Route::get('/archive', [ArchiveApiController::class, 'index']);
    Route::get('/topeng', [TopengApiController::class, 'index']);
    Route::get('/topeng/{id}', [TopengApiController::class, 'show']);
    Route::post('/tamu', [GuestApiController::class, 'store']);
    Route::post('/ai/recommend', [ChatbotController::class, 'recommendDance']);

    Route::get('/jadwal', function () {
        $data = JadwalLatihan::where('aktif', true)->orderBy('urutan')->get();
        return response()->json(['data' => $data]);
    });

    // ── AUTH ──────────────────────────────────────────────────
    Route::post('/auth/login',    [AuthApiController::class, 'login']);
    Route::post('/auth/register', [AuthApiController::class, 'register']);

    // ── PROTECTED ─────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me',      [AuthApiController::class, 'me']);
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::put('/auth/profile', [AuthApiController::class, 'updateProfile']);
        Route::put('/auth/password', [AuthApiController::class, 'updatePassword']);

        Route::post('/attendance/scan', [AttendanceController::class, 'processScan']);

        Route::get('/pendaftaran', function (Illuminate\Http\Request $req) {
            $data = PendaftaranTari::with(['tarian', 'jadwal'])
                ->where('user_id', $req->user()->id)
                ->where('status', 'aktif')
                ->get();
            return response()->json(['data' => $data]);
        });

        Route::post('/pendaftaran', function (Illuminate\Http\Request $req) {
            $req->validate([
                'tarian_id' => 'required|exists:tarian,id',
                'jadwal_id' => 'required|exists:jadwal_latihan,id',
                'catatan'   => 'nullable|string|max:500',
            ]);
            $exists = PendaftaranTari::where([
                'user_id'   => $req->user()->id,
                'tarian_id' => $req->tarian_id,
                'jadwal_id' => $req->jadwal_id,
            ])->exists();
            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Kamu sudah terdaftar di kelas ini!'], 422);
            }
            $p = PendaftaranTari::create([
                'user_id'        => $req->user()->id,
                'tarian_id'      => $req->tarian_id,
                'jadwal_id'      => $req->jadwal_id,
                'status'         => 'aktif',
                'tanggal_daftar' => now()->toDateString(),
                'catatan'        => $req->catatan,
            ]);
            $p->load(['tarian', 'jadwal']);
            return response()->json([
                'success' => true,
                'message' => "Berhasil mendaftar Tari {$p->tarian->nama}! Jadwal: {$p->jadwal->hari}, {$p->jadwal->jam_mulai}–{$p->jadwal->jam_selesai} di {$p->jadwal->tempat}.",
                'data'    => $p,
            ], 201);
        });

        Route::post('/pendaftaran/{id}/batalkan', function (Illuminate\Http\Request $req, $id) {
            $p = PendaftaranTari::where('id', $id)->where('user_id', $req->user()->id)->firstOrFail();
            $p->update(['status' => 'nonaktif']);
            return response()->json(['success' => true, 'message' => 'Pendaftaran berhasil dibatalkan.']);
        });


        Route::get('/kehadiran-saya', function (Illuminate\Http\Request $req) {
            $data = Kehadiran::with(['jadwal', 'tarian'])
                ->where('user_id', $req->user()->id)
                ->orderByDesc('tanggal')
                ->paginate(20);
            return response()->json($data);
        });

        Route::get('/kehadiran-saya/statistik', function (Illuminate\Http\Request $req) {
            $bulan = $req->get('bulan', now()->format('Y-m'));
            $stats = Kehadiran::where('user_id', $req->user()->id)
                ->whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$bulan])
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');
            $hadir = (int) ($stats['hadir'] ?? 0);
            $izin  = (int) ($stats['izin']  ?? 0);
            $alpa  = (int) ($stats['alpa']  ?? 0);
            $total = $hadir + $izin + $alpa;
            return response()->json(['data' => [
                'bulan' => $bulan, 'hadir' => $hadir, 'izin' => $izin, 'alpa' => $alpa,
                'total' => $total, 'persen_hadir' => $total > 0 ? round($hadir / $total * 100) : 0,
            ]]);
        });

        Route::get('/pengumuman', function () {
            $data = \App\Models\Pengumuman::orderBy('created_at', 'desc')->get();
            return response()->json(['data' => $data]);
        });
    });
});
