<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $table = 'santris';
    protected $primaryKey = 'id_santri';
    public $timestamps = false; // Tambahkan ini untuk menonaktifkan timestamps

    protected $fillable = ['nama', 'nisn', 'tgl_lahir', 'alamat', 'angkatan', 'id_kelas', 'jenis_kelamin', 'status'];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas'); // ✅ Sesuaikan foreign key dan primary key
    }
    public function keluarga()
    {
        return $this->belongsTo(Kelas::class, 'id_santri', 'id_santri'); // ✅ Sesuaikan foreign key dan primary key
    }
}

