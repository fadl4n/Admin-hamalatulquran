<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdSantriToHistorisTable extends Migration
{
    public function up()
    {
        Schema::table('historis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_santri')->after('id_histori')->nullable(); // Tambahkan kolom

            // Tambahkan foreign key (pastikan tabel 'santri' punya kolom 'id_santri' sebagai PRIMARY KEY)
            $table->foreign('id_santri')->references('id_santri')->on('santris')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('historis', function (Blueprint $table) {
            $table->dropForeign(['id_santri']);
            $table->dropColumn('id_santri');
        });
    }
}
