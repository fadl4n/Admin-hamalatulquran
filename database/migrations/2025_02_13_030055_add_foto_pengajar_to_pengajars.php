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
        Schema::table('pengajars', function (Blueprint $table) {
            $table->string('foto_pengajar')->nullable()->after('nama');
            $table->string('tempat_lahir')->after('foto_pengajar'); // Tambah kolom tempat lahir setelah nama
            $table->date('tgl_lahir')->nullable()->after('tempat_lahir'); // Tambah kolom tanggal lahir setelah tempat lahir
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajars', function (Blueprint $table) {
            $table->dropColumn(['foto_pengajar',
            'tempat_lahir', 'tgl_lahir']);
        });
    }
};
