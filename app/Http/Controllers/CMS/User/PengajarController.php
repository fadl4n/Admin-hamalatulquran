<?php

namespace App\Http\Controllers\CMS\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Pengajar;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class PengajarController extends Controller
{
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

    public function create()
    {
        return view('pengajar.create');
    }

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
            'foto_pengajar' => $fotoPath, // cuma path (biar Storage::url bisa dipake di frontend)
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

    public function edit($id)
    {
        $pengajar = Pengajar::findOrFail($id);
        return view('pengajar.edit', compact('pengajar'));
    }

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

        $pengajar = Pengajar::findOrFail($id);

        // Validasi unik NIP & Email
        if (Pengajar::where('nip', $request->nip)->where('id_pengajar', '!=', $id)->exists()) {
            return back()->withInput()->with('error', 'NIP sudah digunakan oleh pengajar lain.');
        }

        if (Pengajar::where('email', $request->email)->where('id_pengajar', '!=', $id)->exists()) {
            return back()->withInput()->with('error', 'Email sudah digunakan oleh pengajar lain.');
        }

        // Mapping jenis kelamin string ke integer
        $jenisKelaminMap = [
            'Laki-laki' => 1,
            'Perempuan' => 2,
        ];

        $data = $request->only([
            'nama',
            'nip',
            'email',
            'tempat_lahir',
            'tgl_lahir',
            'jenis_kelamin',
            'no_telp',
            'alamat'
        ]);

        $data['jenis_kelamin'] = $jenisKelaminMap[$request->jenis_kelamin] ?? null;

        // Cek dan simpan foto baru kalau ada
        if ($request->hasFile('foto_pengajar')) {
            $file = $request->file('foto_pengajar');
            $filename = now()->format('YmdHis') . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploadedFile/image/pengajar'), $filename);
            $data['foto_pengajar'] = url('uploadedFile/image/pengajar/' . $filename);
        }

        // Cek password baru
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update data pengajar
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
