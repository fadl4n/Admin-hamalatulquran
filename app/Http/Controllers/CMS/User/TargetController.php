<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;

use App\Models\Target;
use App\Models\Santri;
use App\Models\Pengajar;
use App\Models\Kelas;
use App\Models\Surat;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Http\Request;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Mengambil data dan mengelompokkan berdasarkan kriteria yang sama
    $targets = Target::with(['santri', 'kelas', 'surat','pengajar'])
        ->get()
        ->groupBy(function ($item) {
            return $item->id_santri . '-' . $item->id_group;
        });

    return view('target.show', compact('targets'));
}


    public function create()
    {
        $santris = Santri::all();
        $pengajars = Pengajar::all();
        $kelas = Kelas::all();
        $surats = Surat::all();
        return view('target.create', compact('santris', 'pengajars', 'kelas', 'surats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'id_pengajar' => 'required|exists:pengajars,id_pengajar',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_surat' => 'required|exists:surats,id_surat',
            'tgl_mulai' => 'required|date',
            'jumlah_ayat_target_awal' => 'required|integer|min:1',
            'jumlah_ayat_target' => 'required|integer|min:1',
            'tgl_target' => 'required|date',
            'id_group' => 'nullable|integer'
        ]);

        // Ambil jumlah ayat dari surat terkait
        $surat = Surat::findOrFail($request->id_surat);

        if ($request->jumlah_ayat_target_awal > $request->jumlah_ayat_target) {
            return back()->withErrors(['jumlah_ayat_target_awal' => 'Jumlah ayat target awal tidak boleh lebih dari jumlah ayat target.'])->withInput();
        }
        // Validasi agar jumlah_ayat_target tidak melebihi jumlah_ayat surat
        if ($request->jumlah_ayat_target > $surat->jumlah_ayat) {
            return back()->withErrors(['jumlah_ayat_target' => 'Jumlah ayat target tidak boleh lebih dari jumlah ayat surat.'])->withInput();
        }

        // Simpan data ke database
        Target::create($request->all());

        // Cek apakah tombol "Continue" ditekan
        if ($request->has('continue')) {
            return redirect()->route('target.create')->with('success', 'Data berhasil disimpan!')->withInput();
        }

        // Jika tombol simpan ditekan, kembali ke halaman index
        return redirect()->route('target.index')->with('success', 'Target berhasil ditambahkan!');
    }


    public function show($id)
    {
        $target = Target::with(['santri', 'pengajar', 'kelas', 'surat'])->findOrFail($id);
        return view('target.show', compact('target'));
    }
public function edit(Target $target)
{
    $santris = Santri::all();
    $pengajars = Pengajar::all();
    $kelas = Kelas::all();
    $surats = Surat::all();
    return view('target.edit', compact('target', 'santris', 'pengajars', 'kelas', 'surats'));
}

public function update(Request $request, Target $target)
{
    $request->validate([
        'id_santri' => 'required|exists:santris,id_santri',
        'id_pengajar' => 'required|exists:pengajars,id_pengajar',
        'id_kelas' => 'required|exists:kelas,id_kelas',
        'id_surat' => 'required|exists:surats,id_surat',
        'jumlah_ayat_target_awal' => 'required|integer|min:1',
        'jumlah_ayat_target' => 'required|integer|min:1',
        'tgl_mulai' => 'required|date',
        'tgl_target' => 'required|date',
        'id_group' => 'nullable|integer'
    ]);

    // Ambil jumlah ayat dari surat terkait
    $surat = Surat::findOrFail($request->id_surat);

    if ($request->jumlah_ayat_target_awal >$request->jumlah_ayat_target) {
        return back()->withErrors(['jumlah_ayat_target_awal' => 'Jumlah ayat target awal tidak boleh lebih dari jumlah ayat target.'])->withInput();
    }

    // Validasi agar jumlah_ayat_target tidak melebihi jumlah_ayat surat
    if ($request->jumlah_ayat_target > $surat->jumlah_ayat) {
        return back()->withErrors(['jumlah_ayat_target' => 'Jumlah ayat target tidak boleh lebih dari jumlah ayat surat.'])->withInput();
    }

    $target->update($request->all());

    return redirect()->route('target.index')->with('success', 'Target berhasil diperbarui!');
}

public function destroy($id_santri, $id_group)
{
    try {
        // Mencari target berdasarkan id_santri dan id_group
        $target = Target::where('id_santri', $id_santri)
            ->where('id_group', $id_group)
            ->first();

        // Pastikan target ditemukan
        if (!$target) {
            return response()->json(['error' => 'Target tidak ditemukan'], 404);
        }

        // Hapus target
        $target->delete();

        return response()->json(['success' => 'Target berhasil dihapus']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal menghapus target'], 500);
    }
}
public function destroyByIdTarget($id_target)
{
    try {
        // Mencari target berdasarkan id_target
        $target = Target::findOrFail($id_target);

        // Hapus target
        $target->delete();

        return response()->json(['success' => 'Target berhasil dihapus']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal menghapus target'], 500);
    }
}



// Untuk DataTables
public function fnGetData()
{
    $targets = Target::with(['santri', 'pengajar', 'kelas', 'surat'])
        ->select('targets.*');

    return DataTables::of($targets)
        ->addIndexColumn()
        ->addColumn('nama_santri', function ($row) {
            return $row->santri->nama;
        })
        ->addColumn('nama_pengajar', function ($row) {
            return $row->pengajar->nama;
        })
        ->addColumn('nama_kelas', function ($row) {
            return $row->kelas->nama_kelas;
        })
        ->addColumn('nama_surat', function ($row) {
            return $row->surat->nama_surat;
        })
        ->addColumn('jumlah_ayat_target_awal', function ($row) {  // ✅ Menggunakan jumlah_ayat_target
            return $row->jumlah_ayat_target_awal;
        })
        ->addColumn('jumlah_ayat_target', function ($row) {  // ✅ Menggunakan jumlah_ayat_target
            return $row->jumlah_ayat_target;
        })
        ->addColumn('tgl_mulai', function ($row) {
            return $row->tgl_mulai;
        })
        ->addColumn('tgl_target', function ($row) {
            return $row->tgl_target;
        })
        ->addColumn('id_group', function ($row) {
            return $row->id_group ?: '-';
        })
        ->addColumn('action', function ($row) {
            return '
                <a href="'.route('target.edit', $row->id).'" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger btnDelete" data-id="'.$row->id.'">Hapus</button>
            ';
        })
        ->rawColumns(['action'])
        ->make(true);
}
public function detail(Request $request, $id_group)
{
    // Ambil parameter id_santri dari query string
    $id_santri = $request->query('id_santri'); // Bisa juga pakai $request->input('id_santri')

    // Query untuk mengambil data berdasarkan filter id_group dan id_santri
    $targets = Target::with(['santri', 'kelas', 'surat', 'pengajar'])
        ->when($id_group, function ($query, $id_group) {
            return $query->where('id_group', $id_group);
        })
        ->when($id_santri, function ($query, $id_santri) {
            return $query->where('id_santri', $id_santri);
        })
        ->orderBy('id_surat')
        ->get();

    // Lakukan pengelompokan berdasarkan id_santri dan id_group
    $targets = $targets->groupBy(function ($item) {
        return $item->id_santri . '-' . $item->id_group;
    });

    return view('target.detail', compact('targets'));
}




}
