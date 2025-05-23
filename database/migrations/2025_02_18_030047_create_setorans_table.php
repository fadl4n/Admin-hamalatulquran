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
        Schema::create('setorans', function (Blueprint $table) {
            $table->id('id_setoran');
            $table->unsignedBigInteger('id_santri');
            $table->date('tgl_setoran');
            $table->integer('status');
            $table->unsignedBigInteger('id_kelas');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('id_surat')->nullable();
            $table->unsignedBigInteger('id_target');
            $table->unsignedBigInteger('id_pengajar')->nullable();
            $table->integer('jumlah_ayat_start');
            $table->integer('jumlah_ayat_end'); // Menambahkan kolom id_pengajar
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_surat')->references('id_surat')->on('surats')->onDelete('set null');
            $table->foreign('id_pengajar')->references('id_pengajar')->on('pengajars')->onDelete('cascade');
            $table->foreign('id_target')->references('id_target')->on('target')->onDelete('cascade'); //
        });
    }

    public function down()
    {
        Schema::dropIfExists('setorans');
    }
};
