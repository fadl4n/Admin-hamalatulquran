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
        Schema::table('targets', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pengajar');

            $table->foreign('id_pengajar')->references('id_pengajar')->on('pengajars')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('targets', function (Blueprint $table) {
            $table->dropForeign(['id_pengajar']); // Hapus foreign key
            $table->dropColumn('id_pengajar'); // Hapus kolom
        });
    }
};
