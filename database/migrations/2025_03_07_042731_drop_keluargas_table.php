<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('keluargas');
    }

    public function down(): void
    {
        // Jika ingin bisa rollback, buat ulang tabelnya
        Schema::create('keluargas', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->nullable();
            $table->string('pekerjaan')->nullable();
            $table->string('pendidikan')->nullable();
            $table->string('no_telp')->nullable();
            $table->unsignedBigInteger('id_santri')->nullable();
            $table->string('alamat')->nullable();
            $table->string('email')->nullable();
            $table->integer('hubungan')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->timestamps();

            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('set null');
        });
    }
};
