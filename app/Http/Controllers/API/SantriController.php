<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use Exception;

class SantriController extends Controller
{
    // Helper untuk format URL foto
    private function formatFotoSantri($santri)
    {
        $santri->map(function ($item) {
            if ($item->foto_santri) {
                $item->foto_santri = asset('storage/' . $item->foto_santri);
                $item->foto_santri = str_replace("127.0.0.1", "10.0.2.2", $item->foto_santri); // Emulator fix
            }
        });
    }

    // Get all santri
    public function getAllSantri()
    {
        try {
            $santri = Santri::with('kelas')->get();

            $this->formatFotoSantri($santri);

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

            $santri = Santri::with('kelas')->find($id);

            if (!$santri) {
                return response()->json([
                    'status' => false,
                    'message' => 'Santri tidak ditemukan'
                ], 404);
            }

            // Format URL foto
            if ($santri->foto_santri) {
                $santri->foto_santri = asset('storage/' . $santri->foto_santri);
                $santri->foto_santri = str_replace("127.0.0.1", "10.0.2.2", $santri->foto_santri);
            }

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
            $santri = Santri::where('id_kelas', $id)->get();

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
}
