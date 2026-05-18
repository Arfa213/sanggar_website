<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mengecek apakah tabel sudah ada agar tidak error jika dijalankan di database 
        // yang tabel pengunjungnya sudah dibuat secara manual (lewat phpMyAdmin dll)
        if (!Schema::hasTable('pengunjung')) {
            Schema::create('pengunjung', function (Blueprint $table) {
                $table->id();
                $table->string('nama');
                $table->string('no_hp')->nullable();
                // Kolom instansi tidak perlu ditambahkan di sini karena sudah ada di migration terpisah
                // (2026_05_14_030102_add_instansi_to_pengunjung_table.php)
                $table->text('tujuan');
                $table->date('tanggal');
                $table->time('jam');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengunjung');
    }
};
