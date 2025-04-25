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
            'foto_pengajar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:7168',
            'no_telp' => 'required|string|max:20',
            'jenis_kelamin' => 'required|integer|in:1,2',
            'alamat' => 'required|string|max:255',
            'password' => 'required|string|min:6'
        ]);

        if (Pengajar::where('nip', $request->nip)->exists()) {
            return redirect()->back()->withInput()->with('error', 'NIP sudah digunakan.');
        }

        if (Pengajar::where('email', $request->email)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan.');
        }

        $fotoPath = 'uploadedFile/image/default-user.png'; // Default image di storage/app/public

        if ($request->hasFile('foto_pengajar')) {
            $file = $request->file('foto_pengajar');

            $filename = time() . '.' . $file->getClientOriginalExtension();

            // Gunain Intervention Image (pakai driver GD)
            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $img = $manager->read($file->getPathname());

            $encodedImage = $img->toJpeg(75); // kualitas 75%

            $path = 'uploadedFile/image/pengajar/' . $filename;
            Storage::disk('public')->put($path, (string) $encodedImage);

            $fotoPath = asset('storage/' . $path);
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
            'jenis_kelamin' => 'required|integer|in:1,2',
            'alamat' => 'required|string|max:255',
            'password' => 'nullable|string|min:6',
        ]);

        $pengajar = Pengajar::findOrFail($id);

        if (Pengajar::where('nip', $request->nip)->where('id_pengajar', '!=', $id)->exists()) {
            return redirect()->back()->withInput()->with('error', 'NIP sudah digunakan.');
        }

        if (Pengajar::where('email', $request->email)->where('id_pengajar', '!=', $id)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan.');
        }

        $fotoPath = $pengajar->foto_pengajar;

        if ($request->hasFile('foto_pengajar')) {
            $file = $request->file('foto_pengajar');
            $extension = $file->getClientOriginalExtension();
            $allowedFileTypes = ['png', 'jpg', 'jpeg'];

            if (!in_array($extension, $allowedFileTypes)) {
                return redirect()->back()->with('error', 'Tipe file tidak diperbolehkan.');
            }

            $name_original = date('YmdHis') . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploadedFile/image/pengajar'), $name_original);
            $fotoPath = url('uploadedFile/image/pengajar/' . $name_original);
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
