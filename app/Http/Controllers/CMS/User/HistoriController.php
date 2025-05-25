<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Histori;
use App\Models\Santri;
use App\Models\Setoran; // Pastikan Anda mengimpor model Setoran
use Illuminate\Http\Request;
use carbon\Carbon;

class HistoriController extends Controller
{
    public function index()
    {
        $histori = Histori::with(['santri', 'surat'])
                ->orderBy('id_surat')
                ->get();

        // Ambil daftar santri untuk filter
        $santris = Santri::all();

        return view('histori.show', compact('histori', 'santris'));
    }

    public function fnGetData(Request $request)
    {
        $query = Histori::with(['santri.kelas', 'surat', 'target'])
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
                    $q->where('nama', 'like', "%$searchValue%")
                      ->orWhereHas('kelas', function ($q2) use ($searchValue) {
                          $q2->where('nama_kelas', 'like', "%$searchValue%");
                      });
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
            ->filterColumn('nama_kelas', function ($query, $keyword) {
            $query->whereHas('kelas', function ($q) use ($keyword) {
                $q->where('nama_kelas', 'like', "%$keyword%");
            });
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
        'status' => 'required|integer',
        'persentase' => 'required|numeric',
        'nilai' => 'required|numeric',
        'id_setoran' => 'required|array',
        'id_setoran.*' => 'integer', // Pastikan setiap id_setoran bertipe integer
    ]);

    $histori = Histori::findOrFail($id);
    $target = $histori->target;

    if (!$target) {
        return redirect()->back()->with('error', 'Target tidak ditemukan.');
    }

    $jumlahAyatTarget = $target->jumlah_ayat_target;
    $totalAyatDisetorkan = 0;
    $setoranIds = collect();
    $tglSetoranTerakhir = null;

    $setorans = Setoran::whereIn('id_setoran', $request->id_setoran)->get();

    foreach ($setorans as $setoran) {
        $totalAyatDisetorkan += ($setoran->jumlah_ayat_end - $setoran->jumlah_ayat_start + 1);
        $setoranIds->push($setoran->id_setoran);
        if (!$tglSetoranTerakhir || $setoran->tgl_setoran > $tglSetoranTerakhir) {
            $tglSetoranTerakhir = $setoran->tgl_setoran;
        }
    }

    // Cek status otomatis dengan method determineStatus
    $status = Histori::determineStatus($totalAyatDisetorkan, $jumlahAyatTarget, $tglTarget = Carbon::parse($target->tgl_target), $tglSetoranTerakhir);

    $persentaseBaru = number_format(($totalAyatDisetorkan / max(1, $jumlahAyatTarget)) * 100, 2);

    // Pastikan id_santri terbaru sesuai target yang baru
    $santriId = $target->id_santri;

    Histori::updateOrCreate(
        ['id_target' => $target->id_target],
        [
            'status' => $status,
            'persentase' => $persentaseBaru,
            'nilai' => $request->nilai,
            'id_setoran' => $setoranIds->first(),
            'tgl_setoran' => $tglSetoranTerakhir,
        ]
    );

    return redirect()->route('histori.index')->with('success', 'Histori berhasil diperbarui');
}

public function updateNilai(Request $request, $id_target)
{
    $request->validate([
        'nilai' => 'required|numeric|min:0',
    ]);

    // Ambil histori terakhir berdasarkan id_target
    $lastHistori = Histori::where('id_target', $id_target)->orderBy('updated_at', 'desc')->first();

    // Jika histori terakhir ditemukan, gunakan data yang ada, tetapi buat histori baru
    $histori = new Histori();
    $histori->id_target = $id_target;
    $histori->nilai = $request->nilai;
    $histori->persentase = $lastHistori ? $lastHistori->persentase : 0;
    $histori->id_santri = $lastHistori ? $lastHistori->id_santri : null;
    $histori->id_surat = $lastHistori ? $lastHistori->id_surat : null;
    $histori->id_kelas = $lastHistori ? $lastHistori->id_kelas : null;
    $histori->id_setoran = $lastHistori ? $lastHistori->id_setoran : null;

    // Status tetap sama dengan histori terakhir, jika ada
    $histori->status = $lastHistori ? $lastHistori->status : 0;

    $histori->save();

    return response()->json(['success' => true, 'message' => 'Nilai berhasil ditambahkan ke histori!']);
}

public function getPreview($id)
{
    // Ambil histori berdasarkan id_target dan urutkan berdasarkan updated_at
    $data = Histori::where('id_target', $id)
        ->whereNotNull('nilai') // Hanya ambil data yang nilai-nya tidak null
        ->orderBy('updated_at', 'asc') // Urutkan berdasarkan tanggal perubahan
        ->get(['updated_at', 'nilai']); // Ambil nilai dan tanggal perubahan

    // Format tanggal dengan waktu
    $data = $data->map(function ($item) {
        return [
            'updated_at' => $item->updated_at->format('d-m-Y    '), // Format: Hari-Bulan-Tahun Jam:Menit:Detik
            'nilai' => $item->nilai
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $data
    ]);
}

}
