<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('tipe_anggota', ['anggota_tetap', 'pengunjung', 'private'])
                  ->default('anggota_tetap')
                  ->after('role');
            $table->date('tanggal_keluar')->nullable()->after('tipe_anggota');
            $table->string('catatan_keanggotaan')->nullable()->after('tanggal_keluar');
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['tipe_anggota', 'tanggal_keluar', 'catatan_keanggotaan']);
        });
    }
};
