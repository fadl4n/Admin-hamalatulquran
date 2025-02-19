<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setoran;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Surat;

class HistoriController extends Controller
{
    /**
     * Tampilkan halaman histori dengan filter santri.
     */
    public function index(Request $request)
    {
        $santris = Santri::select('id_santri', 'nama','nisn')->get();
        return view('histori.show', compact('santris'));
    }

    /**
     * Ambil data histori dengan filter.
     */
    public function fnGetData(Request $request)
{
    $query = Setoran::with(['santri', 'kelas', 'surat'])
        ->where('setorans.status', 1) // Tambahkan filter status selesai
        ->selectRaw('
            setorans.id_santri,
            setorans.id_kelas,
            setorans.id_surat,
            MIN(setorans.jumlah_ayat_start) as ayat_awal,
            MAX(setorans.jumlah_ayat_end) as ayat_akhir
        ')
        ->groupBy('setorans.id_santri', 'setorans.id_kelas', 'setorans.id_surat');

    // Filter berdasarkan santri jika ada
    if ($request->has('id_santri') && !empty($request->id_santri)) {
        $query->where('setorans.id_santri', $request->id_santri);
    }

    return datatables()->eloquent($query)
        ->addColumn('nama', function ($setoran) {
            return $setoran->santri->nama ?? '-';
        })
        ->addColumn('kelas', function ($setoran) {
            return $setoran->kelas->nama_kelas ?? '-';
        })
        ->addColumn('Juz', function ($setoran) {
            return $setoran->surat->juz ?? '-';
        })
        ->addColumn('nama_surat', function ($setoran) {
            return $setoran->surat->nama_surat ?? '-';
        })
        ->addColumn('jumlah_ayat', function ($setoran) {
            return $setoran->ayat_awal . ' - ' . $setoran->ayat_akhir;
        })
        ->make(true);
}

}
