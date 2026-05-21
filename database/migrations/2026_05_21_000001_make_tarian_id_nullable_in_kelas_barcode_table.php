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
        Schema::table('kelas_barcode', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['tarian_id']);
        });

        Schema::table('kelas_barcode', function (Blueprint $table) {
            // Make tarian_id nullable
            $table->unsignedBigInteger('tarian_id')->nullable()->change();

            // Re-add the foreign key constraint
            $table->foreign('tarian_id')->references('id')->on('tarian')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas_barcode', function (Blueprint $table) {
            $table->dropForeign(['tarian_id']);
        });

        Schema::table('kelas_barcode', function (Blueprint $table) {
            $table->unsignedBigInteger('tarian_id')->nullable(false)->change();
            $table->foreign('tarian_id')->references('id')->on('tarian')->onDelete('cascade');
        });
    }
};
