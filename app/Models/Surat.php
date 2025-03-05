<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    protected $table = 'surats'; // Nama tabel di database
    protected $primaryKey = 'id_surat'; // Primary key tabel

    protected $fillable = [
        'nama_surat',
        'jumlah_ayat',
        'deskripsi',
    ];

    public $timestamps = true; // Menggunakan created_at & updated_at

    public function targets()
    {
        return $this->hasMany(Target::class, 'id_surat');
    }
}
