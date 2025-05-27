<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\Infaq;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class InfaqController extends Controller
{
    // List infaq berdasarkan tanggal, default hari ini
    public function index(Request $request)
    {
        $tanggal = $request->input('tgl_infaq', Carbon::now()->toDateString());

        $semuaKelas = Kelas::all();

        $infaqs = Infaq::where('tgl_infaq', $tanggal)->get()->keyBy('id_kelas');

        return response()->json([
            'status' => 'success',
            'tanggal' => $tanggal,
            'kelasList' => $semuaKelas,
            'infaqs' => $infaqs,
        ]);
    }

    // Simpan data infaq baru
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'tgl_infaq' => 'required|date',
            'nominal_infaq' => 'required|integer|min:0',
        ]);

        // Cek duplikat
        $exists = Infaq::where('id_kelas', $validated['id_kelas'])
            ->where('tgl_infaq', $validated['tgl_infaq'])
            ->first();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data infaq untuk kelas dan tanggal ini sudah ada.'
            ], 422);
        }

        $infaq = Infaq::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data infaq berhasil ditambahkan.',
            'data' => $infaq,
        ], 201);
    }

    // Tampilkan data infaq tertentu (optional, kalau perlu)
    public function show($id)
    {
        $infaq = Infaq::find($id);
        if (!$infaq) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data infaq tidak ditemukan.'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $infaq,
        ]);
    }

    // Update data infaq
    public function update(Request $request, $id)
    {
        $infaq = Infaq::find($id);
        if (!$infaq) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data infaq tidak ditemukan.'
            ], 404);
        }

        $validated = $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'tgl_infaq' => 'required|date',
            'nominal_infaq' => 'required|integer|min:0',
        ]);

        // Cek duplikat selain data ini
        $exists = Infaq::where('id_kelas', $validated['id_kelas'])
            ->where('tgl_infaq', $validated['tgl_infaq'])
            ->where('id', '!=', $infaq->id)
            ->first();

        if ($exists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data infaq untuk kelas dan tanggal ini sudah ada.'
            ], 422);
        }

        $infaq->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data infaq berhasil diperbarui.',
            'data' => $infaq,
        ]);
    }

    // (Opsional) Hapus data infaq
    public function destroy($id)
    {
        $infaq = Infaq::find($id);
        if (!$infaq) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data infaq tidak ditemukan.'
            ], 404);
        }

        $infaq->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data infaq berhasil dihapus.'
        ]);
    }
}
