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
        Schema::create('keluarga', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('nama');
            $table->string('pekerjaan');
            $table->string('pendidikan');
            $table->string('no_telp');
            $table->unsignedBigInteger('id_santri')->nullable(); // Foreign key
            $table->string('alamat');
            $table->string('email')->unique();
            $table->string('password')->nullable(); // Password bisa kosong
            $table->timestamps();

            // Foreign Key Constraint (mengacu ke id_santri di tabel santris)
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga');
    }
};
