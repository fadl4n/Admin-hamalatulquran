<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keluarga extends Model
{
    use HasFactory;

    protected $table = 'keluargas';
    protected $primaryKey = 'id'; // Asumsikan 'id' adalah primary key
    public $timestamps = false;

    protected $fillable = [
        'nama', 'pekerjaan', 'pendidikan', 'hubungan', 'no_telp',
        'id_santri', 'alamat', 'email', 'tempat_lahir', 'tgl_lahir','status',
    ];

    /**
     * Mendapatkan teks hubungan (Ayah, Ibu, Wali).
     */
    public function getHubunganTextAttribute()
    {
        $hubungan = [
            1 => 'Ayah',
            2 => 'Ibu',
            3 => 'Wali'
        ];

        return $hubungan[$this->hubungan] ?? 'Tidak Diketahui';
    }

    /**
     * Relasi Many-to-One dengan model Santri.
     */
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri', 'id_santri');
    }
}
