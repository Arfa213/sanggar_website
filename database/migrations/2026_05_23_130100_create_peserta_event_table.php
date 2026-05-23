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
        // 1. Tambah kolom harga dan status berbayar di tabel events
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('is_berbayar')->default(false)->after('status');
            $table->integer('harga_tiket')->nullable()->after('is_berbayar');
        });

        // 2. Buat tabel peserta_event untuk orang luar
        Schema::create('peserta_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('nama_peserta');
            $table->string('no_hp');
            $table->string('asal_instansi')->nullable();
            $table->enum('status_pembayaran', ['gratis', 'menunggu_verifikasi', 'lunas', 'ditolak'])->default('gratis');
            $table->string('bukti_transfer')->nullable();
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta_event');
        
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['is_berbayar', 'harga_tiket']);
        });
    }
};
