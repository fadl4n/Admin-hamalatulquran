<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use Exception;

class SantriController extends Controller
{
    // Get all santri
    public function getAllSantri()
    {
        try {
            $santri = Santri::all();

            return response()->json([
                'status' => 'success',
                'data' => $santri
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get santri by ID
    public function getSantriById($id)
    {
        try {
            // Cek apakah ID valid (harus angka)
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID harus berupa angka'
                ], 400);
            }

            $santri = Santri::with('kelas')->find($id);

            if (!$santri) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Santri tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $santri
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getSantriByKelas($id)
    {
        $santri = Santri::where('id_kelas', $id)->get();

        if ($santri->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Kelas masih kosong',
                'data' => []
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'Data santri berdasarkan kelas',
            'data' => $santri
        ]);
    }
}
