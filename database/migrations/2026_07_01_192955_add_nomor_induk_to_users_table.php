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
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_induk')->unique()->nullable()->after('id');
        });

        // Generate nomor induk untuk anggota lama
        $users = \App\Models\User::where('role', 'anggota')
            ->orderBy('created_at', 'asc')
            ->get();

        $counters = [];

        foreach ($users as $user) {
            $datePrefix = $user->created_at->format('ymd');
            if (!isset($counters[$datePrefix])) {
                $counters[$datePrefix] = 1;
            } else {
                $counters[$datePrefix]++;
            }

            $sequence = str_pad($counters[$datePrefix], 3, '0', STR_PAD_LEFT);
            // Disable events temporary using DB facade or just saving without firing events if needed
            // However, we didn't add the generating boot event yet, so it's safe to use $user->save()
            $user->nomor_induk = $datePrefix . $sequence;
            $user->save();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('nomor_induk');
        });
    }
};
