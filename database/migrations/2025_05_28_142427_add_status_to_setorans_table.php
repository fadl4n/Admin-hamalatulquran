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
        $table->integer('status')->after('nilai'); // letakkan setelah kolom 'nilai' misalnya
    });
}

public function down()
{
    Schema::table('setorans', function (Blueprint $table) {
        $table->dropColumn('status');
    });
}

};
