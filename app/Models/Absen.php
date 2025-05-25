<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absen extends Model
{
    use HasFactory;

    protected $table = 'absens';

    protected $fillable = [
        'id_kelas',
        'id_santri',
        'nisn',
        'tgl_absen',
        'status',
    ];

    protected $casts = [
        'tgl_absen' => 'date',
    ];

    // Relasi ke Santri
    public function santri()
    {
        return $this->belongsTo(Santri::class, 'id_santri');
    }

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    // Optional: accessor untuk teks status
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            1 => 'Hadir',
            2 => 'Sakit',
            3 => 'Izin',
            4 => 'Alfa',
            default => 'Tidak diketahui',
        };
    }
}
