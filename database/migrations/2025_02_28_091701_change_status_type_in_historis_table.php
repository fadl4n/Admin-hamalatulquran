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
    Schema::table('historis', function (Blueprint $table) {
        $table->integer('status')->change();  // Mengubah tipe status menjadi integer
    });
}

public function down()
{
    Schema::table('historis', function (Blueprint $table) {
        $table->string('status')->change();  // Mengubah kembali tipe status menjadi string jika rollback
    });
}

};
