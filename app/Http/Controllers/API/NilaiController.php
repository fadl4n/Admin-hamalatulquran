<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\Target;
use App\Models\Setoran;
use App\Models\Histori;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * Endpoint untuk mendapatkan daftar santri dengan pencarian.
     */
    public function index(Request $request)
    {
        $query = Santri::with('kelas');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%")
                    ->orWhereHas('kelas', function ($kelasQuery) use ($search) {
                        $kelasQuery->where('nama_kelas', 'like', "%$search%");
                    })
                    ->orWhereHas('targets', function ($targetQuery) use ($search) {
                        if (preg_match('/^Target (\d+)$/i', $search, $matches)) {
                            $idGroup = $matches[1];
                            $targetQuery->where('id_group', $idGroup);
                        } elseif (is_numeric($search)) {
                            $targetQuery->where('id_group', $search);
                        } else {
                            $targetQuery->where('id_group', 'like', "%$search%");
                        }
                    });
            });
        }

        $santris = $query->get();

        // Ambil hanya data yang diperlukan untuk tampilan tabel
        $data = $santris->map(function ($santri) {
            return [
                'nama' => $santri->nama,
                'nisn' => $santri->nisn,
                'kelas' => $santri->kelas->nama_kelas ?? '-'
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Endpoint untuk menampilkan detail nilai hafalan dan murojaah untuk seorang santri berdasarkan id_group.
     */
    public function show($idSantri, $idGroup)
    {
        $santri = Santri::with('kelas')->findOrFail($idSantri);

        $targets = Target::where('id_santri', $idSantri)
            ->where('id_group', $idGroup)
            ->get();

        $hafalan = [];
        $murojaah = [];

        foreach ($targets as $target) {
            $namaSurat = $target->surat->nama_surat;

            $nilaiHafalan = Setoran::where('id_target', $target->id_target)->avg('nilai');
            $nilaiMurojaah = Histori::where('id_target', $target->id_target)->avg('nilai');

            $hafalan[] = [
                'surat' => $namaSurat,
                'nilai' => number_format($nilaiHafalan ?? 0, 2)
            ];

            $murojaah[] = [
                'surat' => $namaSurat,
                'nilai' => number_format($nilaiMurojaah ?? 0, 2)
            ];
        }

        return response()->json([
            'santri' => [
                'nama' => $santri->nama,
                'nisn' => $santri->nisn,
                'kelas' => $santri->kelas->nama_kelas ?? '-'
            ],
            'hafalan' => $hafalan,
            'murojaah' => $murojaah
        ]);
    }

    /**
     * Endpoint untuk datatable nilai versi API.
     */
    public function fnGetData(Request $request)
    {
        $santris = Santri::with('kelas');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $santris->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%")
                    ->orWhereHas('kelas', function ($kelasQuery) use ($search) {
                        $kelasQuery->where('nama_kelas', 'like', "%$search%");
                    });
            });
        }
        $santrisPaginated = $santris; // simpan pagination info
        $data = collect($santris->items())->map(function ($santri) {
            return [
                'nama' => $santri->nama,
                'nisn' => $santri->nisn,
                'kelas' => $santri->kelas->nama_kelas ?? '-',
                'id_santri' => $santri->id_santri
            ];
        });

        return response()->json([
            'data' => $data,
            'recordsTotal' => $santrisPaginated->total(),
            'recordsFiltered' => $santrisPaginated->total()
        ]);


    }
}
