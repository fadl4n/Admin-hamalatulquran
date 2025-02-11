<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluargas'; // Nama tabel

    protected $fillable = [
        'nama',
        'pekerjaan',
        'pendidikan',
        'no_telp',
        'id_santri',
        'alamat',
        'email',
        'password'
    ];

    // Relasi ke tabel santris
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }
}
