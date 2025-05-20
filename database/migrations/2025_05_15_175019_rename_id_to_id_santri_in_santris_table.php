<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameIdToIdSantriInSantrisTable extends Migration
{
    public function up()
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->renameColumn('id', 'id_santri');
        });
    }

    public function down()
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->renameColumn('id_santri', 'id');
        });
    }
}
