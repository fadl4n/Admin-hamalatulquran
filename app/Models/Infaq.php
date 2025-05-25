<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Infaq extends Model
{
     use HasFactory;

    protected $table = 'infaqs';

    protected $fillable = [
        'id_kelas',
        'tgl_infaq',
        'nominal_infaq',
    ];

    protected $casts = [
        'tgl_infaq' => 'date',
    ];

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
