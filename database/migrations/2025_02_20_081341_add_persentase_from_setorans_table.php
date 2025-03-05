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
        Schema::table('setorans', function (Blueprint $table) {
            // Menambahkan kolom persentase (jika perlu)
            $table->decimal('persentase', 5, 2)->nullable()->after('keterangan');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('setorans', function (Blueprint $table) {
            // Menghapus kolom persentase jika rollback dilakukan
            $table->dropColumn('persentase');
        });
    }
};
