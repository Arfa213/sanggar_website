<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('kehadiran', function (Blueprint $table) {
            $table->string('barcode_token', 64)->nullable()->after('dicatat_oleh');
            $table->timestamp('scan_at')->nullable()->after('barcode_token');
            $table->enum('metode_absen', ['manual', 'barcode'])->default('manual')->after('scan_at');
        });
    }
    public function down(): void {
        Schema::table('kehadiran', function (Blueprint $table) {
            $table->dropColumn(['barcode_token', 'scan_at', 'metode_absen']);
        });
    }
};
