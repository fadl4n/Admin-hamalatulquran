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
        Schema::create('keluargas', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->string('nama')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('no_telp')->nullable();
            $table->unsignedBigInteger('id_santri')->nullable(); // Foreign key
            $table->string('alamat')->nullable();
            $table->string('email')->nullable();
            $table->integer('hubungan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
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
        Schema::dropIfExists('keluargas');
    }
};
