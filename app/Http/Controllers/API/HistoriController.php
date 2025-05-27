<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Histori;
use App\Models\Setoran;
use Illuminate\Http\Request;

class HistoriController extends Controller
{
    public function index()
    {
        // Ambil semua histori, lalu ambil histori terbaru per id_target
        $latestHistories = Histori::with(['santri.kelas', 'surat'])
            ->get()
            ->groupBy('id_target')
            ->map(function ($group) {
                return $group->sortByDesc('updated_at')->first();
            })
            ->values(); // reset index agar jadi array biasa

        $data = $latestHistories->map(function ($item) {
            return [
                'nama' => optional($item->santri)->nama,
                'kelas' => optional(optional($item->santri)->kelas)->nama_kelas,
                'id_surat' => optional($item->surat)->id_surat,
                'nama_surat' => optional($item->surat)->nama_surat,
                'ayat' => optional($item->surat) ? $item->surat->jumlah_ayat . ' ayat' : '-',
                'persentase' => $item->persentase . '%',
                'status' => $item->status,
                'status_label' => match ($item->status) {
                    0 => 'Belum Mulai',
                    1 => 'Proses',
                    2 => 'Selesai',
                    3 => 'Terlambat',
                    default => 'Tidak Diketahui',
                },
                'nilai' => $item->nilai ?? '-', // Jika null, tampilkan strip
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function showBySantri($id_santri)
    {
        $histori = Histori::with(['targets.surat'])
            ->where('id_santri', $id_santri)
            ->get()
            ->map(function ($h) {
                return [
                    'id_histori' => $h->id_histori,
                    'id_setoran' => $h->id_setoran,
                    'id_target' => $h->id_target,
                    'id_surat' => $h->surat->id_surat,
                    'nama_surat' => $h->targets->surat->nama_surat ?? '-',
                    'jumlah_ayat' => $h->targets->jumlah_ayat_target,
                    'persentase' => $h->persentase,
                    'tgl_target' => $h->targets->tgl_target,
                    'status' => $h->status,
                    'nilai' => $h->nilai ?? 0,
                    'nilai_remedial' => $h->nilai_remedial ?? 0,
                ];
            });

        return response()->json($histori);
    }

    public function inputNilai(Request $request, $id_histori)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'nilai_remedial' => 'nullable|numeric|min:0|max:100',
        ]);

        $histori = Histori::findOrFail($id_histori);

        $histori->nilai = $request->nilai;

        // Cuma update remedial kalau nilai di bawah KKM
        if ($request->nilai <= 75) {
            $histori->nilai_remedial = $request->nilai_remedial ?? null;
        } else {
            $histori->nilai_remedial = null; // Reset kalau sebelumnya ada nilai remedial
        }

        $histori->save();

        if ($request->has('nilai_remedial') && $request->nilai_remedial !== null) {
            return response()->json([
                'success' => true,
                'message' => 'Nilai remedial berhasil ditambahkan ke histori!'
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Nilai berhasil ditambahkan ke histori!']);
    }

    public function getPreview($id_target)
    {
        $data = Histori::where('id_target', $id_target)
            ->whereNotNull('nilai') // Hanya ambil histori dengan nilai yang tidak null
            ->orderBy('updated_at', 'asc')
            ->get(['updated_at', 'nilai'])
            ->map(function ($item) {
                return [
                    'updated_at' => $item->updated_at->format('d-m-Y'),
                    'nilai' => $item->nilai
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }


    public function updateHistori(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|integer',
            'persentase' => 'required|numeric',
            'nilai' => 'required|numeric',
            'id_setoran' => 'required|array',
            'id_setoran.*' => 'integer',
        ]);

        $histori = Histori::findOrFail($id);
        $target = $histori->targets;

        if (!$target) {
            return response()->json(['success' => false, 'message' => 'Target tidak ditemukan.'], 404);
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

        $status = Histori::determineStatus(
            $totalAyatDisetorkan,
            $jumlahAyatTarget,
            $target->tgl_target,
            $tglSetoranTerakhir
        );

        $persentaseBaru = number_format(($totalAyatDisetorkan / max(1, $jumlahAyatTarget)) * 100, 2);
        $santriId = $target->santri_id;

        Histori::updateOrCreate(
            ['id_target' => $target->id_target, 'id_santri' => $santriId],
            [
                'status' => $status,
                'persentase' => $persentaseBaru,
                'nilai' => $request->nilai,
                'id_setoran' => $setoranIds->first(),
                'tgl_setoran' => $tglSetoranTerakhir,
            ]
        );

        return response()->json(['success' => true, 'message' => 'Histori berhasil diperbarui']);
    }
}
