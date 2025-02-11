<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('santris', function (Blueprint $table) {
            $table->id('id_santri');
            $table->string('nama');
            $table->integer('nisn')->unique();
            $table->date('tgl_lahir');
            $table->string('alamat');
            $table->string('angkatan');
            $table->unsignedBigInteger('id_kelas'); // Perbaikan: Sesuai dengan id kelas
            $table->foreign('id_kelas')->references('id')->on('kelas')->onDelete('set null'); // Tambah foreign key
            $table->integer('jenis_kelamin'); // 1 = Laki-laki, 2 = Perempuan
            $table->string('email');
            $table->integer('status'); // 1 = Aktif, 0 = Nonaktif
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};
