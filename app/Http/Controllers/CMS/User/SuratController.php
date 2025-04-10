<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SuratController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Surat::all();

            // Pastikan format yang dikirim benar
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <a href="'.route('surat.edit', $row->id_surat).'" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                        <button data-id="'.$row->id_surat.'" class="btn btn-sm btn-danger btnDelete"><i class="fas fa-trash-alt"></i></button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('surat.show');
    }

    public function create()
    {
        return view('surat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'jumlah_ayat' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        Surat::create($request->all());

        return redirect()->route('surat.index')->with('success', 'Data surat berhasil ditambahkan.');
    }

    public function edit(Surat $surat)
    {
        return view('surat.edit', compact('surat'));
    }

    public function update(Request $request, Surat $surat)
    {
        $request->validate([
            'nama_surat' => 'required|string|max:255',
            'jumlah_ayat' => 'required|integer|min:1',

            'deskripsi' => 'nullable|string',
        ]);

        $surat->update($request->all());

        return redirect()->route('surat.index')->with('success', 'Data surat berhasil diperbarui.');
    }

    public function destroy($id)
{
    $surat = Surat::where('id_surat', $id)->firstOrFail();
    $surat->delete();
    return response()->json(['success' => 'Data surat berhasil dihapus.']);
}

}

