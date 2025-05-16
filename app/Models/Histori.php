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
        return $this->belongsTo(Santri::class, 'id_santri');
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

    // Relasi ke Setoran
    public function setoran()
    {
        return $this->belongsTo(Setoran::class, 'id_setoran', 'id_setoran');
    }

    public static function determineStatus($totalAyatDisetorkan, $jumlahAyatTarget, $tglTarget, $tglSetoranTerakhir)
    {
        $hariIni = now()->toDateString(); // Format YYYY-MM-DD

        // Jika target sudah tercapai, status tetap selesai (2)
        if ($totalAyatDisetorkan >= $jumlahAyatTarget) {
            return 2; // Selesai
        }

        // Jika tanggal target sudah terlewati dan belum selesai, status menjadi terlambat (3)
        if ($hariIni > $tglTarget) {
            return 3; // Terlambat
        }

        // Jika ada progres, status tetap proses (1), jika tidak ada progres status tetap belum mulai (0)
        return ($totalAyatDisetorkan > 0) ? 1 : 0;
    }
}
