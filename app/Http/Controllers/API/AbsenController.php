<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Santri;
use App\Models\Absen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AbsenController extends Controller
{
    // List kelas dengan jumlah santri (mirip index di web)
    public function index(Request $request)
    {
        $kelas = Kelas::select('id_kelas', 'nama_kelas')
            ->withCount('santri')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $kelas,
        ]);
    }

    // Detail absensi per kelas dan tanggal
    public function detail($id_kelas, Request $request)
    {
        $tanggal = $request->query('tgl_absen', now()->toDateString());
        $kelas = Kelas::find($id_kelas);

        if (!$kelas) {
            return response()->json(['status' => 'error', 'message' => 'Kelas tidak ditemukan'], 404);
        }

        $santris = Santri::with(['absens' => function ($q) use ($tanggal) {
            $q->whereDate('tgl_absen', $tanggal);
        }])
            ->where('id_kelas', $id_kelas)
            ->select('id_santri', 'nisn', 'nama', 'jenis_kelamin')
            ->get();

        return response()->json([
            'status' => 'success',
            'kelas' => $kelas,
            'tanggal' => $tanggal,
            'santris' => $santris,
        ]);
    }

    // Ambil daftar santri berdasarkan kelas (untuk API dropdown/select)
    public function getSantriByKelas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id' => 'required|exists:kelas,id_kelas',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $santris = Santri::where('id_kelas', $request->kelas_id)->get(['id_santri', 'nama']);

        return response()->json([
            'status' => 'success',
            'data' => $santris,
        ]);
    }

    // Simpan absensi baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id'   => 'required|exists:kelas,id_kelas',
            'santri_id'  => 'required|exists:santris,id_santri',
            'tgl_absen'  => 'required|date',
            'status'     => 'required|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cek apakah sudah ada absensi pada tanggal tersebut
        $exists = Absen::where('id_santri', $request->santri_id)
            ->whereDate('tgl_absen', $request->tgl_absen)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Absensi sudah ada untuk tanggal tersebut.'
            ], 409);
        }

        $santri = Santri::find($request->santri_id);

        $absen = Absen::create([
            'id_kelas'  => $request->kelas_id,
            'id_santri' => $santri->id_santri,
            'nisn'      => $santri->nisn,
            'tgl_absen' => $request->tgl_absen,
            'status'    => $request->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil disimpan.',
            'data' => $absen,
        ], 201);
    }

    public function simpan(Request $request)
    {
        $validated = $request->validate([
            'tgl_absen' => 'required|date',
            'data' => 'required|array',
            'data.*.id_kelas' => 'required|exists:kelas,id_kelas',
            'data.*.id_santri' => 'required|exists:santris,id_santri',
            'data.*.nisn' => 'required|exists:santris,nisn',
            'data.*.status' => 'required|in:1,2,3,4',
        ]);

        foreach ($validated['data'] as $absen) {
            Absen::updateOrCreate(
                [
                    'id_kelas' => $absen['id_kelas'],
                    'id_santri' => $absen['id_santri'],
                    'nisn' => $absen['nisn'],
                ],
                [
                    'tgl_absen' => $validated['tgl_absen'],
                    'status' => $absen['status'],
                ]
            );
        }

        return response()->json(['message' => 'Absensi berhasil disimpan'], 200);
    }

    // Update absensi
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'kelas_id'  => 'required|exists:kelas,id_kelas',
            'santri_id' => 'required|exists:santris,id_santri',
            'tgl_absen' => 'required|date',
            'status'    => 'required|in:1,2,3,4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $absen = Absen::find($id);
        if (!$absen) {
            return response()->json(['status' => 'error', 'message' => 'Absensi tidak ditemukan'], 404);
        }

        // Cek apakah absensi sudah ada pada tanggal dan santri tersebut (kecuali record ini)
        $exists = Absen::where('id_santri', $request->santri_id)
            ->whereDate('tgl_absen', $request->tgl_absen)
            ->where('id', '!=', $absen->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Absensi sudah ada untuk tanggal tersebut.'
            ], 409);
        }

        $santri = Santri::find($request->santri_id);

        $absen->update([
            'id_kelas'  => $request->kelas_id,
            'id_santri' => $santri->id_santri,
            'nisn'      => $santri->nisn,
            'tgl_absen' => $request->tgl_absen,
            'status'    => $request->status,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil diupdate.',
            'data' => $absen,
        ]);
    }

    // Hapus absensi
    public function destroy($id)
    {
        $absen = Absen::find($id);
        if (!$absen) {
            return response()->json(['status' => 'error', 'message' => 'Absensi tidak ditemukan'], 404);
        }

        $absen->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi berhasil dihapus.'
        ]);
    }
}
