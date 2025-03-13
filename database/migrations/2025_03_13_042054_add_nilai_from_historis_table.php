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
        Schema::table('historis', function (Blueprint $table) {
            $table->integer('nilai')->nullable()->after('persentase'); // Perbaikan nama kolom dan penggunaan nullable()
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historis', function (Blueprint $table) {
            $table->dropColumn('nilai'); // Menghapus kolom nilai jika rollback
        });
    }
};
