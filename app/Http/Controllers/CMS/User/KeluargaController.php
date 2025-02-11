<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Keluarga;
use App\Models\Santri;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;

class KeluargaController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $data = Keluarga::with('santri')->get();
        return DataTables::of($data)
            ->addColumn('nama_santri', function ($row) {
                return $row->santri ? $row->santri->nama : 'Tidak Ada Santri';
            })
            ->addColumn('action', function ($row) {
                return '<a href="' . url('keluarga/' . $row->id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm btnDelete" data-id="' . $row->id . '">Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    return view('keluarga.show');
}


public function fnGetData()
{
    $data = Keluarga::leftJoin('santris', 'keluarga.id_santri', '=', 'santris.id_santri')
                    ->select('keluarga.*', 'santris.nama as nama_santri'); // Gunakan nama_santri

    return DataTables::of($data)
        ->addColumn('action', function ($row) {
            return '
                <a href="'.route('keluarga.edit', $row->id).'" class="btn btn-sm btn-warning">
                    <i class="fas fa-edit"></i>
                </a>
                <button data-id="'.$row->id.'" class="btn btn-sm btn-danger btnDelete">
                    <i class="fas fa-trash-alt"></i>
                </button>
            ';
        })
        ->rawColumns(['action'])
        ->make(true);
}



    public function create()
    {
        $santris = Santri::all();
        return view('keluarga.create', compact('santris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15',
            'id_santri' => 'nullable|exists:santris,id_santri',
            'alamat' => 'required|string',
            'email' => 'required|email|unique:keluarga,email',
            'password' => 'required|string|min:6',
        ]);

        Keluarga::create([
            'nama' => $request->nama,
            'pekerjaan' => $request->pekerjaan,
            'pendidikan' => $request->pendidikan,
            'no_telp' => $request->no_telp,
            'id_santri' => $request->id_santri,
            'alamat' => $request->alamat,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil ditambahkan.');
    }

    public function edit(Keluarga $keluarga)
    {
        $santris = Santri::all();
        $keluarga = Keluarga::with('santri')->find($keluarga->id); // Tambahkan relasi santri
        return view('keluarga.edit', compact('keluarga', 'santris'));
    }

    public function update(Request $request, Keluarga $keluarga)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'pekerjaan' => 'required|string|max:255',
            'pendidikan' => 'required|string|max:255',
            'no_telp' => 'required|string|max:15',
            'id_santri' => 'nullable|exists:santris,id_santri',
            'alamat' => 'required|string',
            'email' => 'required|email|unique:keluarga,email,' . $keluarga->id,
            'password' => 'nullable|string|min:6',
        ]);

        // Perbaikan: Jika id_santri kosong, pastikan nilainya tetap bisa disimpan
        $keluarga->update([
            'nama' => $request->nama,
            'pekerjaan' => $request->pekerjaan,
            'pendidikan' => $request->pendidikan,
            'no_telp' => $request->no_telp,
            'id_santri' => $request->id_santri ?? null,  // Memastikan bisa null
            'alamat' => $request->alamat,
            'email' => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $keluarga->password,
        ]);

        return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil diperbarui.');
    }


    public function destroy(Keluarga $keluarga)
{
    $keluarga->delete();
    return redirect()->route('keluarga.index')->with('success', 'Data keluarga berhasil dihapus.');
}

}
