<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    ProfileController,
    EventController,
    DigitalArchiveController,
    DashboardController,
    PenjadwalanController,
    AttendanceController,
    ChatbotController,
};
use App\Http\Controllers\Api\GeminiController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\{
    DashboardController     as AdminDashboard,
    ProfileController       as AdminProfile,
    EventController         as AdminEvent,
    TarianController        as AdminTarian,
    AnggotaController        as AdminAnggota,
    GaleriController         as AdminGaleri,
    KehadiranController      as AdminKehadiran,
    TopengController         as AdminTopeng,
    BookingController        as AdminBooking,
    PengumumanController     as AdminPengumuman,
};

// ── PUBLIC ────────────────────────────────────────────────────
Route::get('/',               [HomeController::class,           'index'])->name('home');
Route::get('/profile',        [ProfileController::class,        'index'])->name('profile');
Route::get('/event',          [EventController::class,          'index'])->name('event');
Route::get('/digital-archive',[DigitalArchiveController::class, 'index'])->name('digital-archive');
Route::get('/galeri/{seksi?}', [App\Http\Controllers\GaleriController::class, 'frontendIndex'])->name('galeri.frontend.index');

// ── TAMU (Public Guest Log) ──────────────────────────────────
Route::get('/tamu',           [\App\Http\Controllers\TamuController::class, 'index'])->name('tamu.index');
Route::post('/tamu',          [\App\Http\Controllers\TamuController::class, 'store'])->name('tamu.store');

// ── AUTH ──────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

// Google OAuth (Web)
Route::get('/auth/google',          [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Lupa password — halaman saja (kirim email butuh konfigurasi mail)
Route::get('/forgot-password', function () {
    return view('auth.forgot-password');
})->name('password.request');

// ── MEMBER (harus login) ──────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/dashboard',                  [DashboardController::class,   'index'])->name('dashboard');
    Route::get('/penjadwalan',                [PenjadwalanController::class, 'index'])->name('penjadwalan');
    Route::post('/penjadwalan/daftar',        [PenjadwalanController::class, 'daftar'])->name('penjadwalan.daftar');
    Route::post('/penjadwalan/batalkan/{id}', [PenjadwalanController::class, 'batalkan'])->name('penjadwalan.batalkan');
    Route::get('/penjadwalan/riwayat',        [PenjadwalanController::class, 'riwayatKehadiran'])->name('penjadwalan.kehadiran');
    
    // Member Attendance Scanner
    Route::post('/kehadiran/scan/process',    [AttendanceController::class, 'processScan'])->name('member.kehadiran.process');
    
    // Member Profile
    Route::get('/my-profile',                 [DashboardController::class, 'editProfile'])->name('member.profile');
    Route::post('/my-profile/update',         [DashboardController::class, 'updateProfile'])->name('member.profile.update');
});


