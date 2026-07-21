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
        if (!Schema::hasColumn('users', 'tgl_kadaluarsa')) {
            Schema::table('users', function (Blueprint $table) {
                $table->date('tgl_kadaluarsa')->nullable()->after('tipe_anggota');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tgl_kadaluarsa');
        });
    }
};
