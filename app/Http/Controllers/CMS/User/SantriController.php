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
            $data = Santri::with('kelas')->get();
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
            'nisn' => 'required|numeric|digits_between:1,10|unique:santris,nisn',
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

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('foto_santri')) {
            $data['foto_santri'] = $request->file('foto_santri')->store('santri', 'public');
        }

        Santri::create($data);

        return redirect('santri')->with('success', 'Santri berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $santri = Santri::findOrFail($id);
        $kelas = Kelas::all();
        return view('santri.edit', compact('santri', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|integer|unique:santris,nisn,' . $id . ',id_santri',
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
        ]);

        $santri = Santri::findOrFail($id);
        $data = $request->except('password', 'foto_santri');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto_santri')) {
            if ($santri->foto_santri) {
                Storage::disk('public')->delete($santri->foto_santri);
            }
            $data['foto_santri'] = $request->file('foto_santri')->store('santri', 'public');
        }

        $santri->update($data);
        return redirect()->route('santri.show', $santri->id_santri)->with('success', 'Santri berhasil diperbarui.');
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

        return DataTables::of($santris)
            ->addColumn('nama_kelas', function ($santri) {
                return $santri->kelas ? $santri->kelas->nama_kelas : 'Tidak Ada Kelas';
            })
            ->addColumn('action', function ($santri) {
                return '<a href="' . url('santri/show/' . $santri->id_santri) . '" class="btn btn-primary btn-sm" title="Preview">
                            <i class="fa fa-eye"></i>
                        </a>
                        <button class="btn btn-danger btn-sm btnDelete" data-id="' . $santri->id_santri . '" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function editOrangTua($id_santri)
    {
        $santri = Santri::findOrFail($id_santri);
        $ayah = $santri->keluarga->firstWhere('hubungan', 1);
        $ibu = $santri->keluarga->firstWhere('hubungan', 2);

        return view('santri.edit_orangtua', compact('santri', 'ayah', 'ibu'));
    }

    public function updateOrangTua(Request $request, $id_santri)
    {
        $santri = Santri::findOrFail($id_santri);

        $validated = $request->validate([
            'nama_ayah' => 'required|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'pendidikan_ayah' => 'nullable|string|max:255',
            'no_telp_ayah' => 'nullable|string|max:20',
            'alamat_ayah' => 'nullable|string',
            'email_ayah' => 'nullable|email|max:255',
            'tempat_lahir_ayah' => 'nullable|string|max:255',
            'tgl_lahir_ayah' => 'nullable|date',
            'nama_ibu' => 'required|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'pendidikan_ibu' => 'nullable|string|max:255',
            'no_telp_ibu' => 'nullable|string|max:20',
            'alamat_ibu' => 'nullable|string',
            'email_ibu' => 'nullable|email|max:255',
            'tempat_lahir_ibu' => 'nullable|string|max:255',
            'tgl_lahir_ibu' => 'nullable|date',
        ]);

        $this->updateKeluarga($santri->id_santri, 1, $validated, 'ayah');
        $this->updateKeluarga($santri->id_santri, 2, $validated, 'ibu');

        return redirect()->route('santri.show', $id_santri)->with('success', 'Data Orang Tua berhasil diperbarui!');
    }

    public function editWali($id_santri)
    {
        $santri = Santri::findOrFail($id_santri);
        $wali = $santri->keluarga->firstWhere('hubungan', 3);

        return view('santri.edit_wali', compact('santri', 'wali'));
    }

    public function updateWali(Request $request, $id_santri)
    {
        $santri = Santri::findOrFail($id_santri);

        $validated = $request->validate([
            'nama_wali' => 'required|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'pendidikan_wali' => 'nullable|string|max:255',
            'no_telp_wali' => 'nullable|string|max:20',
            'alamat_wali' => 'nullable|string',
            'email_wali' => 'nullable|email|max:255',
            'tempat_lahir_wali' => 'nullable|string|max:255',
            'tgl_lahir_wali' => 'nullable|date',
        ]);

        $this->updateKeluarga($santri->id_santri, 3, $validated, 'wali');

        return redirect()->route('santri.show', $id_santri)->with('success', 'Data Wali berhasil diperbarui!');
    }

    private function updateKeluarga($id_santri, $hubungan, $data, $prefix)
    {
        Keluarga::updateOrCreate(
            ['id_santri' => $id_santri, 'hubungan' => $hubungan],
            [
                'nama' => $data['nama_' . $prefix],
                'pekerjaan' => $data['pekerjaan_' . $prefix] ?? null,
                'pendidikan' => $data['pendidikan_' . $prefix] ?? null,
                'no_telp' => $data['no_telp_' . $prefix] ?? null,
                'alamat' => $data['alamat_' . $prefix] ?? null,
                'email' => $data['email_' . $prefix] ?? null,
                'tempat_lahir' => $data['tempat_lahir_' . $prefix] ?? null,
                'tgl_lahir' => $data['tgl_lahir_' . $prefix] ?? null,
            ]
        );
    }
}
