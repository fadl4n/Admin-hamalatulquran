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
    public function targets()
    {
        return $this->hasMany(Target::class, 'id_target', 'id_target');
    }

    public function getPersentaseAttribute()
    {
        // Ambil semua target terkait dengan setoran ini
        $targets = $this->targets;

        if ($targets->isEmpty()) {
            return 0; // Jika tidak ada target, persentase 0%
        }

        // Totalkan jumlah ayat dari semua target
        $total_target = 0;
        $ayat_dicapai = 0;

        foreach ($targets as $target) {
            $jumlah_ayat_target = $target->jumlah_ayat_target;
            $jumlah_ayat_target_awal = $target->jumlah_ayat_target_awal;

            if ($jumlah_ayat_target && $jumlah_ayat_target_awal) {
                $total_target += ($jumlah_ayat_target - $jumlah_ayat_target_awal + 1);
            }
        }

        // Ambil jumlah ayat start terkecil dan jumlah ayat end terbesar dari tabel setorans dengan id_target yang sama
        $setoranData = Setoran::whereIn('id_target', $targets->pluck('id_target'))
            ->selectRaw('MIN(jumlah_ayat_start) as min_ayat, MAX(jumlah_ayat_end) as max_ayat')
            ->first();

        if ($setoranData && $setoranData->min_ayat && $setoranData->max_ayat) {
            $ayat_dicapai = $setoranData->max_ayat - $setoranData->min_ayat + 1;
        }

        // Hitung persentase
        if ($total_target > 0) {
            $persentase = ($ayat_dicapai / $total_target) * 100;
        } else {
            return 0; // Jika tidak ada target ayat yang valid
        }

        // Periksa apakah status harus berubah
        if ($persentase >= 100) {
            // Ubah status menjadi 'Selesai'
            $this->status = 1;
            $this->save();
        } elseif ($persentase > 0 && $persentase < 100) {
            // Status tetap 'Proses' jika persentase antara 0% dan 100%
            $this->status = 0;
            $this->save();
        }

        return round($persentase); // Dibulatkan ke angka bulat terdekat
    }

}
