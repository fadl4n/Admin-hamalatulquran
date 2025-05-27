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
       Schema::create('absens', function (Blueprint $table) {
    $table->id(); // Primary key
    $table->unsignedBigInteger('id_kelas'); // FK ke tabel kelas
    $table->unsignedBigInteger('id_santri'); // FK ke tabel santris
    $table->string('nisn'); // Diambil dari tabel santris
    $table->date('tgl_absen');
    $table->integer('status'); // 1=hadir, 2=sakit, 3=izin, 4=alfa
    $table->timestamps();

    // Foreign key constraints
    $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
    $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absens');
    }
};
