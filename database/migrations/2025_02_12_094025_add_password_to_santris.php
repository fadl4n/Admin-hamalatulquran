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
        Schema::table('santris', function (Blueprint $table) {
            $table->string('tempat_lahir')->after('nama');
            $table->string('password')->nullable()->after('tempat_lahir'); // Password bisa kosong
            $table->string('foto_santri')->nullable()->after('password'); // Foto santri bisa kosong

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn(['tempat_lahir', 'password','foto_santri']);
        });
    }
};
