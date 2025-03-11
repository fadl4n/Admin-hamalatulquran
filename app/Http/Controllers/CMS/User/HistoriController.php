<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Histori;
use App\Models\Santri;
use App\Models\Setoran; // Pastikan Anda mengimpor model Setoran
use Illuminate\Http\Request;

class HistoriController extends Controller
{
    public function index()
    {
        // Ambil semua data histori dengan relasi yang diperlukan
        $histori = Histori::with(['santri', 'surat'])->get();

        // Ambil daftar santri untuk filter
        $santris = Santri::all();

        return view('histori.show', compact('histori', 'santris'));
    }

    public function fnGetData(Request $request)
    {
        $query = Histori::with(['santri', 'surat', 'target'])
            ->whereIn('id_histori', function ($subquery) {
                $subquery->selectRaw('MAX(id_histori)')
                    ->from('historis')
                    ->groupBy('id_target');
            });

        // Filter berdasarkan id_santri jika ada
        if ($request->filled('id_santri')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('id_santri', $request->id_santri);
            });
        }

        // Pencarian berdasarkan search_value
        if ($request->has('search_value') && $request->search_value != '') {
            $searchValue = $request->search_value;
            $query->where(function ($query) use ($searchValue) {
                $query->whereHas('santri', function ($q) use ($searchValue) {
                    $q->where('nama', 'like', "%$searchValue%");
                })
                ->orWhereHas('surat', function ($q) use ($searchValue) {
                    $q->where('nama_surat', 'like', "%$searchValue%");
                })
                ->orWhereHas('target', function ($q) use ($searchValue) {
                    $q->where('id_group', 'like', "%$searchValue%");
                });
            });
        }

        return datatables()->eloquent($query)
            ->addColumn('nama', fn($histori) => $histori->santri->nama ?? '-')
            ->addColumn('kelas', fn($histori) => $histori->santri->kelas->nama_kelas ?? '-')
            ->addColumn('nama_surat', fn($histori) => $histori->surat->nama_surat ?? '-')
            ->addColumn('jumlah_ayat_start', fn($histori) => $histori->target->setoran()->min('jumlah_ayat_start') ?? 0)
            ->addColumn('jumlah_ayat_end', fn($histori) => $histori->target->setoran()->max('jumlah_ayat_end') ?? 0)
            ->addColumn('ayat', fn($histori) => $histori->target->setoran()->min('jumlah_ayat_start') . ' - ' . $histori->target->setoran()->max('jumlah_ayat_end'))
            ->addColumn('status', fn($histori) => $histori->status)
            ->addColumn('persentase', function ($histori) {
                $totalAyat = max(1, $histori->target->jumlah_ayat_target - $histori->target->jumlah_ayat_target_awal + 1);
                $ayatEnd = $histori->target->setoran()->max('jumlah_ayat_end');
                return round(($ayatEnd / $totalAyat) * 100) . '%';
            })
            ->addColumn('nilai', fn($histori) => $histori->nilai)
            ->addColumn('aksi', function($histori) {
                return '<button class="btn btn-primary btn-sm edit-nilai" data-id="' . $histori->id_target . '" data-nilai="' . $histori->nilai . '"><i class="fas fa-edit"></i></button>';
            })
            ->make(true);
    }


    public function updateHistori(Request $request, $id)
{
    $request->validate([
        'status' => 'required|string',
        'persentase' => 'required|numeric',
        'nilai' => 'required|numeric',
        'id_setoran' => 'required|array',
    ]);

    $histori = Histori::findOrFail($id);
    $target = $histori->target;  // Mengambil target terkait histori
    $jumlahAyatTarget = $target->jumlah_ayat_target;
    $totalAyatDisetorkan = 0;
    $setoranIds = [];
    $tglSetoranTerakhir = null;

    foreach ($request->id_setoran as $idSetoran) {
        $setoran = Setoran::findOrFail($idSetoran);
        $totalAyatDisetorkan += ($setoran->jumlah_ayat_end - $setoran->jumlah_ayat_start + 1);
        $setoranIds[] = $setoran->id_setoran;
        if (!$tglSetoranTerakhir || $setoran->tgl_setoran > $tglSetoranTerakhir) {
            $tglSetoranTerakhir = $setoran->tgl_setoran;
        }
    }

    // **Cek apakah sudah melewati tgl_target**
    $today = now()->toDateString(); // Ambil tanggal hari ini (format: Y-m-d)
    $status = 1; // Default status = Proses (1)

    if ($setoran->jumlah_ayat_end >= $jumlahAyatTarget) {
        $status = 2; // Selesai (2)
    } elseif ($target->tgl_target < $today) {
        $status = 3; // Terlambat (3) jika tgl_target sudah lewat
    }

    $persentaseBaru = number_format(($totalAyatDisetorkan / $jumlahAyatTarget) * 100, 2);

    // Pastikan id_santri terbaru sesuai target yang baru
    $santriId = $target->id_santri;

    Histori::updateOrCreate(
        ['id_target' => $target->id_target, 'id_santri' => $santriId],
        [
            'status' => $status, // Menggunakan status integer
            'persentase' => $persentaseBaru,
            'nilai' => $request->nilai,
            'id_setoran' => json_encode($setoranIds),
            'tgl_setoran' => $tglSetoranTerakhir,
        ]
    );

    return redirect()->route('histori.index')->with('success', 'Histori berhasil diperbarui');
}

public function updateNilai(Request $request, $id_target)
{
    $request->validate([
        'nilai' => 'required|numeric',
    ]);

    // Ambil histori terakhir berdasarkan id_target
    $lastHistori = Histori::where('id_target', $id_target)->orderBy('updated_at', 'desc')->first();

    // Jika histori terakhir ditemukan, update data histori terakhir saja
    if ($lastHistori) {
        $lastHistori->update([
            'nilai' => $request->nilai,
            'persentase' => $lastHistori->persentase,
            'status' => Histori::determineStatus(
                $lastHistori->nilai + $request->nilai,
                100, // Misalkan targetnya 100
                now()->toDateString(),
                $lastHistori->persentase
            )
        ]);
    } else {
        // Jika histori belum ada, buat baru
        $histori = new Histori();
        $histori->id_target = $id_target;
        $histori->nilai = $request->nilai;
        $histori->persentase = 0;
        $histori->status = Histori::determineStatus($request->nilai, 100, now()->toDateString(), 0);
        $histori->save();
    }

    return response()->json(['success' => true, 'message' => 'Nilai berhasil diperbarui di histori!']);
}




public function getPreview($id)
{
    // Ambil histori berdasarkan id_target dan urutkan berdasarkan updated_at
    $data = Histori::where('id_target', $id)
        ->orderBy('updated_at', 'asc') // Urutkan berdasarkan tanggal perubahan
        ->get(['updated_at', 'nilai']); // Ambil nilai dan tanggal perubahan

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}




}
