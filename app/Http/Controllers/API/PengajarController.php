<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pengajar;
use Exception;

class PengajarController extends Controller
{
    // Get all pengajar
    public function getAllPengajar()
    {
        try {
            $pengajar = Pengajar::all();

            return response()->json([
                'status' => 'success',
                'data' => $pengajar
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Get pengajar by ID
    public function getPengajarById($id)
    {
        try {
            // Cek apakah ID valid (harus angka)
            if (!filter_var($id, FILTER_VALIDATE_INT)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'ID harus berupa angka'
                ], 400);
            }

            $pengajar = Pengajar::find($id);

            if (!$pengajar) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pengajar tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $pengajar
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




