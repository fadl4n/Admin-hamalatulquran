<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

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
     public function targets()
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
    if ($tglTarget instanceof \Carbon\Carbon) {
        $targetDate = $tglTarget->copy()->setTimezone(config('app.timezone'));
    } else {
        $targetDate = Carbon::parse($tglTarget)->setTimezone(config('app.timezone'));
    }

    $hariIni = now()->setTimezone(config('app.timezone'));

    if ($targetDate->hour === 0 && $targetDate->minute === 0 && $targetDate->second === 0) {
        $targetDate->setTime(23, 59, 59);
    }

    Log::info("Hari ini: " . $hariIni);
    Log::info("Target date: " . $targetDate);
    Log::info("Total ayat disetorkan: $totalAyatDisetorkan, Jumlah ayat target: $jumlahAyatTarget");

    if ($totalAyatDisetorkan >= $jumlahAyatTarget) {
        return 2; // Selesai
    }

    if ($hariIni->gt($targetDate)) {
        return 3; // Terlambat
    }

    return ($totalAyatDisetorkan > 0) ? 1 : 0;
}


}
