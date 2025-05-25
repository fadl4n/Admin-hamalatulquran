<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $table = 'santris';
    protected $primaryKey = 'id_santri';
    public $timestamps = false;

    protected $fillable = [
        'nama',
        'nisn',
        'tgl_lahir',
        'tempat_lahir',
        'email',
        'password',
        'foto_santri',
        'alamat',
        'angkatan',
        'id_kelas',
        'jenis_kelamin',
        'status'
    ];

    /**
     * Relasi Many-to-One dengan model Kelas.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    /**
     * Relasi One-to-Many dengan model Keluarga.
     */
    public function keluarga()
    {
        return $this->hasMany(Keluarga::class, 'id_santri', 'id_santri');
    }
    public function targets()
    {
        return $this->hasMany(Target::class, 'id_santri'); // Relasi ke target berdasarkan id_santri
    }
    public function target()
    {
        return $this->hasMany(Target::class, 'id_santri'); // Relasi ke target berdasarkan id_santri
    }
    public function setoran()
    {
        return $this->hasMany(Setoran::class);
    }

    public function histori()
    {
        return $this->hasMany(Histori::class);
    }
}