// ── ADMIN ─────────────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Profil, Pelatih, Pengelola, Jadwal
    Route::get('/profil',                        [AdminProfile::class, 'index'])->name('profil.index');
    Route::post('/profil/update',                [AdminProfile::class, 'updateProfil'])->name('profil.update');

    Route::post('/profil/pelatih',               [AdminProfile::class, 'storePelatih'])->name('pelatih.store');
    Route::post('/profil/pelatih/{id}/update',   [AdminProfile::class, 'updatePelatih'])->name('pelatih.update');
    Route::post('/profil/pelatih/{id}/delete',   [AdminProfile::class, 'destroyPelatih'])->name('pelatih.destroy');

    Route::post('/profil/pengelola',             [AdminProfile::class, 'storePengelola'])->name('pengelola.store');
    Route::post('/profil/pengelola/{id}/update', [AdminProfile::class, 'updatePengelola'])->name('pengelola.update');
    Route::post('/profil/pengelola/{id}/delete', [AdminProfile::class, 'destroyPengelola'])->name('pengelola.destroy');

    Route::post('/profil/jadwal',                [AdminProfile::class, 'storeJadwal'])->name('jadwal.store');
    Route::post('/profil/jadwal/{id}/update',    [AdminProfile::class, 'updateJadwal'])->name('jadwal.update');
    Route::post('/profil/jadwal/{id}/delete',    [AdminProfile::class, 'destroyJadwal'])->name('jadwal.destroy');

    // Event — pakai {id} numerik bukan model binding
    Route::get('/event',               [AdminEvent::class, 'index'])->name('event.index');
    Route::get('/event/create',        [AdminEvent::class, 'create'])->name('event.create');
    Route::post('/event',              [AdminEvent::class, 'store'])->name('event.store');
    Route::get('/event/{id}/edit',     [AdminEvent::class, 'edit'])->name('event.edit');
    Route::put('/event/{id}',          [AdminEvent::class, 'update'])->name('event.update');
    Route::delete('/event/{id}/delete',[AdminEvent::class, 'destroy'])->name('event.destroy');

    // Tarian
    Route::get('/tarian/pdf',          [AdminTarian::class, 'downloadPdf'])->name('tarian.pdf');
    Route::get('/tarian',              [AdminTarian::class, 'index'])->name('tarian.index');
    Route::get('/tarian/create',       [AdminTarian::class, 'create'])->name('tarian.create');
    Route::post('/tarian',             [AdminTarian::class, 'store'])->name('tarian.store');
    Route::get('/tarian/{id}/edit',    [AdminTarian::class, 'edit'])->name('tarian.edit');
    Route::put('/tarian/{id}',         [AdminTarian::class, 'update'])->name('tarian.update');
    Route::delete('/tarian/{id}/delete',[AdminTarian::class, 'destroy'])->name('tarian.destroy');

    // Topeng
    Route::get('/topeng',              [AdminTopeng::class, 'index'])->name('topeng.index');
    Route::get('/topeng/create',       [AdminTopeng::class, 'create'])->name('topeng.create');
    Route::post('/topeng',             [AdminTopeng::class, 'store'])->name('topeng.store');
    Route::get('/topeng/{id}/edit',    [AdminTopeng::class, 'edit'])->name('topeng.edit');
    Route::put('/topeng/{id}',         [AdminTopeng::class, 'update'])->name('topeng.update');
    Route::delete('/topeng/{id}/delete',[AdminTopeng::class, 'destroy'])->name('topeng.destroy');

    // Anggota
    Route::get('/anggota/pdf',            [AdminAnggota::class, 'downloadPdf'])->name('anggota.pdf');
    Route::get('/anggota/excel',           [AdminAnggota::class, 'downloadExcel'])->name('anggota.excel');
    Route::get('/anggota',                [AdminAnggota::class, 'index'])->name('anggota.index');
    Route::get('/anggota/create',         [AdminAnggota::class, 'create'])->name('anggota.create');
    Route::post('/anggota',               [AdminAnggota::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{id}/edit',      [AdminAnggota::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{id}',           [AdminAnggota::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{id}/delete', [AdminAnggota::class, 'destroy'])->name('anggota.destroy');
    Route::patch('/anggota/{id}/toggle',  [AdminAnggota::class, 'toggleStatus'])->name('anggota.toggle');

    // Galeri
    Route::get('/galeri',              [AdminGaleri::class, 'index'])->name('galeri.index');
    Route::get('/galeri/create',       [AdminGaleri::class, 'create'])->name('galeri.create');
    Route::post('/galeri',             [AdminGaleri::class, 'store'])->name('galeri.store');
    Route::get('/galeri/{id}/edit',    [AdminGaleri::class, 'edit'])->name('galeri.edit');
    Route::put('/galeri/{id}',         [AdminGaleri::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/{id}',      [AdminGaleri::class, 'destroy'])->name('galeri.destroy');

    // Kehadiran
    Route::get('/kehadiran',                    [AdminKehadiran::class, 'index'])->name('kehadiran.index');
    Route::post('/kehadiran/input',             [AdminKehadiran::class, 'inputKehadiran'])->name('kehadiran.input');
    Route::post('/kehadiran/simpan',            [AdminKehadiran::class, 'simpanKehadiran'])->name('kehadiran.simpan');
    // Laporan Kehadiran
    Route::get('/kehadiran/laporan',            [AdminKehadiran::class, 'laporan'])->name('kehadiran.laporan');
    Route::get('/kehadiran/laporan/anggota',    [AdminKehadiran::class, 'laporanAnggota'])->name('kehadiran.laporan.anggota');
    Route::get('/kehadiran/laporan/pengunjung', [AdminKehadiran::class, 'laporanPengunjung'])->name('kehadiran.laporan.pengunjung');
    Route::get('/kehadiran/laporan/export-pdf', [AdminKehadiran::class, 'exportPdf'])->name('kehadiran.pdf');

    // Permanent QR (Kelas Barcode)
    Route::get('/kehadiran/permanent/{id}',     [AdminKehadiran::class, 'showPermanentQR'])->name('kehadiran.permanent.show');
    Route::delete('/kehadiran/permanent/{id}',  [AdminKehadiran::class, 'deletePermanentQR'])->name('kehadiran.permanent.destroy');

    // Booking Anggota Sementara
    Route::get('/booking',            [AdminBooking::class, 'index'])->name('booking.index');
    Route::get('/booking/pending-count', [AdminBooking::class, 'getPendingCount'])->name('booking.pending-count');
    Route::post('/booking/{id}/confirm', [AdminBooking::class, 'confirm'])->name('booking.confirm');
    Route::post('/booking/{id}/reject',  [AdminBooking::class, 'reject'])->name('booking.reject');
    Route::delete('/booking/{id}',    [AdminBooking::class, 'destroy'])->name('booking.destroy');

    // Broadcast Pengumuman Global
    Route::get('/pengumuman',         [AdminPengumuman::class, 'index'])->name('pengumuman.index');
    Route::post('/pengumuman',        [AdminPengumuman::class, 'store'])->name('pengumuman.store');
    Route::delete('/pengumuman/{id}', [AdminPengumuman::class, 'destroy'])->name('pengumuman.destroy');
});

// ── CHATBOT AI ────────────────────────────────────────────────────────
Route::post('/chatbot/chat',      [ChatbotController::class, 'chat'])->name('chatbot.chat');
Route::post('/chatbot/clear',     [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
Route::post('/chatbot/recommend', [ChatbotController::class, 'recommendDance'])->name('chatbot.recommend');

// Helper untuk membuat symbolic link storage di live server
Route::get('/link-storage', function () {
    try {
        // Hapus folder/symlink storage yang mungkin sudah ada (biasanya rusak atau folder kosong)
        $publicStoragePath = public_path('storage');
        if (file_exists($publicStoragePath) || is_link($publicStoragePath)) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Windows
                if (is_link($publicStoragePath)) {
                    unlink($publicStoragePath);
                } else {
                    rmdir($publicStoragePath);
                }
            } else {
                // Linux / Unix
                exec('rm -rf ' . escapeshellarg($publicStoragePath));
            }
        }

        $result = \Illuminate\Support\Facades\Artisan::call('storage:link');
        return "Storage link created successfully!<br>Result Code: " . $result . "<br>Output: " . \Illuminate\Support\Facades\Artisan::output();
    } catch (\Exception $e) {
        return "Failed to link storage: " . $e->getMessage() . "<br><br><b>Catatan:</b> Silakan hapus folder/file bernama 'storage' di dalam direktori 'public' hosting Anda secara manual melalui cPanel File Manager, lalu coba akses kembali halaman ini.";
    }
});

// Fallback route jika symbolic link dinonaktifkan oleh penyedia hosting (Permission Denied)
Route::get('/storage/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    
    // Cegah directory traversal attacks untuk keamanan
    if (strpos($path, '..') !== false) {
        abort(404);
    }
    
    if (!file_exists($fullPath)) {
        abort(404);
    }
    
    return response()->file($fullPath);
})->where('path', '.*');


