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
        Schema::create('target', function (Blueprint $table) {
            $table->id('id_target');
            $table->unsignedBigInteger('id_santri');
            $table->date('tgl_mulai');
            $table->date('tgl_target');
            $table->unsignedBigInteger('id_kelas');
            $table->unsignedBigInteger('id_group')->nullable();
            $table->unsignedBigInteger('id_surat');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade');
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade');
            $table->foreign('id_surat')->references('id_surat')->on('surats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target');
    }
};
