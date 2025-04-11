<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\kelas;
use App\Models\Santri;

class KelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $kelas = Kelas::all(['id_kelas', 'nama_kelas']); // pilih field yang dibutuhkan aja

    return response()->json([
        'data' => $kelas
    ]);
}

public function getSantriByKelas($id)
{
    $santri = Santri::where('id_kelas', $id)->get(['id_santri', 'nama']);

    if ($santri->isEmpty()) {
        return response()->json([
            'message' => 'Tidak ada santri dalam kelas ini.'
        ], 404);
    }

    return response()->json([
        'kelas_id' => $id,
        'santri' => $santri
    ]);
}

}
