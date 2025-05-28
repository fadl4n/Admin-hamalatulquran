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
        // HISTORIS
        Schema::table('historis', function (Blueprint $table) {
            $table->dropForeign(['id_santri']);
            $table->renameColumn('id_santri', 'santri_id');
        });
        Schema::table('historis', function (Blueprint $table) {
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
        });

        // KELUARGAS
        Schema::table('keluargas', function (Blueprint $table) {
            $table->dropForeign(['id_santri']);
            $table->renameColumn('id_santri', 'santri_id');
        });
        Schema::table('keluargas', function (Blueprint $table) {
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
        });

        // SETORANS
        Schema::table('setorans', function (Blueprint $table) {
            $table->dropForeign(['id_santri']);
            $table->renameColumn('id_santri', 'santri_id');
        });
        Schema::table('setorans', function (Blueprint $table) {
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
        });

        // TARGETS
        Schema::table('targets', function (Blueprint $table) {
            $table->dropForeign(['id_santri']);
            $table->renameColumn('id_santri', 'santri_id');
        });
        Schema::table('targets', function (Blueprint $table) {
            $table->foreign('santri_id')->references('id')->on('santris')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse for all tables

        // HISTORIS
        Schema::table('historis', function (Blueprint $table) {
            $table->dropForeign(['santri_id']);
            $table->renameColumn('santri_id', 'id_santri');
        });
        Schema::table('historis', function (Blueprint $table) {
            $table->foreign('id_santri')->references('id')->on('santris')->onDelete('cascade');
        });

        // KELUARGAS
        Schema::table('keluargas', function (Blueprint $table) {
            $table->dropForeign(['santri_id']);
            $table->renameColumn('santri_id', 'id_santri');
        });
        Schema::table('keluargas', function (Blueprint $table) {
            $table->foreign('id_santri')->references('id')->on('santris')->onDelete('cascade');
        });

        // SETORANS
        Schema::table('setorans', function (Blueprint $table) {
            $table->dropForeign(['santri_id']);
            $table->renameColumn('santri_id', 'id_santri');
        });
        Schema::table('setorans', function (Blueprint $table) {
            $table->foreign('id_santri')->references('id')->on('santris')->onDelete('cascade');
        });

        // TARGETS
        Schema::table('targets', function (Blueprint $table) {
            $table->dropForeign(['santri_id']);
            $table->renameColumn('santri_id', 'id_santri');
        });
        Schema::table('targets', function (Blueprint $table) {
            $table->foreign('id_santri')->references('id')->on('santris')->onDelete('cascade');
        });
    }
};
