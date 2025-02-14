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

        if (Kelas::where('nama_kelas', $request->nama_kelas)->exists()) {
            return redirect('kelas/create')->with('error', 'Nama kelas sudah ada. Silakan pilih nama lain.');
        }

        Kelas::create($request->all());
        return redirect('kelas')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kelas = Kelas::findOrFail($id);
        return view('kelas.edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|string|max:255',
        ], [
            'nama_kelas.required' => 'Nama kelas harus diisi.',
        ]);

        if (Kelas::where('nama_kelas', $request->nama_kelas)->where('id_kelas', '!=', $id)->exists()) {
            return redirect('kelas')->with('error', 'Nama kelas sudah ada. Silakan pilih nama lain.');
        }

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        return redirect('kelas')->with('success', 'Kelas berhasil diperbarui.');
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
                    <a href="'.url('kelas/edit/'.$kelas->id_kelas).'" class="btn btn-warning btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-danger btn-sm btnDelete" data-id="'.$kelas->id_kelas.'" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
