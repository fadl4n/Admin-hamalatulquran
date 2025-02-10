<?php

namespace App\Http\Controllers\CMS\User;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pengajar;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class PengajarController extends Controller
{
    /**
     * Menampilkan daftar pengajar.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Pengajar::all();
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.url('pengajar/'.$row->id_pengajar.'/edit').'" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm btnDelete" data-id="'.$row->id_pengajar.'">
                            <i class="fas fa-trash"></i>
                        </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('pengajar.show');
    }

    /**
     * Menampilkan halaman tambah pengajar.
     */
    public function create()
    {
        return view('pengajar.create');
    }

    /**
     * Menyimpan data pengajar baru ke database.
     */
    public function store(Request $request)
{
    $request->validate([
        'nama' => 'required|string|max:255',
        'nip' => 'required|string|unique:pengajar,nip',
        'email' => 'required|string|email|unique:pengajar,email',
        'no_telp' => 'required|string|max:20',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'alamat' => 'required|string|max:255',
        'password' => 'required|string|min:6',
    ]);

    Pengajar::create([
        'nama' => $request->nama,
        'nip' => $request->nip,
        'email' => $request->email,
        'no_telp' => $request->no_telp,
        'jenis_kelamin' => $request->jenis_kelamin,
        'alamat' => $request->alamat,
        'password' => Hash::make($request->password), // Hashing password
    ]);

    return redirect('pengajar')->with('success', 'Pengajar berhasil ditambahkan.');
}


    /**
     * Menampilkan halaman edit pengajar.
     */
    public function edit($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        return view('pengajar.edit', compact('pengajar'));
    }

    /**
     * Mengupdate data pengajar di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'required|string|unique:pengajar,nip,'.$id.',id_pengajar',
            'email' => 'required|string|email|unique:pengajar,email,'.$id.',id_pengajar',
            'no_telp' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $pengajar = Pengajar::findOrFail($id);
        $pengajar->update([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'email' => $request->email,
            'no_telp' => $request->no_telp,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat' => $request->alamat,
            'password' => $request->password ? Hash::make($request->password) : $pengajar->password,
        ]);

        return redirect('pengajar')->with('success', 'Pengajar berhasil diperbarui.');
    }

    /**
     * Menghapus pengajar dari database.
     */
    public function destroy($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        $pengajar->delete();

        return response()->json(['success' => 'Pengajar berhasil dihapus.']);
    }
    public function fnGetData(Request $request)
    {
        $pengajar = Pengajar::select(['id_pengajar', 'nama', 'nip', 'email', 'alamat', 'no_telp', 'jenis_kelamin', 'password']);

        return DataTables::of($pengajar)
            ->addColumn('action', function ($pengajar) {
                return '
                    <a href="'.url('pengajar/edit/'.$pengajar->id_pengajar).'" class="btn btn-warning btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-danger btn-sm btnDelete" data-id="'.$pengajar->id_pengajar.'" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}

