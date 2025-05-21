<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropSantriIdFromHistorisTable extends Migration
{
    public function up()
    {
        Schema::table('historis', function (Blueprint $table) {
            // Drop foreign key constraint dulu (nama index biasanya seperti tabel_kolom_foreign)
            $table->dropForeign(['santri_id']); // sesuaikan nama kolomnya, misal 'santri_id' atau 'id_santri'

            // Baru drop kolom
            $table->dropColumn('santri_id');
        });
    }

    public function down()
    {
        Schema::table('historis', function (Blueprint $table) {
            // Tambahkan kembali kolom (sesuaikan tipe data dan nullable jika perlu)
            $table->unsignedBigInteger('santri_id')->nullable();

            // Tambahkan kembali foreign key jika rollback
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
        });
    }
}
