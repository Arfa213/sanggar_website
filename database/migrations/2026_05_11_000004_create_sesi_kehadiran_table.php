<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sesi_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained('jadwal_latihan')->onDelete('cascade');
            $table->foreignId('tarian_id')->constrained('tarian')->onDelete('cascade');
            $table->date('tanggal');
            $table->string('barcode_token', 64)->unique();
            $table->boolean('aktif')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->string('dibuat_oleh')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sesi_kehadiran');
    }
};
