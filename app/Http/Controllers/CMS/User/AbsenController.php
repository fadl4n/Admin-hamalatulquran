<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;


class AbsenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index(Request $request)
{
    if ($request->ajax()) {
        $data = Kelas::select('id_kelas', 'nama_kelas')
            ->withCount('santri');

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('jumlah_santri', fn($row) => $row->santri_count)
            ->addColumn('action', function ($row) {
                return '<a href="'.route('absen.detail', $row->id_kelas).'" class="btn btn-info btn-sm" title="Lihat Detail"><i class="fas fa-eye"></i></a>';
            })
          ->filterColumn('jumlah_santri', function($query, $keyword) {
    if (is_numeric($keyword)) {
        $query->havingRaw("santri_count = ?", [(int)$keyword]);
    } else {
        // Bisa tambahkan fallback misal ignore filter kalau bukan angka
    }
})


            ->filterColumn('nama_kelas', function($query, $keyword) {
                $query->whereRaw("LOWER(nama_kelas) LIKE ?", ["%".strtolower($keyword)."%"]);
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('absen.show');
}

   public function create(Request $request)
{
    $kelasId = $request->input('id_kelas');
    $kelas = Kelas::findOrFail($kelasId); // Pastikan kelas valid

    return view('absen.create', [
        'kelas' => $kelas,
    ]);
}

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'kelas_id'   => 'required|exists:kelas,id_kelas',
        'santri_id'  => 'required|exists:santris,id_santri',
        'tgl_absen'  => 'required|date',
        'status'     => 'required|in:1,2,3,4',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // Cek apakah sudah ada absensi pada tanggal tersebut
    $exists = Absen::where('id_santri', $request->santri_id)
        ->whereDate('tgl_absen', $request->tgl_absen)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Absensi sudah ada untuk tanggal tersebut.')->withInput();
    }

    $santri = Santri::findOrFail($request->santri_id);

    Absen::create([
        'id_kelas'  => $request->kelas_id,
        'id_santri' => $santri->id_santri,
        'nisn'      => $santri->nisn,
        'tgl_absen' => $request->tgl_absen,
        'status'    => $request->status,
    ]);

    if ($request->action === 'continue') {
        return redirect()
            ->route('absen.create', ['id_kelas' => $request->kelas_id])
            ->with('success', 'Absensi disimpan. Tambah lagi.');
    }

   return redirect()->route('absen.detail', ['id' => $request->kelas_id])
    ->with('success', 'Absensi berhasil disimpan.');

}

public function edit($id)
{
    // Ambil data absensi berdasarkan ID
    $absen = Absen::findOrFail($id);

    // Ambil daftar kelas untuk dropdown
    $kelasList = Kelas::all();

    return view('absen.edit', compact('absen', 'kelasList'));
}



public function update(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'kelas_id'  => 'required|exists:kelas,id_kelas',
        'santri_id' => 'required|exists:santris,id_santri',
        'tgl_absen' => 'required|date',
        'status'    => 'required|in:1,2,3,4',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $absen = Absen::findOrFail($id);

    // Cek apakah absensi sudah ada pada tanggal dan santri tersebut (kecuali record ini)
    $exists = Absen::where('id_santri', $request->santri_id)
        ->whereDate('tgl_absen', $request->tgl_absen)
        ->where('id', '!=', $absen->id)
        ->exists();

    if ($exists) {
        return redirect()->back()->with('error', 'Absensi sudah ada untuk tanggal tersebut.')->withInput();
    }

    // Ambil data santri terbaru
    $santri = Santri::findOrFail($request->santri_id);

    // Update absensi
    $absen->update([
        'id_kelas'  => $request->kelas_id,
        'id_santri' => $santri->id_santri,
        'nisn'      => $santri->nisn,
        'tgl_absen' => $request->tgl_absen,
        'status'    => $request->status,
    ]);

    return redirect()->route('absen.detail', ['id' => $request->kelas_id])
        ->with('success', 'Absensi berhasil diupdate.');
}


public function detail($id_kelas, Request $request)
{
    $kelas = Kelas::findOrFail($id_kelas);
    $tanggal = $request->tgl_absen ?? now()->toDateString();

    $santris = Santri::with(['absens' => function ($q) use ($tanggal) {
        $q->whereDate('tgl_absen', $tanggal);
    }])
    ->where('id_kelas', $id_kelas)
    ->get();

    return view('absen.detail', compact('kelas', 'santris', 'tanggal'));
}


public function getSantriByKelas(Request $request)
{
    $santris = Santri::where('id_kelas', $request->kelas_id)->get(['id_santri', 'nama']);
    return response()->json($santris);
}

}
