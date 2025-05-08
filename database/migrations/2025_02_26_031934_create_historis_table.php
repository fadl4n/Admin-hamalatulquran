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
        Schema::create('historis', function (Blueprint $table) {
            $table->id('id_histori');
            $table->unsignedBigInteger('id_setoran')->nullable();
            $table->unsignedBigInteger('id_target')->nullable();
            $table->unsignedBigInteger('id_santri');
            $table->unsignedBigInteger('id_surat');
            $table->unsignedBigInteger('id_kelas');
            $table->integer('nilai');
            $table->string('status');
            $table->decimal('persentase', 5, 2);


            $table->foreign('id_target')->references('id_target')->on('target')->onDelete('cascade'); //
            $table->foreign('id_setoran')->references('id_setoran')->on('setorans')->onDelete('set null'); //
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade'); //
            $table->foreign('id_surat')->references('id_surat')->on('surats')->onDelete('cascade'); //
            $table->foreign('id_kelas')->references('id_kelas')->on('kelas')->onDelete('cascade'); //

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historis');
    }
};
