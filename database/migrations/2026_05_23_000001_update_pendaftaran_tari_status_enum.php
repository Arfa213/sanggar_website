<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah status 'pending' dan 'ditolak' ke enum pendaftaran_tari.status
     * Status lama: aktif, nonaktif, selesai
     * Status baru: pending, aktif, ditolak, selesai
     *
     * 'nonaktif' lama digantikan oleh 'pending' (menunggu konfirmasi) dan 'ditolak' (ditolak admin)
     */
    public function up(): void
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'mysql') {
            return; // Skip raw MySQL statement on SQLite/CI
        }

        // MySQL: ubah kolom enum langsung via raw SQL
        DB::statement("ALTER TABLE pendaftaran_tari MODIFY COLUMN status ENUM('aktif','nonaktif','pending','ditolak','selesai') DEFAULT 'pending'");

        // Migrasi data lama: nonaktif yang belum ada tanggal_latihan lewat → anggap pending
        // Semua 'nonaktif' yang ada sekarang → ubah ke 'pending' (lebih aman)
        DB::statement("UPDATE pendaftaran_tari SET status = 'pending' WHERE status = 'nonaktif'");
    }

    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::connection()->getDriverName() !== 'mysql') {
            return; // Skip raw MySQL statement on SQLite/CI
        }

        // Kembalikan ke enum lama, tapi data 'pending' jadi 'nonaktif' dulu
        DB::statement("UPDATE pendaftaran_tari SET status = 'nonaktif' WHERE status = 'pending'");
        DB::statement("UPDATE pendaftaran_tari SET status = 'nonaktif' WHERE status = 'ditolak'");
        DB::statement("ALTER TABLE pendaftaran_tari MODIFY COLUMN status ENUM('aktif','nonaktif','selesai') DEFAULT 'aktif'");
    }
};
