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
        'nama', 'nisn', 'tempat_lahir', 'password', 'foto_santri','email',
        'tgl_lahir', 'alamat', 'angkatan', 'id_kelas', 'jenis_kelamin', 'status'
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

}
