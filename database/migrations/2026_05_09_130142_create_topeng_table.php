<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('topeng', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('warna');        // misal: Putih, Merah, Hijau
            $table->string('karakter');     // misal: Lincah, Gagah, Sabar
            $table->text('filosofi')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('topeng');
    }
};
