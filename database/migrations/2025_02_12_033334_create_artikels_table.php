<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('artikels', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('gambar'); // Gunakan string jika menyimpan path gambar
            $table->timestamps();
            $table->timestamp('expired_at')->nullable(); // Bisa dihapus otomatis dengan scheduler
        });
    }

    public function down()
    {
        Schema::dropIfExists('artikels');
    }
};
