<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pengajar extends Model
{
    use HasFactory;

    protected $table = 'pengajars'; // Sesuai dengan nama tabel di database
    protected $primaryKey = 'id_pengajar';
    public $timestamps = false; // Jika tabel tidak memiliki created_at dan updated_at

    protected $fillable = [
        'nama', 'nip', 'email', 'alamat',
        'no_telp', 'password', 'jenis_kelamin',
    ];
}
