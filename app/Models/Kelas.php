<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas'; // Sesuai dengan nama tabel di database
    protected $primaryKey = 'id_kelas';
    public $timestamps = false; // Jika tabel tidak memiliki created_at dan updated_at

    protected $fillable = [
        'id_kelas','nama_kelas','jumlah_santri'
    ];

    public function santri()
    {
        return $this->hasMany(Santri::class, 'id_kelas', 'id_kelas'); // ✅ Sesuaikan foreign key dan primary key
    }
    public function updateJumlahSantri()
    {
        $this->jumlah_santri = $this->santri()->count();
        $this->save();
    }
}
