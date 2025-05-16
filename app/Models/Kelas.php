<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Santri;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id_kelas';
    public $timestamps = false;

    protected $fillable = [
        'id_kelas',
        'nama_kelas'
        // GAK PERLU 'jumlah_santri' lagi, karena kita hitung otomatis
    ];

    public function santri()
    {
        return $this->hasMany(Santri::class, 'id_kelas', 'id_kelas');
    }
}
