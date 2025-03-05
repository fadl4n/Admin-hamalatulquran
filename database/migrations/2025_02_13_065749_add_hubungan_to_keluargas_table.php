<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('keluargas', function (Blueprint $table) {
            $table->integer('hubungan')->after('nama')->nullable();
        });
    }

    public function down()
    {
        Schema::table('keluargas', function (Blueprint $table) {
            $table->dropColumn('hubungan');
        });
    }
};
