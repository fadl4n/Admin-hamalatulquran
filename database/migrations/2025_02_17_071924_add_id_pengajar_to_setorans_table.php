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
    Schema::table('setorans', function (Blueprint $table) {
        $table->unsignedBigInteger('id_pengajar')->nullable(); // Menambahkan kolom id_pengajar
        $table->foreign('id_pengajar')->references('id_pengajar')->on('pengajars')->onDelete('set null'); // Menambahkan relasi ke tabel pengajars
    });
}

public function down()
{
    Schema::table('setorans', function (Blueprint $table) {
        $table->dropForeign(['id_pengajar']);
        $table->dropColumn('id_pengajar');
    });
}

};
