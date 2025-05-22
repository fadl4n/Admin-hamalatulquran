<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Target;
use App\Models\Setoran;
use App\Models\Histori;
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
   <a href="'.url('kelas/'.$kelas->id_kelas.'/santri').'" class="btn btn-info btn-sm" title="Detail">
        <i class="fas fa-eye"></i>
    </a>
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
public function showSantri($id_kelas)
{
    $kelas = Kelas::findOrFail($id_kelas);
    return view('kelas.detail', compact('kelas'));
}
public function showDetailSantri($id_kelas, $id_santri)
{
    $santri = Santri::with(['kelas', 'keluarga'])->findOrFail($id_santri);
    $kelas = Kelas::findOrFail($id_kelas);

    // Ambil semua target milik santri
    $targets = Target::where('id_santri', $id_santri)->with('surat')->get();

    $hafalan = [];
    $murojaah = [];

    foreach ($targets as $target) {
        $namaSurat = $target->surat->nama_surat ?? '-';

        $nilaiHafalan = Setoran::where('id_target', $target->id_target)->avg('nilai');
        $ayatHafalanStart = Setoran::where('id_target', $target->id_target)->min('jumlah_ayat_start');
        $ayatHafalanEnd = Setoran::where('id_target', $target->id_target)->max('jumlah_ayat_end');
        $ayatHafalan = ($ayatHafalanStart && $ayatHafalanEnd) ? "$ayatHafalanStart - $ayatHafalanEnd" : '-';

        $nilaiMurojaah = Histori::where('id_target', $target->id_target)->avg('nilai');
        $ayatMurojaahStart = $target->jumlah_ayat_target_awal;
        $ayatMurojaahEnd = $target->jumlah_ayat_target;
        $ayatMurojaah = ($ayatMurojaahStart && $ayatMurojaahEnd) ? "$ayatMurojaahStart - $ayatMurojaahEnd" : '-';

        $hafalan[] = [
            'surat' => $namaSurat,
            'nilai' => number_format($nilaiHafalan ?? 0, 2),
            'ayat'  => $ayatHafalan,
        ];

        $murojaah[] = [
            'surat' => $namaSurat,
            'nilai' => number_format($nilaiMurojaah ?? 0, 2),
            'ayat'  => $ayatMurojaah,
        ];
    }

    return view('kelas.detail_santri', compact('santri', 'kelas', 'hafalan', 'murojaah'));
}


public function fnGetSantri(Request $request)
{
    $kelas_id = $request->input('kelas_id');

    $santriQuery = Santri::where('id_kelas', $kelas_id)->select('id_santri as id', 'nama', 'nisn');

    return DataTables::of($santriQuery)
        ->addColumn('action', function ($santri) use ($kelas_id) {
            return '<a href="'.url('kelas/'.$kelas_id.'/santri/'.$santri->id).'" class="btn btn-info btn-sm" title="Detail">
                        <i class="fas fa-eye"></i>
                    </a>';
        })
        ->rawColumns(['action'])
        ->make(true);
}


}
