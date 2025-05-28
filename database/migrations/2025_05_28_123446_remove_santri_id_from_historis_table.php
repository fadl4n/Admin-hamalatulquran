<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('historis', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu
            $table->dropForeign(['santri_id']);

            // Baru hapus kolom
            $table->dropColumn('santri_id');
        });
    }

    public function down(): void
    {
        Schema::table('historis', function (Blueprint $table) {
            // Tambah kembali kolom dan foreign key-nya (jika ingin rollback)
            $table->unsignedBigInteger('santri_id')->nullable();

            $table->foreign('santri_id')->references('id_santri')->on('santris')->onDelete('cascade');
        });
    }
};
