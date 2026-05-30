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

    Route::get('/file/{path}', function ($path) {
        $fullPath = storage_path('app/public/' . $path);
        if (!file_exists($fullPath)) abort(404);
        return response()->file($fullPath);
    })->where('path', '.*');

    // ── AUTH ──────────────────────────────────────────────────
    Route::post('/auth/login',    [AuthApiController::class, 'login']);
    Route::post('/auth/register', [AuthApiController::class, 'register']);
    Route::post('/auth/verify-otp', [AuthApiController::class, 'verifyOtp']);
    Route::post('/auth/resend-otp', [AuthApiController::class, 'resendOtp']);
    Route::post('/auth/google',   [AuthApiController::class, 'loginWithGoogle']); // Google Sign-In (Firebase)

    // ── PROTECTED ─────────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function () {

        Route::get('/auth/me',      [AuthApiController::class, 'me']);
        Route::post('/auth/logout', [AuthApiController::class, 'logout']);
        Route::put('/auth/profile', [AuthApiController::class, 'updateProfile']);
        Route::post('/auth/foto', [AuthApiController::class, 'updateFoto']);
        Route::put('/auth/password', [AuthApiController::class, 'updatePassword']);
        
        Route::post('/auth/update-fcm-token', function (Illuminate\Http\Request $req) {
            $req->validate(['fcm_token' => 'required|string']);
            $req->user()->update(['fcm_token' => $req->fcm_token]);
            return response()->json(['success' => true, 'message' => 'FCM Token updated.']);
        });

        Route::get('/rapor-saya', function (Illuminate\Http\Request $req) {
            $data = \App\Models\RaporPagelaran::with(['event', 'tarian', 'pelatih'])
                ->where('user_id', $req->user()->id)
                ->join('events', 'rapor_pagelaran.event_id', '=', 'events.id')
                ->orderBy('events.tanggal', 'desc')
                ->select('rapor_pagelaran.*')
                ->get();
            return response()->json(['data' => $data]);
        });

        Route::get('/rapor-saya/summary', function (Illuminate\Http\Request $req) {
            $userId = $req->user()->id;
            $totalPagelaran = \App\Models\RaporPagelaran::where('user_id', $userId)->count();
            
            if ($totalPagelaran === 0) {
                return response()->json(['data' => null]);
            }
            
            $summary = \App\Models\RaporPagelaran::where('user_id', $userId)
                ->selectRaw('
                    AVG(nilai_teknik) as avg_teknik,
                    AVG(nilai_hafalan) as avg_hafalan,
                    AVG(nilai_ekspresi) as avg_ekspresi,
                    AVG(nilai_penampilan) as avg_penampilan,
                    AVG(nilai_kehadiran) as avg_kehadiran,
                    AVG(nilai_akhir) as avg_akhir
                ')
                ->first();
                
            $data = [
                'total_pagelaran' => $totalPagelaran,
                'avg_teknik' => round($summary->avg_teknik, 1),
                'avg_hafalan' => round($summary->avg_hafalan, 1),
                'avg_ekspresi' => round($summary->avg_ekspresi, 1),
                'avg_penampilan' => round($summary->avg_penampilan, 1),
                'avg_kehadiran' => round($summary->avg_kehadiran, 1),
                'avg_akhir' => round($summary->avg_akhir, 1),
                'predikat_umum' => match(true) {
                    $summary->avg_akhir >= 90 => 'Istimewa',
                    $summary->avg_akhir >= 80 => 'Sangat Baik',
                    $summary->avg_akhir >= 70 => 'Baik',
                    $summary->avg_akhir >= 60 => 'Cukup',
                    default => 'Perlu Peningkatan',
                }
            ];
            
            return response()->json(['data' => $data]);
        });

        Route::post('/attendance/scan', [AttendanceController::class, 'processScan']);

        Route::get('/pendaftaran', function (Illuminate\Http\Request $req) {
            $user = $req->user();
            if ($user->tipe_anggota === 'pengunjung') {
                $data = PendaftaranTari::with(['tarian', 'jadwal'])
                    ->where('user_id', $user->id)
                    ->get();
            } else {
                $data = PendaftaranTari::with(['tarian', 'jadwal'])
                    ->where('user_id', $user->id)
                    ->where('status', 'aktif')
                    ->get();
            }
            return response()->json(['data' => $data]);
        });

        Route::post('/pendaftaran', function (Illuminate\Http\Request $req) {
            $user = $req->user();

            // Semua tipe anggota menggunakan tanggal_latihan dan jam_latihan
            $req->validate([
                'tarian_id'       => 'required|exists:tarian,id',
                'tanggal_latihan' => 'required|date|after_or_equal:today',
                'jam_latihan'     => 'required|string',
                'catatan'         => 'nullable|string|max:500',
            ]);

            $tarianId = $req->tarian_id;
            $tanggal  = $req->tanggal_latihan;
            $jam      = $req->jam_latihan;

            // 1. Cek apakah user sudah daftar tarian ini di tanggal & jam yang sama
            $exists = PendaftaranTari::where([
                'user_id'         => $user->id,
                'tarian_id'       => $tarianId,
                'tanggal_latihan' => $tanggal,
                'jam_latihan'     => $jam,
            ])->whereIn('status', ['aktif', 'pending'])->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'Kamu sudah booking sesi ini!'], 422);
            }

            // 2. Cek Kapasitas Aula (Maksimal 2 tarian berbeda per jam)
            $sessionsAtTime = PendaftaranTari::where('tanggal_latihan', $tanggal)
                ->where('jam_latihan', $jam)
                ->whereIn('status', ['aktif', 'pending'])
                ->with('tarian')
                ->get();

            $distinctDances = $sessionsAtTime->pluck('tarian.nama', 'tarian_id')->unique();

            if ($distinctDances->count() >= 2) {
                if (!$distinctDances->has($tarianId)) {
                    $names = $distinctDances->implode(' dan ');
                    return response()->json(['success' => false, 'message' => "Maaf, aula sudah penuh pada jam ini oleh kelas: {$names}. Silakan pilih jam atau tanggal lain."], 422);
                }
            }

            // 3. Cek kapasitas orang dalam kelompok tarian yang dipilih (Maksimal 5 orang)
            $countOrangDiTarian = $sessionsAtTime->where('tarian_id', $tarianId)->count();
            if ($countOrangDiTarian >= 5) {
                return response()->json(['success' => false, 'message' => "Maaf, kelompok tari yang Anda pilih pada jam ini sudah mencapai batas maksimal (5 orang). Silakan pilih jam lain."], 422);
            }

            // 4. Tentukan status: pengunjung perlu konfirmasi admin, anggota tetap langsung aktif
            $isPengunjung = $user->tipe_anggota === 'pengunjung';
            $status       = $isPengunjung ? 'pending' : 'aktif';

            // 5. Update kadaluarsa jika pengunjung & sesi baru lebih jauh
            if ($isPengunjung) {
                $tglBaru = \Carbon\Carbon::parse($tanggal)->addDays(3)->toDateString();
                if (is_null($user->tgl_kadaluarsa) || $tglBaru > $user->tgl_kadaluarsa) {
                    \App\Models\User::where('id', $user->id)->update(['tgl_kadaluarsa' => $tglBaru]);
                }
            }

            // 6. Simpan Pendaftaran
            $p = PendaftaranTari::create([
                'user_id'         => $user->id,
                'tarian_id'       => $tarianId,
                'tanggal_latihan' => $tanggal,
                'jam_latihan'     => $jam,
                'status'          => $status,
                'tanggal_daftar'  => now()->toDateString(),
                'catatan'         => $req->catatan,
            ]);

            $p->load(['tarian']);

            $msg = $isPengunjung
                ? "Booking Tari {$p->tarian->nama} terkirim! Tanggal {$tanggal} jam {$jam}. Menunggu konfirmasi admin."
                : "Berhasil mendaftar Tari {$p->tarian->nama}! Tanggal {$tanggal} jam {$jam}.";

            return response()->json([
                'success' => true,
                'message' => $msg,
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
