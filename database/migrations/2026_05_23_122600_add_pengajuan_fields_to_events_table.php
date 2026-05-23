<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, ENUM modification requires raw SQL if using Doctrine DBAL < 3, 
        // but since Laravel 10+ uses native schema operations, let's try direct modify if possible.
        // Or we can just add a new column for is_external and change status.
        // Actually, it's safer to use raw SQL for enum modification in Laravel if using mysql.

        $driver = config('database.default');

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE events MODIFY COLUMN status ENUM('akan_datang', 'selesai', 'pending_approval') DEFAULT 'akan_datang'");
        }

        Schema::table('events', function (Blueprint $table) {
            if (config('database.default') !== 'mysql') {
                // SQLite doesn't support altering ENUM easily, so we add a string column or just ignore the enum check on SQLite testing
            }
            
            $table->boolean('is_external')->default(false)->after('unggulan');
            $table->string('nama_pengaju')->nullable()->after('is_external');
            $table->string('no_hp_pengaju')->nullable()->after('nama_pengaju');
            $table->string('portofolio_link')->nullable()->after('no_hp_pengaju');
            $table->text('catatan_pengaju')->nullable()->after('portofolio_link');
            
            // Allow kategori to accept 'workshop'
            // We'll also need to update the kategori ENUM
        });

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE events MODIFY COLUMN kategori ENUM('internasional','nasional','festival','pentas','kompetisi', 'workshop', 'kelas_khusus') DEFAULT 'pentas'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn([
                'is_external', 
                'nama_pengaju', 
                'no_hp_pengaju', 
                'portofolio_link', 
                'catatan_pengaju'
            ]);
        });
    }
};
