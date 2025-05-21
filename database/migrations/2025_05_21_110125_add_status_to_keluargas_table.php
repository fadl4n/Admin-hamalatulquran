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
    Schema::table('keluargas', function (Blueprint $table) {
        $table->integer('status')->nullable(); // tambahkan kolom status dengan default 0
    });
}

public function down()
{
    Schema::table('keluargas', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
