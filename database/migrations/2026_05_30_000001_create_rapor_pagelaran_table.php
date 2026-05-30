<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rapor_pagelaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tarian_id')->constrained('tarian')->onDelete('cascade');
            $table->foreignId('pelatih_id')->constrained('users'); // admin penilai

            $table->tinyInteger('nilai_teknik');       // 0-100
            $table->tinyInteger('nilai_hafalan');      // 0-100
            $table->tinyInteger('nilai_ekspresi');     // 0-100
            $table->tinyInteger('nilai_penampilan');   // 0-100

            $table->decimal('nilai_kehadiran', 5, 2); // auto-hitung (%)
            $table->decimal('nilai_akhir', 5, 2);      // weighted average
            $table->string('predikat');                // Istimewa, Sangat Baik, dll
            $table->boolean('lulus')->default(true);
            $table->text('catatan')->nullable();
            $table->boolean('notif_terkirim')->default(false);
            $table->timestamps();

            $table->unique(['event_id', 'user_id', 'tarian_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rapor_pagelaran');
    }
};
