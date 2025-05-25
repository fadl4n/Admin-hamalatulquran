<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SantriResource;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Target;
use App\Models\Histori;
use Exception;

class SantriController extends Controller
{
    // Helper untuk format URL foto
    private function formatFotoSantri($santri)
    {
        if (is_iterable($santri)) {
            foreach ($santri as $item) {
                $this->formatFotoSantri($item);
            }
        } elseif ($santri) {
            $foto = (string) $santri->foto_santri;

            if ($foto !== '' && !str_starts_with($foto, 'http://') && !str_starts_with($foto, 'https://')) {
                $santri->foto_santri = asset('storage/' . $foto);
            } else {
                $santri->foto_santri = $foto;
            }

            if ($santri->foto_santri !== null && is_string($santri->foto_santri)) {
                $santri->foto_santri = str_replace("127.0.0.1", "10.0.2.2", $santri->foto_santri);
            }
        }
    }

    public function countAktif()
    {
        $jumlah = Santri::where('status', 1)->count();

        return response()->json([
            'status' => 'success',
            'jumlah' => $jumlah
        ]);
    }

    // Get all santri
    public function getAllSantri()
    {
        try {
            // Ambil data santri dengan relasi ke kelas dan target
            $santri = Santri::with(['kelas', 'target'])->get();

            // Format foto santri
            $this->formatFotoSantri($santri);

            // Menambahkan id_group ke setiap santri berdasarkan target yang ada
            $santri->map(function ($item) {
                // Ambil id_group dari target pertama yang terhubung dengan santri
                $item->id_group = $item->target->first()->id_group ?? null;
            });

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil semua data santri',
                'data' => $santri
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get santri by ID
    public function getSantriById($id)
    {
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                return response()->json([
                    'status' => false,
                    'message' => 'ID harus berupa angka'
                ], 400);
            }

            $santri = Santri::with('kelas', 'target')->find($id);

            if (!$santri) {
                return response()->json([
                    'status' => false,
                    'message' => 'Santri tidak ditemukan'
                ], 404);
            }

            // Format URL foto
            $this->formatFotoSantri($santri);

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil data santri',
                'data' => new SantriResource($santri)
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get santri by kelas
    public function getSantriByKelas($id)
    {
        try {
            $santri = Santri::with('kelas')->where('id_kelas', $id)->get();

            $this->formatFotoSantri($santri);

            if ($santri->isEmpty()) {
                return response()->json([
                    'status' => true,
                    'message' => 'Kelas masih kosong',
                    'data' => []
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'Data santri berdasarkan kelas',
                'data' => $santri
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getLaporanDetail($id)
    {
        $santri = Santri::with('kelas')->findOrFail($id);

        // Data target hafalan baru + nilai rata-rata setoran tiap target
        $targets = Target::where('id_santri', $id)
            ->with(['surat'])
            ->withAvg('setoran as nilai', 'nilai')
            ->get()
            ->map(function ($target) {
                $latestHistori = $target->histori->sortByDesc('created_at')->first();
                $persentase = $latestHistori?->persentase ?? 0;
                return [
                    'id_target' => $target->id_target,
                    'id_surat' => $target->id_surat,
                    'nama_surat' => $target->surat->nama_surat ?? '-',
                    'nilai' => ($persentase == 100) ? round($target->nilai ?? 0) : 0,
                ];
            });

        // Data murajaah dari histori (anggap histori punya kolom nilai juga)
        $murajaah = Histori::where('id_santri', $id)
            ->with('surat')
            ->get()
            ->map(function ($histori) {
                return [
                    'id_histori' => $histori->id_histori,
                    'id_target' => $histori->id_target,
                    'id_surat' => $histori->id_surat,
                    'nama_surat' => $histori->surat->nama_surat ?? '-',
                    'persentase' => $histori->persentase,
                    'nilai' => $histori->nilai ?? 0,
                ];
            });

        return response()->json([
            'santri' => new SantriResource($santri),
            'targets' => $targets,
            'murojaah' => $murajaah,
        ]);
    }

    public function getGroupFromTarget($id)
    {
        $target = Target::where('id_santri', $id)->first();

        if (!$target) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ditemukan target untuk santri ini'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id_group' => $target->id_group,
            ]
        ]);
    }
}
