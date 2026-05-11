<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tarian', function (Blueprint $table) {
            $table->enum('jenis_kegiatan', ['tari', 'gamelan', 'drama', 'srimpi'])
                  ->default('tari')
                  ->after('kategori');
        });
    }
    public function down(): void {
        Schema::table('tarian', function (Blueprint $table) {
            $table->dropColumn('jenis_kegiatan');
        });
    }
};
