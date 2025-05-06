<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Target;
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
            $santri = Santri::with(['kelas', 'targets'])->get();

            // Format foto santri
            $this->formatFotoSantri($santri);

            // Menambahkan id_group ke setiap santri berdasarkan target yang ada
            $santri->map(function ($item) {
                // Ambil id_group dari target pertama yang terhubung dengan santri
                $item->id_group = $item->targets->first()->id_group ?? null;
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

            $santri = Santri::with('kelas', 'targets')->find($id);

            if (!$santri) {
                return response()->json([
                    'status' => false,
                    'message' => 'Santri tidak ditemukan'
                ], 404);
            }

            // Format URL foto
            $this->formatFotoSantri($santri);

            // Menambahkan id_group ke santri berdasarkan target yang ada
            $santri->id_group = $santri->targets->first()->id_group ?? null;

            return response()->json([
                'status' => true,
                'message' => 'Berhasil mengambil data santri',
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
