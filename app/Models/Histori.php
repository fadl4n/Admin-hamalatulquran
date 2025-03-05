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

    // Relasi ke Setoran
    public function setoran()
    {
        return $this->belongsTo(Setoran::class, 'id_setoran', 'id_setoran');
    }

    // Method untuk menentukan status berdasarkan setoran
    public static function determineStatus($totalAyatDisetorkan, $jumlahAyatTarget, $tgl_target,$persentase)
    {
        $today = now()->toDateString(); // Ambil tanggal hari ini (format: Y-m-d)

        if ($persentase == 0) {
            return 0; // Status belum mulai
        }
        if ($totalAyatDisetorkan >= $jumlahAyatTarget) {
            return 2; // Status selesai
        } elseif ($tgl_target < $today) {
            return 3; // Status terlambat
        } else {
            return 1; // Status proses
        }
    }
}
