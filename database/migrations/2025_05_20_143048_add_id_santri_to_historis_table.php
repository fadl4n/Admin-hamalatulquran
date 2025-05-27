<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdSantriToHistorisTable extends Migration
{
    public function up()
    {
        Schema::table('historis', function (Blueprint $table) {
            if (!Schema::hasColumn('historis', 'id_santri')) {
                $table->unsignedBigInteger('id_santri')->nullable()->after('id_histori');

                $table->foreign('id_santri')
                    ->references('id_santri') // sesuaikan ini juga, kalau di tabel santris nama PK-nya 'id' maka ganti jadi ->references('id')
                    ->on('santris')
                    ->onDelete('cascade');
            }
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
