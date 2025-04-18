<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Target;
use App\Models\Santri;
use App\Models\Surat;
use App\Models\Histori;
use Illuminate\Support\Facades\Validator;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

class TargetController extends Controller
{
    private function getUserFromToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));
            return [
                'id' => $decoded->sub,
                'role' => $decoded->role
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    public function index()
    {
        $targets = Target::with(['santri', 'kelas', 'pengajar', 'surat'])
            ->get()
            ->groupBy(function ($item) {
                return $item->id_santri . '-' . $item->id_group;
            });

        if ($targets->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada target yang tersedia'
            ], 404);
        }

        $result = [];
        $no = 1;

        foreach ($targets as $group) {
            $first = $group->first();
            $result[] = [
                'no' => $no++,
                'nama_santri' => $first->santri->nama ?? '-',
                'kelas' => $first->kelas->nama_kelas ?? '-',
                'pengajar' => $first->pengajar->nama ?? '-',
                'target_group' => 'Target ' . $first->id_group,
                'tgl_mulai' => $first->tgl_mulai,
                'tgl_target' => $first->tgl_target,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function show($id_target)
{
    $target = Target::find($id_target);

    if (!$target) {
        return response()->json([
            'success' => false,
            'message' => 'Target tidak ditemukan'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'data' => $target
    ]);
}

public function getBySantriGroup($id_santri, $id_group)
{
    $targets = Target::with('surat', 'santri')
        ->where('id_santri', $id_santri)
        ->where('id_group', $id_group)
        ->get();

    if ($targets->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada target ditemukan untuk santri dan group ini.'
        ], 404);
    }

    // Hanya ambil data yang dibutuhkan seperti di detail.php (tanpa aksi)
    $result = $targets->map(function ($target) {
        return [
            'nama_surat' => optional($target->surat)->nama_surat ?? 'Tidak Ditemukan',
            'ayat_awal' => $target->jumlah_ayat_target_awal ?? '0',
            'ayat_akhir' => $target->jumlah_ayat_target ?? '0',
        ];
    });

    return response()->json([
        'success' => true,
        'data' => $result
    ]);
}




    public function store(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $user = $this->getUserFromToken($token);

        if (!$user || $user['role'] !== 'pengajar') {
            return response()->json(['success' => false, 'message' => 'Hanya pengajar yang dapat menambahkan target'], 403);
        }

        $validator = Validator::make($request->all(), [
            'id_santri' => 'required|exists:santris,id_santri',
            'id_surat' => 'required|exists:surats,id_surat',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'tgl_mulai' => 'required|date',
            'jumlah_ayat_target_awal' => 'required|integer|min:1',
            'jumlah_ayat_target' => 'required|integer|min:1',
            'tgl_target' => 'required|date',
            'id_group' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        $santri = Santri::find($request->id_santri);
        if (!$santri || $santri->id_kelas != $request->id_kelas) {
            return response()->json([
                'message' => 'Santri tidak terdaftar di kelas yang dipilih. Santri tersebut terdaftar di kelas ' . ($santri?->kelas?->nama_kelas ?? 'tidak diketahui') . '.',
            ], 422);
        }

        $surat = Surat::find($request->id_surat);

        if ($request->jumlah_ayat_target_awal > $request->jumlah_ayat_target) {
            return response()->json(['success' => false, 'message' => 'Ayat awal tidak boleh lebih besar dari jumlah target'], 422);
        }

        if ($request->jumlah_ayat_target > $surat->jumlah_ayat) {
            return response()->json(['success' => false, 'message' => 'Jumlah ayat target melebihi jumlah ayat dalam surat'], 422);
        }

        $jumlah_ayat_target_end = $request->jumlah_ayat_target_awal + $request->jumlah_ayat_target - 1;

        $overlapTarget = Target::where('id_santri', $request->id_santri)
            ->where('id_group', $request->id_group)
            ->where('id_surat', $request->id_surat)
            ->where(function ($query) use ($request, $jumlah_ayat_target_end) {
                $query->whereBetween('jumlah_ayat_target_awal', [$request->jumlah_ayat_target_awal, $jumlah_ayat_target_end])
                    ->orWhereBetween('jumlah_ayat_target', [$request->jumlah_ayat_target_awal, $jumlah_ayat_target_end]);
            })->exists();

        if ($overlapTarget) {
            return response()->json(['success' => false, 'message' => 'Rentang ayat target tumpang tindih dengan target yang sudah ada'], 422);
        }

        $target = Target::create([
            'id_santri' => $request->id_santri,
            'id_pengajar' => explode('_', $user['id'])[1], // ambil id_pengajar dari "pengajar_1"
            'id_kelas' => $request->id_kelas,
            'id_surat' => $request->id_surat,
            'tgl_mulai' => $request->tgl_mulai,
            'jumlah_ayat_target_awal' => $request->jumlah_ayat_target_awal,
            'jumlah_ayat_target' => $request->jumlah_ayat_target,
            'tgl_target' => $request->tgl_target,
            'id_group' => $request->id_group,
        ]);

        $today = now();
        $status = ($today->greaterThan($target->tgl_target)) ? 3 : 0;

        Histori::create([
            'id_santri' => $target->id_santri,
            'id_surat' => $target->id_surat,
            'id_kelas' => $target->id_kelas,
            'persentase' => 0.00,
            'status' => $status,
            'id_target' => $target->id_target,
            'nilai' => null,
        ]);

        return response()->json(['success' => true, 'message' => 'Target berhasil ditambahkan', 'data' => $target]);
    }
    public function update(Request $request, $id_target)
{
    $authHeader = $request->header('Authorization');
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);
    $user = $this->getUserFromToken($token);

    if (!$user || $user['role'] !== 'pengajar') {
        return response()->json(['success' => false, 'message' => 'Hanya pengajar yang dapat mengubah target'], 403);
    }

    $target = Target::findOrFail($id_target);

    $validator = Validator::make($request->all(), [
        'id_santri' => 'required|exists:santris,id_santri',
        'id_kelas' => 'required|exists:kelas,id_kelas',
        'id_surat' => 'required|exists:surats,id_surat',
        'tgl_mulai' => 'required|date',
        'jumlah_ayat_target_awal' => 'required|integer|min:1',
        'jumlah_ayat_target' => 'required|integer|min:1',
        'tgl_target' => 'required|date',
        'id_group' => 'nullable|integer'
    ]);

    $santri = Santri::find($request->id_santri);
        if (!$santri || $santri->id_kelas != $request->id_kelas) {
            return response()->json([
                'message' => 'Santri tidak terdaftar di kelas yang dipilih. Santri tersebut terdaftar di kelas ' . ($santri?->kelas?->nama_kelas ?? 'tidak diketahui') . '.',
            ], 422);
        }

    if ($validator->fails()) {
        return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
    }

    $surat = Surat::findOrFail($request->id_surat);

    if ($request->jumlah_ayat_target_awal > $request->jumlah_ayat_target) {
        return response()->json(['success' => false, 'message' => 'Ayat awal tidak boleh lebih besar dari jumlah target'], 422);
    }

    if ($request->jumlah_ayat_target > $surat->jumlah_ayat) {
        return response()->json(['success' => false, 'message' => 'Jumlah ayat target melebihi jumlah ayat surat'], 422);
    }

    $jumlah_ayat_target_end = $request->jumlah_ayat_target_awal + $request->jumlah_ayat_target - 1;

    $overlapTarget = Target::where('id_santri', $request->id_santri)
        ->where('id_group', $request->id_group)
        ->where('id_surat', $request->id_surat)
        ->where(function ($query) use ($request, $jumlah_ayat_target_end) {
            $query->whereBetween('jumlah_ayat_target_awal', [$request->jumlah_ayat_target_awal, $jumlah_ayat_target_end])
                ->orWhereBetween('jumlah_ayat_target', [$request->jumlah_ayat_target_awal, $jumlah_ayat_target_end]);
        })
        ->where('id_target', '!=', $id_target)
        ->exists();

    if ($overlapTarget) {
        return response()->json(['success' => false, 'message' => 'Rentang ayat target tumpang tindih'], 422);
    }

    // Update target
    $target->update([
        'id_santri' => $request->id_santri,
        'id_kelas' => $request->id_kelas,
        'id_surat' => $request->id_surat,
        'tgl_mulai' => $request->tgl_mulai,
        'jumlah_ayat_target_awal' => $request->jumlah_ayat_target_awal,
        'jumlah_ayat_target' => $request->jumlah_ayat_target,
        'tgl_target' => $request->tgl_target,
        'id_group' => $request->id_group,
    ]);

    // Update histori
    $histori = Histori::where('id_target', $target->id_target)->first();
    if ($histori) {
        $totalAyatDisetorkan = \App\Models\Setoran::where('id_target', $target->id_target)
            ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

        $persentaseBaru = number_format(($totalAyatDisetorkan / max(1, $request->jumlah_ayat_target)) * 100, 2);

        $status = Histori::determineStatus($totalAyatDisetorkan, $request->jumlah_ayat_target, $request->tgl_target, $histori->tgl_setoran);

        $histori->update([
            'id_santri' => $target->id_santri,
            'id_surat' => $target->id_surat,
            'id_kelas' => $target->id_kelas,
            'persentase' => $persentaseBaru,
            'status' => $status,
            'tgl_target' => $target->tgl_target,
        ]);
    }

    return response()->json(['success' => true, 'message' => 'Target berhasil diperbarui', 'data' => $target]);
}
public function destroy(Request $request, $id_target)
{
    $authHeader = $request->header('Authorization');
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);
    $user = $this->getUserFromToken($token);

    if (!$user || $user['role'] !== 'pengajar') {
        return response()->json(['success' => false, 'message' => 'Hanya pengajar yang dapat menghapus target'], 403);
    }

    try {
        $target = Target::findOrFail($id_target);
        $target->delete();

        return response()->json(['success' => true, 'message' => 'Target berhasil dihapus']);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Gagal menghapus target'], 500);
    }
}
public function destroyBySantriGroup(Request $request, $id_santri, $id_group)
{
    $authHeader = $request->header('Authorization');
    if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
        return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
    }

    $token = str_replace('Bearer ', '', $authHeader);
    $user = $this->getUserFromToken($token);

    if (!$user || $user['role'] !== 'pengajar') {
        return response()->json(['success' => false, 'message' => 'Hanya pengajar yang dapat menghapus target'], 403);
    }

    try {
        $deleted = Target::where('id_santri', $id_santri)
            ->where('id_group', $id_group)
            ->delete();

        if ($deleted > 0) {
            return response()->json(['success' => true, 'message' => 'Target berhasil dihapus berdasarkan id_santri dan id_group']);
        } else {
            return response()->json(['success' => false, 'message' => 'Tidak ada target yang cocok ditemukan'], 404);
        }

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Gagal menghapus target'], 500);
    }
}


}
