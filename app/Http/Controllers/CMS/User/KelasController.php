<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kelas::withCount('santri')->get();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.url('kelas/'.$row->id_kelas.'/edit').'" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm btnDelete" data-id="'.$row->id_kelas.'">
                            <i class="fas fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('kelas.show');
    }

    public function create()
    {
        return view('kelas.create')->with('error', session('error'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
        ], [
            'nama_kelas.required' => 'Nama kelas harus diisi.',
        ]);

        // Cek apakah nama kelas sudah ada
        if (Kelas::where('nama_kelas', $request->nama_kelas)->exists()) {
            // Mengirimkan response JSON dengan error jika nama kelas sudah ada
            return response()->json(['error' => 'Nama kelas sudah ada. Silakan pilih nama lain.'], 400);
        }

        // Menyimpan kelas baru
        Kelas::create($request->all());

        // Mengirimkan response sukses
        return response()->json(['success' => 'Kelas berhasil ditambahkan.'], 200);
    }


    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return response()->json($kelas); // Mengirimkan data kelas dalam bentuk JSON
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
        ], [
            'nama_kelas.required' => 'Nama kelas harus diisi.',
        ]);

        // Cek apakah nama kelas sudah ada dan bukan kelas yang sedang diupdate
        if (Kelas::where('nama_kelas', $request->nama_kelas)->where('id_kelas', '!=', $id)->exists()) {
            // Mengirimkan response JSON dengan error jika nama kelas sudah ada
            return response()->json(['error' => 'Nama kelas sudah ada. Silakan pilih nama lain.'], 400);
        }

        // Update kelas
        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        // Mengirimkan response sukses
        return response()->json(['success' => 'Kelas berhasil diperbarui.'], 200);
    }


    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json(['success' => 'Kelas berhasil dihapus.']);
    }

    public function fnGetData(Request $request)
{
    $kelas = Kelas::withCount('santri')->get();

    return DataTables::of($kelas)
        ->addIndexColumn()
        ->addColumn('action', function ($kelas) {
            return '
                <button class="btn btn-warning btn-sm btnEdit" data-id="'.$kelas->id_kelas.'" title="Edit">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm btnDelete" data-id="'.$kelas->id_kelas.'" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            ';
        })
        ->rawColumns(['action'])
        ->make(true);
}

}
