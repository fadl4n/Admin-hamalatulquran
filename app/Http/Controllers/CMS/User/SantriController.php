<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Keluarga;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Santri::with('kelas')
            ->orderBy('id_kelas')
            ->get();
            return DataTables::of($data)
                ->addColumn('nama_kelas', function ($row) {
                    return $row->kelas ? $row->kelas->nama_kelas : 'Tidak Ada Kelas';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . url('santri/show/' . $row->id_santri) . '" class="btn btn-primary btn-sm" title="Preview">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="' . url('santri/' . $row->id_santri . '/edit') . '" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm btnDelete" data-id="' . $row->id_santri . '">Hapus</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('santri.show');
    }

    public function show($id)
    {
        $santri = Santri::with(['kelas', 'keluarga'])->findOrFail($id);
        return view('santri.detail', compact('santri'));
    }


    public function create()
    {
        $kelas = Kelas::all();
        return view('santri.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|numeric|digits_between:1,10',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'angkatan' => 'nullable|string|max:50',
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
            'jenis_kelamin' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
            'password' => 'required|string|min:6',
            'foto_santri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Pengecekan manual untuk NISN dan Email
        if (Santri::where('nisn', $request->nisn)->exists()) {
            return redirect()->back()->withInput()->with('error', 'NISN sudah digunakan.');
        }

        if (Santri::where('email', $request->email)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan.');
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        $data['foto_santri'] = asset('assets/image/default.png'); // Gambar default

        if ($request->hasFile('foto_santri')) {
            $file = $request->file('foto_santri');
            $allowedFileTypes = ['png', 'jpg', 'jpeg'];
            $extension = $file->getClientOriginalExtension();

            // Validasi tipe file
            if (!in_array($extension, $allowedFileTypes)) {
                return redirect()->back()->with('error', 'File type not allowed. Only png and jpg files are allowed.');
            }

            // Buat nama file unik
            $name_original = date('YmdHis') . '_' . $file->getClientOriginalName();

            // Simpan file ke folder public
            $file->move(public_path('uploadedFile/image/santri'), $name_original);

            // Simpan path gambar
            $data['foto_santri'] = url('uploadedFile/image/santri') . '/' . $name_original;
        }


        Santri::create($data);

        return redirect('santri')->with('success', 'Santri berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $santri = Santri::with(['keluarga'])->findOrFail($id);

        // Ambil data keluarga berdasarkan hubungan
        $ayah = $santri->keluarga->firstWhere('hubungan', 1);
        $ibu = $santri->keluarga->firstWhere('hubungan', 2);
        $wali = $santri->keluarga->firstWhere('hubungan', 3);

        $kelas = Kelas::all();

        return view('santri.edit', compact('santri', 'ayah', 'ibu', 'wali', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|numeric|digits_between:1,10',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'email' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'angkatan' => 'nullable|string|max:50',
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
            'jenis_kelamin' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
            'password' => 'nullable|string|min:6',
            'foto_santri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'nama_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'pendidikan_ayah' => 'nullable|string|max:255',
            'no_telp_ayah' => 'nullable|string|max:20',
            'alamat_ayah' => 'nullable|string',
            'email_ayah' => 'nullable|email|max:255',
            'tempat_lahir_ayah' => 'nullable|string|max:255',
            'tgl_lahir_ayah' => 'nullable|date',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'pendidikan_ibu' => 'nullable|string|max:255',
            'no_telp_ibu' => 'nullable|string|max:20',
            'alamat_ibu' => 'nullable|string',
            'email_ibu' => 'nullable|email|max:255',
            'tempat_lahir_ibu' => 'nullable|string|max:255',
            'tgl_lahir_ibu' => 'nullable|date',
            'nama_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'pendidikan_wali' => 'nullable|string|max:255',
            'no_telp_wali' => 'nullable|string|max:20',
            'alamat_wali' => 'nullable|string',
            'email_wali' => 'nullable|email|max:255',
            'tempat_lahir_wali' => 'nullable|string|max:255',
            'tgl_lahir_wali' => 'nullable|date',
        ]);

        $santri = Santri::findOrFail($id);
        $data = $request->except('password', 'foto_santri');

        // Pengecekan manual untuk NISN dan Email
        if (Santri::where('nisn', $request->nisn)->where('id_santri', '!=', $santri->id_santri)->exists()) {
            return redirect()->back()->withInput()->with('error', 'NISN sudah digunakan oleh santri lain.');
        }

        if (Santri::where('email', $request->email)->where('id_santri', '!=', $santri->id_santri)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan oleh santri lain.');
        }

        // Handle password update
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Handle foto santri upload
        if ($request->hasFile('foto_santri')) {
            $file = $request->file('foto_santri');
            $allowedFileTypes = ['png', 'jpg', 'jpeg'];

            // Validasi tipe file
            $extension = $file->getClientOriginalExtension();
            if (!in_array($extension, $allowedFileTypes)) {
                return redirect()->back()->with('error', 'File type not allowed. Only PNG, JPG, and JPEG files are allowed.');
            }

            // Hapus file lama jika ada
            if ($santri->foto_santri && file_exists(public_path('uploadedFile/image/santri/' . basename($santri->foto_santri)))) {
                unlink(public_path('uploadedFile/image/santri/' . basename($santri->foto_santri)));
            }

            // Simpan file gambar baru dengan nama unik
            $name_original = date('YmdHis') . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploadedFile/image/santri'), $name_original);

            // Simpan path gambar baru ke dalam data
            $data['foto_santri'] = 'uploadedFile/image/santri/' . $name_original;
        }

        // Update data keluarga (ayah, ibu, wali)
        $this->updateKeluarga($santri->id_santri, 1, $request->all(), 'ayah');
        $this->updateKeluarga($santri->id_santri, 2, $request->all(), 'ibu');
        $this->updateKeluarga($santri->id_santri, 3, $request->all(), 'wali');

        // Update data santri
        $santri->update($data);

        return redirect()->route('santri.index')->with('success', 'Santri berhasil diperbarui.');
    }

    private function updateKeluarga($id_santri, $hubungan, $data, $prefix)
    {
        // Set default values as null if not present
        $keluargaData = [
            'nama' => $data['nama_' . $prefix] ?? null,
            'pekerjaan' => $data['pekerjaan_' . $prefix] ?? null,
            'pendidikan' => $data['pendidikan_' . $prefix] ?? null,
            'no_telp' => $data['no_telp_' . $prefix] ?? null,
            'alamat' => $data['alamat_' . $prefix] ?? null,
            'email' => $data['email_' . $prefix] ?? null,
            'tempat_lahir' => $data['tempat_lahir_' . $prefix] ?? null,
            'tgl_lahir' => $data['tgl_lahir_' . $prefix] ?? null,
        ];

        Keluarga::updateOrCreate(
            ['id_santri' => $id_santri, 'hubungan' => $hubungan],
            $keluargaData
        );
    }





    public function destroy($id)
    {
        $santri = Santri::findOrFail($id);
        if ($santri->foto_santri) {
            Storage::disk('public')->delete($santri->foto_santri);
        }
        $santri->delete();

        return response()->json(['success' => 'Santri berhasil dihapus.']);
    }

    public function fnGetData(Request $request)
    {
        $santris = Santri::with('kelas')->select(['id_santri', 'nama', 'nisn', 'angkatan', 'id_kelas']);

        // Menangani pengurutan berdasarkan kolom yang dikirim dari DataTables
        if ($request->has('order')) {
            $orderColumnIndex = $request->input('order.0.column');
            $orderDirection = $request->input('order.0.dir');

            // Menentukan kolom yang akan diurutkan
            $columns = ['id_kelas', 'nama', 'nisn', 'angkatan'];

            // Menambahkan pengurutan berdasarkan kolom dan arah
            if ($orderColumnIndex == 0) {
                // Jika yang diurutkan adalah kolom nama (indeks 0), maka urutkan berdasarkan id_kelas dulu
                $santris->orderBy('id_kelas', 'asc')->orderBy('nama', $orderDirection);
            } elseif ($orderColumnIndex == 1) {
                // Jika yang diurutkan adalah kolom lainnya, lakukan pengurutan yang sesuai
                $santris->orderBy('id_kelas', 'asc')->orderBy('nama', 'asc');
            } else {
                // Pengurutan default jika bukan kolom pertama atau lainnya
                $santris->orderBy('id_kelas', 'asc')->orderBy('nama', 'asc');
            }
        } else {
            // Default urutan berdasarkan id_kelas terlebih dahulu, baru nama
            $santris->orderBy('id_kelas', 'asc')->orderBy('nama', 'asc');
        }

        return DataTables::of($santris)
            ->addColumn('nama_kelas', function ($santri) {
                return $santri->kelas ? $santri->kelas->nama_kelas : 'Tidak Ada Kelas';
            })
            ->addColumn('action', function ($santri) {
                return '<a href="' . url('santri/show/' . $santri->id_santri) . '" class="btn btn-primary btn-sm" title="Preview">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a href="' . url('santri/edit/' . $santri->id_santri) . '" class="btn btn-warning btn-sm" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-danger btn-sm btnDelete" data-id="' . $santri->id_santri . '" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }



}


