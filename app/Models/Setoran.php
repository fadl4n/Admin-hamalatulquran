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
        'id_pengajar',
        'id_target',
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
    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class, 'id_pengajar');
    }
   // Model Setoran
  // Di model Santri
public function targets()
{
    return $this->hasMany(Target::class, 'santri_id');
}

}
