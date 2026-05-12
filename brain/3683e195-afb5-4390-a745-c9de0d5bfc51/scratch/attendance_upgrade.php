<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__ . '/../../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Updating database for 3-Type Attendance System...\n";

try {
    // 1. Update Tabel Users (untuk Anggota Tetap/Sementara)
    Schema::table('users', function (Blueprint $table) {
        if (!Schema::hasColumn('users', 'tipe_anggota')) {
            $table->enum('tipe_anggota', ['tetap', 'sementara'])->default('tetap');
        }
        if (!Schema::hasColumn('users', 'tgl_kadaluarsa')) {
            $table->date('tgl_kadaluarsa')->nullable(); // Khusus Anggota Sementara
        }
    });

    // 2. Buat Tabel Pengunjung (untuk Tamu Umum)
    Schema::dropIfExists('pengunjung');
    Schema::create('pengunjung', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('no_hp')->nullable();
        $table->string('tujuan')->nullable();
        $table->date('tanggal');
        $table->time('jam');
        $table->timestamps();
    });

    echo "Success: Database updated for 3-Type Attendance!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
