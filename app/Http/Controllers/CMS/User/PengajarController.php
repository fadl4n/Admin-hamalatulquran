<?php

namespace App\Http\Controllers\CMS\User;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pengajar;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

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
                        <a href="' . url('pengajar/show/' . $row->id_pengajar) . '" class="btn btn-info btn-sm" title="Detail">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button class="btn btn-danger btn-sm btnDelete" data-id="' . $row->id_pengajar . '">
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
        'nip' => 'required|numeric|digits_between:1,15',
        'email' => 'required|string|email',
        'tempat_lahir' => 'required|string|max:255',
        'tgl_lahir' => 'required|date',
        'foto_pengajar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'no_telp' => 'required|string|max:20',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'alamat' => 'required|string|max:255',
        'password' => 'required|string|min:6'
    ]);

    // Pengecekan manual untuk NIP
    if (Pengajar::where('nip', $request->nip)->exists()) {
        return redirect()->back()->withInput()->with('error', 'NIP sudah digunakan. Silakan gunakan NIP lain.');
    }

    // Pengecekan manual untuk Email
    if (Pengajar::where('email', $request->email)->exists()) {
        return redirect()->back()->withInput()->with('error', 'Email sudah digunakan. Silakan gunakan email lain.');
    }

    $fotoPath = asset('assets/image/default-user.png'); // Gambar default

    if ($request->hasFile('foto_pengajar')) {
        $file = $request->file('foto_pengajar');
        $allowedFileTypes = ['png', 'jpg', 'jpeg'];
        $extension = $file->getClientOriginalExtension();

        // Validasi tipe file
        if (!in_array($extension, $allowedFileTypes)) {
            return redirect()->back()->with('error', 'File type not allowed. Only png and jpg files are allowed.');
        }

        // Buat nama file unik
        $name_original = date('YmdHis') . '_' . $file->getClientOriginalName();

        // Simpan file ke folder public
        $file->move(public_path('uploadedFile/image/pengajar'), $name_original);

        // Simpan path gambar
        $fotoPath = url('uploadedFile/image/pengajar') . '/' . $name_original;
    }


    Pengajar::create([
        'nama' => $request->nama,
        'nip' => $request->nip,
        'email' => $request->email,
        'tempat_lahir' => $request->tempat_lahir,
        'tgl_lahir' => $request->tgl_lahir,
        'foto_pengajar' => $fotoPath,
        'no_telp' => $request->no_telp,
        'jenis_kelamin' => $request->jenis_kelamin,
        'alamat' => $request->alamat,
        'password' => Hash::make($request->password),
    ]);

    return redirect()->route('pengajar.index')->with('success', 'Pengajar berhasil ditambahkan.');
}



    /**
     * Menampilkan detail pengajar.
     */
    public function show($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        return view('pengajar.detail', compact('pengajar'));
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
        'nip' => 'required|numeric|digits_between:1,15',
        'email' => 'required|string|email',
        'no_telp' => 'required|string|max:20',
        'tempat_lahir' => 'required|string|max:255',
        'tgl_lahir' => 'required|date',
        'foto_pengajar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'alamat' => 'required|string|max:255',
        'password' => 'nullable|string|min:6',
    ]);

    // Pengecekan manual untuk NIP
    $pengajar = Pengajar::findOrFail($id);
    if (Pengajar::where('nip', $request->nip)->where('id_pengajar', '!=', $id)->exists()) {
        return redirect()->back()->withInput()->with('error', 'NIP sudah digunakan oleh pengajar lain.');
    }

    // Pengecekan manual untuk Email
    if (Pengajar::where('email', $request->email)->where('id_pengajar', '!=', $id)->exists()) {
        return redirect()->back()->withInput()->with('error', 'Email sudah digunakan oleh pengajar lain.');
    }

    if ($request->hasFile('foto_pengajar')) {
        $file = $request->file('foto_pengajar');
        $allowedFileTypes = ['png', 'jpg', 'jpeg'];
        $extension = $file->getClientOriginalExtension();

        if (!in_array($extension, $allowedFileTypes)) {
            return redirect()->back()->with('error', 'File type not allowed. Only png and jpg files are allowed.');
        }

        $name_original = date('YmdHis') . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploadedFile/image/pengajar'), $name_original);
        $fotoPath = url('uploadedFile/image/pengajar') . '/' . $name_original;
    } else {
        $fotoPath = asset('assets/image/default-user.png');
    }


    $data = [
        'nama' => $request->nama,
        'nip' => $request->nip,
        'email' => $request->email,
        'tempat_lahir' => $request->tempat_lahir,
        'tgl_lahir' => $request->tgl_lahir,
        'foto_pengajar' => $fotoPath,
        'no_telp' => $request->no_telp,
        'jenis_kelamin' => $request->jenis_kelamin,
        'alamat' => $request->alamat,
    ];

    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    $pengajar->update($data);

    return redirect()->route('pengajar.index')->with('success', 'Pengajar berhasil diperbarui.');
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

    /**
     * Mendapatkan data pengajar untuk DataTables.
     */
    public function fnGetData(Request $request)
    {
        $pengajar = Pengajar::select(['id_pengajar', 'nama', 'nip', 'email']);
        return DataTables::of($pengajar)
        ->addIndexColumn() 
            ->addColumn('action', function ($pengajar) {
                return '
                     <a href="' . url('pengajar/show/' . $pengajar->id_pengajar) . '" class="btn btn-info btn-sm" title="Detail">
                            <i class="fas fa-eye"></i>
                    </a>
                      <a href="' . url('pengajar/edit/' . $pengajar->id_pengajar) . '" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                    <button class="btn btn-danger btn-sm btnDelete" data-id="' . $pengajar->id_pengajar . '" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
