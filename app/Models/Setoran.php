<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setoran extends Model
{
    use HasFactory;

    protected $table = 'setorans';
    protected $primaryKey = 'id_setoran';
    public $timestamps = true;

    protected $fillable = [
        'id_santri',
        'tgl_setoran',
        'status',
        'id_kelas',
        'keterangan',
        'id_surat',
        'jumlah_ayat_start',
        'jumlah_ayat_end'

    ];

    // Relasi ke Santri
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    // Relasi ke Surat
    public function surat()
    {
        return $this->belongsTo(Surat::class, 'id_surat', 'id_surat');
    }
}
