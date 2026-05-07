<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    ProfileController,
    EventController,
    DigitalArchiveController,
    DashboardController,
    PenjadwalanController,
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
};

// ── PUBLIC ────────────────────────────────────────────────────
Route::get('/',               [HomeController::class,           'index'])->name('home');
Route::get('/profile',        [ProfileController::class,        'index'])->name('profile');
Route::get('/event',          [EventController::class,          'index'])->name('event');
Route::get('/digital-archive',[DigitalArchiveController::class, 'index'])->name('digital-archive');
Route::get('/galeri/{seksi?}', [App\Http\Controllers\GaleriController::class, 'frontendIndex'])->name('galeri.frontend.index');

// ── AUTH ──────────────────────────────────────────────────────
Route::get('/login',    [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register',[AuthController::class, 'register'])->name('register.post');
Route::post('/logout',  [AuthController::class, 'logout'])->name('logout');

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
    
    // Member Profile
    Route::get('/my-profile',                 [DashboardController::class, 'editProfile'])->name('member.profile');
    Route::post('/my-profile/update',         [DashboardController::class, 'updateProfile'])->name('member.profile.update');
});

// ── CHATBOT (public — semua bisa akses) ───────────────────────
Route::post('/chatbot/chat', [App\Http\Controllers\ChatbotController::class, 'chat'])->name('chatbot.chat');
Route::post('/chatbot/clear', [App\Http\Controllers\ChatbotController::class, 'clearHistory'])->name('chatbot.clear');

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

    // Anggota
    Route::get('/anggota/pdf',          [AdminAnggota::class, 'downloadPdf'])->name('anggota.pdf');
    Route::get('/anggota',              [AdminAnggota::class, 'index'])->name('anggota.index');
    Route::get('/anggota/create',       [AdminAnggota::class, 'create'])->name('anggota.create');
    Route::post('/anggota',             [AdminAnggota::class, 'store'])->name('anggota.store');
    Route::get('/anggota/{id}/edit',    [AdminAnggota::class, 'edit'])->name('anggota.edit');
    Route::put('/anggota/{id}',         [AdminAnggota::class, 'update'])->name('anggota.update');
    Route::delete('/anggota/{id}/delete',[AdminAnggota::class, 'destroy'])->name('anggota.destroy');
    Route::patch('/anggota/{id}/toggle', [AdminAnggota::class, 'toggleStatus'])->name('anggota.toggle');

    // Galeri
    Route::get('/galeri',              [AdminGaleri::class, 'index'])->name('galeri.index');
    Route::get('/galeri/create',       [AdminGaleri::class, 'create'])->name('galeri.create');
    Route::post('/galeri',             [AdminGaleri::class, 'store'])->name('galeri.store');
    Route::get('/galeri/{id}/edit',    [AdminGaleri::class, 'edit'])->name('galeri.edit');
    Route::put('/galeri/{id}',         [AdminGaleri::class, 'update'])->name('galeri.update');
    Route::delete('/galeri/{id}',      [AdminGaleri::class, 'destroy'])->name('galeri.destroy');

    // Kehadiran
    Route::get('/kehadiran',           [AdminKehadiran::class, 'index'])->name('kehadiran.index');
    Route::post('/kehadiran/input',    [AdminKehadiran::class, 'inputKehadiran'])->name('kehadiran.input');
    Route::post('/kehadiran/simpan',   [AdminKehadiran::class, 'simpanKehadiran'])->name('kehadiran.simpan');
    Route::get('/kehadiran/laporan',   [AdminKehadiran::class, 'laporan'])->name('kehadiran.laporan');
});
