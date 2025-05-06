<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Target extends Model
{
    use HasFactory;

    protected $table = 'targets';
    protected $primaryKey = 'id_target';
    public $timestamps = false;

    protected $fillable = [
        'id_santri',
        'tgl_mulai',
        'tgl_target',
        'id_kelas',
        'id_group',
        'jumlah_ayat_target',
        'jumlah_ayat_target_awal',
        'id_surat',
        'id_pengajar',

    ];

    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id_kelas');
    }

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'id_surat', 'id_surat');
    }

    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class, 'id_pengajar');
    }
    // Model Target
    public function setoran()
    {
        return $this->hasMany(Setoran::class, 'id_target');
    }
}
