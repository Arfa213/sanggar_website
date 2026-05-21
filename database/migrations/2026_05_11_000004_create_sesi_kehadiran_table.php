<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Menggantikan sesi_kehadiran: QR permanen per kelas (jadwal+tarian combo)
return new class extends Migration {
    public function up(): void {
        Schema::create('kelas_barcode', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->nullable()->constrained('jadwal_latihan')->onDelete('cascade');
            $table->foreignId('tarian_id')->nullable()->constrained('tarian')->onDelete('cascade');
            $table->string('barcode_token', 64)->unique(); 
            $table->boolean('aktif')->default(true);
            $table->string('dibuat_oleh')->nullable();
            $table->timestamps();

            // QR permanen sekarang unik per tarian saja (jika jadwal tidak diisi)
            $table->unique('tarian_id', 'tarian_barcode_unique');
        });
    }
    public function down(): void {
        Schema::dropIfExists('kelas_barcode');
    }
};
