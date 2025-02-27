<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Histori extends Model
{
    use HasFactory;

    protected $table = 'historis';
    protected $primaryKey = 'id_histori';
    public $timestamps = true;

    protected $fillable = [
        'status',
        'id_setoran',
        'id_target',
        'id_santri',
        'id_surat',
        'id_kelas',
        'nilai',
        'persentase',


    ];

    // Relasi ke Target
    public function target()
    {
        return $this->belongsTo(Target::class, 'id_target', 'id_target');
    }

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
    public function setoran()
    {
        return $this->belongsTo(Kelas::class, 'id_setoran', 'id_setoran');
    }

}
