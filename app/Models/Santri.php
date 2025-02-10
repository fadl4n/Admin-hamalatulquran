<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Santri extends Model
{
    use HasFactory;

    protected $table = 'santris'; // Sesuai dengan nama tabel di database
    protected $primaryKey = 'id_santri';
    public $timestamps = false; // Jika tabel tidak memiliki created_at dan updated_at

    protected $fillable = [
        'nama', 'nisn', 'tgl_lahir', 'alamat',
        'angkatan', 'id_kelas', 'jenis_kelamin',
        'email', 'status'
    ];
}
