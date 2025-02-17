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
        Schema::create('pengajar', function (Blueprint $table) {
            $table->id('id_pengajar'); // Primary Key
            $table->string('nama'); // Nama harus string
            $table->string('nip'); // NIP sebaiknya unik
            $table->string('email'); // Email harus string dan unik
            $table->string('no_telp'); // Nomor telepon seharusnya string
            $table->string('jenis_kelamin')->nullable(); // Jenis kelamin bisa kosong
            $table->string('alamat'); // Alamat string
            $table->string('password')->nullable(); // Password bisa kosong
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajar');
    }
};
