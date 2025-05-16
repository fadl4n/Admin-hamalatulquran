<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kelas;
use Exception;

class KelasController extends Controller
{
    // Get all Kelas with santri count
    public function getAllKelas()
    {
        try {
            $kelas = Kelas::withCount('santri')->get();

            return response()->json([
                'status' => 'success',
                'data' => $kelas
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get Kelas by ID (with santri count too)
    public function getKelasById($id)
    {
        try {
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID harus berupa angka'
                ], 400);
            }

            $kelas = Kelas::withCount('santri')->find($id);

            if (!$kelas) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kelas tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $kelas
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
