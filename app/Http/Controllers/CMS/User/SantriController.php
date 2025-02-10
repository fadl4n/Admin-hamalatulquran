<?php

namespace App\Http\Controllers\CMS\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;


class SantriController extends Controller
{
        /**
     * Menampilkan daftar santri.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Santri::all();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '<a href="'.url('santri/'.$row->id_santri.'/edit').'" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm btnDelete" data-id="'.$row->id_santri.'">Hapus</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('santri.show');
    }

    /**
     * Menampilkan halaman tambah santri.
     */
    public function create()
    {
        return view('santri.create');
    }

    /**
     * Menyimpan data santri baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|integer|unique:santris,nisn',
            'tgl_lahir' => 'required|date',
            'alamat' => 'nullable|string|max:255',
            'angkatan' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
        ]);

        Santri::create($request->all());

        return redirect('santri')->with('success', 'Santri berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman edit santri.
     */
    public function edit($id)
    {
        $santri = Santri::findOrFail($id);
        return view('santri.edit', compact('santri'));
    }

    /**
     * Mengupdate data santri di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|integer|unique:santris,nisn,'.$id.',id_santri',
            'tgl_lahir' => 'required|date',
            'alamat' => 'nullable|string|max:255',
            'angkatan' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
        ]);

        $santri = Santri::findOrFail($id);
        $santri->update($request->all());

        return redirect('santri')->with('success', 'Santri berhasil diperbarui.');
    }

    /**
     * Menghapus santri dari database.
     */
    public function destroy($id)
{
    $santri = Santri::findOrFail($id);
    $santri->delete();

    return response()->json(['success' => 'Santri berhasil dihapus.']);
}

    public function fnGetData(Request $request)
{
    $santris = Santri::select(['id_santri', 'nama', 'nisn', 'tgl_lahir', 'alamat', 'angkatan', 'jenis_kelamin', 'status']);

    return DataTables::of($santris)
        ->addColumn('action', function ($santri) {
            return '
                <a href="'.url('santri/edit/'.$santri->id_santri).'" class="btn btn-warning btn-sm" title="Edit">
                    <i class="fas fa-edit"></i>
                </a>
                <button class="btn btn-danger btn-sm btnDelete" data-id="'.$santri->id_santri.'" title="Hapus">
                    <i class="fas fa-trash"></i>
                </button>
            ';
        })
        ->rawColumns(['action'])
        ->make(true);
}


}
