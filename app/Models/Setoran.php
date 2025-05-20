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
        'persentase',
        'jumlah_ayat_start',
        'jumlah_ayat_end',
        'nilai',
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

    // Relasi ke Pengajar
    public function pengajar()
    {
        return $this->belongsTo(Pengajar::class, 'id_pengajar');
    }

    // Relasi ke Targets (menggunakan `hasMany` karena satu setoran bisa memiliki banyak target)
    // public function targets()
    // {
    //     return $this->hasMany(Target::class, 'id_target', 'id_target');
    // }
    public function histori()
    {
        return $this->hasOne(Histori::class, 'id_setoran');
    }
    public function target()
    {
        return $this->hasMany(Target::class, 'id_target', 'id_target');
    }


    public function getPersentaseAttribute()
    {
        // Ambil semua target terkait dengan setoran ini
        $target = $this->target;

        if (!$target) {
            return 0; // Jika tidak ada target, persentase 0%
        }

        // Cek semua target dengan id_santri dan id_group yang sama di tabel target
        $matchingtarget = Target::where('id_santri', $target->id_santri)
            ->where('id_group', $target->id_group)
            ->get();

        // Hitung total ayat yang perlu dicapai
        $total_target = 0;
        foreach ($matchingtarget as $target) {
            $jumlah_ayat_target = $target->jumlah_ayat_target;
            $jumlah_ayat_target_awal = $target->jumlah_ayat_target_awal;

            if ($jumlah_ayat_target && $jumlah_ayat_target_awal) {
                // Menghitung jumlah ayat yang perlu dicapai per target
                $total_target += ($jumlah_ayat_target - $jumlah_ayat_target_awal + 1);
            }
        }

        // Jumlahkan ayat yang sudah tercatat dalam tabel setoran
        $ayat_dicapai = 0;
        $totalProgress = 0; // Variabel untuk menghitung total progres

        foreach ($matchingtarget as $target) {
            $setoranData = Setoran::where('id_target', $target->id_target)
                ->selectRaw('SUM(jumlah_ayat_end - jumlah_ayat_start + 1) as total_ayat_dicapai')
                ->first();

            if ($setoranData && $setoranData->total_ayat_dicapai) {
                $ayat_dicapai += $setoranData->total_ayat_dicapai;

                // Tentukan progres untuk target ini (dari 0 sampai 1)
                $targetTotalAyat = $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1;
                $targetProgress = $setoranData->total_ayat_dicapai / $targetTotalAyat;

                // Jumlahkan progres untuk target ini
                $totalProgress += $targetProgress;
            }
        }

        // Hitung persentase berdasarkan total progres
        $persentase = 0;
        $totalTargets = $matchingtarget->count();

        if ($totalTargets > 0) {
            // Persentase dihitung berdasarkan total progres
            $persentase = ($totalProgress / $totalTargets) * 100;
        }

        // Pastikan persentase tidak lebih dari 100%
        $persentase = min(100, $persentase);

        return round($persentase); // Dibulatkan ke angka bulat terdekat
    }
}
