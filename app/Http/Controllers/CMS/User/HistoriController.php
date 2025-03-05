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
            ->select('historis.*');

        // Filter berdasarkan santri jika ada
        if ($request->filled('id_santri')) {
            $query->whereHas('santri', function ($q) use ($request) {
                $q->where('id_santri', $request->id_santri);
            });
        }

        // Menambahkan pencarian berdasarkan search_value
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

    if ($totalAyatDisetorkan >= $jumlahAyatTarget) {
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

    // Cari histori berdasarkan id_target
    $histori = Histori::where('id_target', $id_target)->firstOrFail();

    // Update nilai
    $histori->update([
        'nilai' => $request->nilai
    ]);

    return response()->json(['success' => true, 'message' => 'Nilai berhasil diperbarui!']);
}



}
