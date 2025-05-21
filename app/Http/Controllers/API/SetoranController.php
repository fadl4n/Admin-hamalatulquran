<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\{Santri, Target, Setoran, Histori};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class SetoranController extends Controller
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
    public function index(Request $request)
    {
        $setorans = Setoran::with(['santri', 'kelas', 'target'])
            ->get()
            ->groupBy(function ($item) {
                return $item->id_santri . '-' . $item->id_target;
            });

        $result = [];

        foreach ($setorans as $groupKey => $groupSetorans) {
            [$idSantri, $idTarget] = explode('-', $groupKey);
            $santri = $groupSetorans->first()->santri;
            $kelasList = $groupSetorans->pluck('kelas.nama_kelas')->unique()->implode(', ');
            $averagePersentase = round($groupSetorans->avg('persentase'), 2);
            $status = $averagePersentase >= 100 ? 'Selesai' : 'Proses';

            $result[] = [
                'id_santri' => $idSantri,
                'id_target' => $idTarget,
                'nama_santri' => $santri->nama,
                'nisn' => $santri->nisn,
                'kelas' => $kelasList,
                'target' => 'Target ' . ($groupSetorans->first()->target?->id_group ?? '-'),
                'status' => $status,
                'persentase' => $averagePersentase,
            ];
        }

        return response()->json([
            'success' => true,
            'message' => 'Daftar ringkasan setoran berhasil diambil',
            'data' => $result
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_santri' => 'required|exists:santris,id_santri',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_surat' => 'required|exists:surats,id_surat',
            'id_pengajar' => 'required|exists:pengajars,id_pengajar',
            'tgl_setoran' => 'required|date',
            'nilai' => 'required|numeric|min:0|max:100',
            'jumlah_ayat_start' => 'required|numeric',
            'jumlah_ayat_end' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $santri = Santri::find($request->id_santri);
        if (!$santri || $santri->id_kelas != $request->id_kelas) {
            return response()->json([
                'message' => 'Santri tidak terdaftar di kelas yang dipilih. Santri tersebut terdaftar di kelas ' . ($santri?->kelas?->nama_kelas ?? 'tidak diketahui') . '.',
            ], 422);
        }

        $target = Target::where([
            ['id_santri', '=', $request->id_santri],
            ['id_kelas', '=', $request->id_kelas],
            ['id_surat', '=', $request->id_surat],
        ])->first();

        if (!$target) {
            return response()->json(['message' => 'Target tidak ditemukan.'], 404);
        }

        // Validasi ayat
        if ($request->jumlah_ayat_start > $request->jumlah_ayat_end) {
            return response()->json(['message' => 'Ayat mulai tidak boleh lebih besar dari ayat akhir.'], 422);
        }

        if (
            $request->jumlah_ayat_end > $target->jumlah_ayat_target ||
            $request->jumlah_ayat_start > $target->jumlah_ayat_target ||
            $request->jumlah_ayat_start < $target->jumlah_ayat_target_awal ||
            $request->jumlah_ayat_end < $target->jumlah_ayat_target_awal
        ) {
            return response()->json([
                'message' => 'Ayat berada di luar rentang target: ' .
                    $target->jumlah_ayat_target_awal . ' - ' . $target->jumlah_ayat_target
            ], 422);
        }

        // Validasi tumpang tindih setoran sebelumnya
        $setorans = Setoran::where('id_target', $target->id_target)
            ->orderBy('jumlah_ayat_start')
            ->get();

        // Ambil semua setoran yang tumpang tindih
        $overlappingSetorans = Setoran::where('id_target', $target->id_target)
            ->where(function ($query) use ($request) {
                $query->whereBetween('jumlah_ayat_start', [$request->jumlah_ayat_start, $request->jumlah_ayat_end])
                    ->orWhereBetween('jumlah_ayat_end', [$request->jumlah_ayat_start, $request->jumlah_ayat_end])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('jumlah_ayat_start', '<=', $request->jumlah_ayat_start)
                            ->where('jumlah_ayat_end', '>=', $request->jumlah_ayat_end);
                    });
            })
            ->get();

        if ($overlappingSetorans->count() > 0) {
            $overlapRanges = $overlappingSetorans->map(function ($s) {
                return $s->jumlah_ayat_start . ' - ' . $s->jumlah_ayat_end;
            })->implode(', ');

            return response()->json([
                'message' => 'Ayat yang dimasukkan bertabrakan dengan setoran sebelumnya.',
                'detail' => 'Rentang ayat yang sudah disetorkan: ' . $overlapRanges,
            ], 422);
        }

        // Hitung persentase dan status
        $totalAyat = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);
        $persentase = number_format((($request->jumlah_ayat_end - $request->jumlah_ayat_start + 1) / $totalAyat) * 100, 2);
        $status = $persentase >= 100 ? '1' : '0';

        $setoran = Setoran::create([
            'id_target' => $target->id_target,
            'id_surat' => $request->id_surat,
            'id_santri' => $request->id_santri,
            'id_kelas' => $request->id_kelas,
            'id_pengajar' => $request->id_pengajar,
            'jumlah_ayat_start' => $request->jumlah_ayat_start,
            'jumlah_ayat_end' => $request->jumlah_ayat_end,
            'tgl_setoran' => $request->tgl_setoran,
            'status' => $status,
            'persentase' => $persentase,
            'nilai' => $request->nilai,
            'keterangan' => $request->keterangan,
        ]);

        // Update status semua setoran jika sudah selesai
        $setorans = Setoran::where('id_target', $target->id_target)->get();
        $totalAyatTercapai = $setorans->sum(fn($s) => $s->jumlah_ayat_end - $s->jumlah_ayat_start + 1);

        if ($totalAyatTercapai >= $target->jumlah_ayat_target) {
            foreach ($setorans as $s) {
                $s->status = '1';
                $s->save();
            }
        }

        // Update atau buat histori
        $histori = Histori::firstOrNew([
            'id_santri' => $request->id_santri,
            'id_target' => $target->id_target,
            'id_surat' => $request->id_surat,
            'id_kelas' => $request->id_kelas,
        ]);

        $persentaseBaru = number_format(($totalAyatTercapai / $totalAyat) * 100, 2);
        $histori->persentase = $persentaseBaru;
        $histori->id_setoran = $setoran->id_setoran;
        $histori->ayat = $totalAyatTercapai;

        if ($request->tgl_setoran > $target->tgl_target) {
            $histori->status = 3;
        } elseif ($persentaseBaru >= 99.99) {
            $histori->status = 2;
        } else {
            $histori->status = 1;
        }

        $histori->save();

        return response()->json([
            'message' => 'Setoran berhasil ditambahkan',
            'setoran' => $setoran,
            'histori' => $histori,
        ]);
    }

    public function update(Request $request, $id)
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

        $setoran = Setoran::findOrFail($id);

        $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'tgl_setoran' => 'required|date',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_surat' => 'required',
            'nilai' => 'required|numeric|min:0|max:100',
            'jumlah_ayat_start' => 'required|numeric',
            'jumlah_ayat_end' => 'required|numeric',
            'id_group' => 'nullable',
        ]);

        $santri = Santri::find($request->id_santri);
        if (!$santri || $santri->id_kelas != $request->id_kelas) {
            return response()->json([
                'message' => 'Santri tidak terdaftar di kelas yang dipilih. Santri tersebut terdaftar di kelas ' . ($santri?->kelas?->nama_kelas ?? 'tidak diketahui') . '.',
            ], 422);
        }

        $target = Target::where([
            ['id_santri', '=', $request->id_santri],
            ['id_kelas', '=', $request->id_kelas],
            ['id_surat', '=', $request->id_surat],
            ['id_group', '=', $request->id_group ?? $setoran->target->id_group]
        ])->first();

        if (!$target) {
            return response()->json(['message' => 'Target tidak ditemukan.'], 404);
        }

        if ($request->jumlah_ayat_start > $request->jumlah_ayat_end) {
            return response()->json(['message' => 'Jumlah ayat mulai tidak boleh lebih besar dari jumlah ayat akhir.'], 422);
        }

        if (
            $request->jumlah_ayat_end > $target->jumlah_ayat_target ||
            $request->jumlah_ayat_start > $target->jumlah_ayat_target ||
            $request->jumlah_ayat_start < $target->jumlah_ayat_target_awal ||
            $request->jumlah_ayat_end < $target->jumlah_ayat_target_awal
        ) {
            return response()->json(['message' => 'Jumlah ayat berada di luar rentang target.'], 422);
        }

        if ($request->tgl_setoran < $target->tgl_mulai) {
            return response()->json(['message' => 'Tanggal setoran tidak boleh sebelum tanggal mulai target.'], 422);
        }

        $setorans = Setoran::where('id_target', $target->id_target)
            ->where('id_setoran', '!=', $setoran->id_setoran)
            ->orderBy('jumlah_ayat_start')
            ->get();

        $valid = false;
        $previousEnd = 0;

        foreach ($setorans as $item) {
            if ($request->jumlah_ayat_start >= $previousEnd && $request->jumlah_ayat_start < $item->jumlah_ayat_start) {
                $valid = true;
                break;
            }
            $previousEnd = $item->jumlah_ayat_end;
        }

        if (!$valid && $request->jumlah_ayat_start > $previousEnd && $request->jumlah_ayat_start <= $target->jumlah_ayat_target) {
            $valid = true;
        }

        if (!$valid) {
            return response()->json(['message' => 'Jumlah ayat mulai tidak valid, terdapat tumpang tindih. Ayat akhir sebelumnya: ' . $previousEnd], 422);
        }

        $totalAyat = $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal;
        $persentase = (($request->jumlah_ayat_end - $request->jumlah_ayat_start + 1) / $totalAyat) * 100;
        $status = $persentase >= 100 ? '1' : '0';

        $setoran->update([
            'id_santri' => $request->id_santri,
            'tgl_setoran' => $request->tgl_setoran,
            'status' => $status,
            'id_kelas' => $request->id_kelas,
            'id_target' => $target->id_target,
            'id_surat' => $request->id_surat,
            'nilai' => $request->nilai,
            'jumlah_ayat_start' => $request->jumlah_ayat_start,
            'jumlah_ayat_end' => $request->jumlah_ayat_end,
            'id_pengajar' => explode('_', $user['id'])[1],
            'keterangan' => $request->keterangan,
            'persentase' => $persentase,
        ]);

        $histori = Histori::where('id_santri', $request->id_santri)
            ->where('id_target', $target->id_target)
            ->where('id_surat', $request->id_surat)
            ->first();

        $totalAyatDisetorkan = Setoran::where('id_target', $target->id_target)
            ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

        $totalAyatTarget = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);
        $persentaseBaru = number_format(($totalAyatDisetorkan / $totalAyatTarget) * 100, 2);

        $statusHistori = 1;
        if ($request->tgl_setoran > $target->tgl_target) {
            $statusHistori = 3;
        } elseif ($persentaseBaru >= 99.99) {
            $statusHistori = 2;
        }

        if ($histori) {
            $histori->update([
                'persentase' => $persentaseBaru,
                'id_setoran' => $setoran->id_setoran,
                'status' => $statusHistori,
            ]);
        } else {
            Histori::create([
                'id_santri' => $request->id_santri,
                'id_target' => $target->id_target,
                'id_surat' => $request->id_surat,
                'persentase' => $persentaseBaru,
                'id_setoran' => $setoran->id_setoran,
                'status' => $statusHistori,
            ]);
        }

        return response()->json([
            'message' => 'Setoran berhasil diperbarui',
            'data' => $setoran
        ], 200);
    }
    public function destroy($idSetoran, Request $request)
    {
        // Validasi token


        // Cek setoran
        $setoran = Setoran::find($idSetoran);
        if (!$setoran) {
            return response()->json(['success' => false, 'message' => 'Setoran tidak ditemukan.'], 404);
        }

        // Unlink histori jika ada
        $histori = Histori::where('id_setoran', $setoran->id_setoran)->first();
        if ($histori) {
            $histori->update(['id_setoran' => null]);
        }

        // Ambil relasi target (pakai ->first() jika relasi-nya many)
        $target = method_exists($setoran, 'targets') ? $setoran->target->first() : $setoran->target;

        // Hapus setoran
        $setoran->delete();

        if ($target) {
            // Hitung ulang total ayat yang sudah disetorkan
            $totalAyatDisetorkan = Setoran::where('id_target', $target->id_target)
                ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

            $totalAyat = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);
            $persentaseBaru = number_format(($totalAyatDisetorkan / $totalAyat) * 100, 2);

            $statusHistori = 0;
            if ($totalAyatDisetorkan >= $totalAyat) {
                $statusHistori = 2;
            } elseif ($totalAyatDisetorkan > 0) {
                $statusHistori = 1;
            }

            // Update histori jika masih ada
            if ($histori) {
                $histori->update([
                    'persentase' => $persentaseBaru,
                    'status' => $statusHistori,
                ]);
            }

            // Update status semua setoran yang masih tersisa
            $setoransTersisa = Setoran::where('id_target', $target->id_target)->get();
            $totalAyatTercapai = $setoransTersisa->sum(function ($item) {
                return $item->jumlah_ayat_end - $item->jumlah_ayat_start + 1;
            });

            $statusBaru = ($totalAyatTercapai >= $totalAyat) ? 1 : 0;

            foreach ($setoransTersisa as $item) {
                $item->update(['status' => $statusBaru]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Setoran dan histori terkait berhasil dihapus.']);
    }

    public function destroyByTarget($idSantri, $idGroup, Request $request)
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['success' => false, 'message' => 'Token tidak ditemukan'], 401);
        }

        $token = str_replace('Bearer ', '', $authHeader);
        $user = $this->getUserFromToken($token);

        if (!$user || $user['role'] !== 'pengajar') {
            return response()->json(['success' => false, 'message' => 'Hanya pengajar yang dapat menghapus setoran'], 403);
        }

        $setorans = Setoran::where('id_santri', $idSantri)
            ->whereHas('targets', function ($query) use ($idGroup) {
                $query->where('id_group', $idGroup);
            })->get();

        $setorans->each(function ($setoran) {
            $histori = Histori::where('id_setoran', $setoran->id_setoran)->first();
            if ($histori) {
                $histori->update([
                    'persentase' => 0,
                    'status' => 0,
                    'id_setoran' => null,
                ]);
            }
            $setoran->delete();
        });

        return response()->json(['success' => true, 'message' => 'Semua setoran dan histori terkait berhasil dihapus.']);
    }

    public function getSetoranBySantriAndGroup($idSantri, $idGroup)
    {
        // Cari santri berdasarkan ID
        $santri = Santri::find($idSantri);

        if (!$santri) {
            return response()->json([
                'message' => 'Santri tidak ditemukan'
            ], 404);
        }

        // Ambil data setoran berdasarkan ID Santri dan ID Group (misalnya idGroup disimpan di setoran)
        $setorans = Setoran::with(['santri', 'kelas', 'pengajar', 'targets', 'surat'])
            ->whereHas('santri', function ($query) use ($idSantri) {
                $query->where('id_santri', $idSantri);
            })
            ->whereHas('target', function ($query) use ($idGroup) {
                $query->where('id_group', $idGroup);
            })
            ->get();

        if ($setorans->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data setoran untuk santri ini di group ini.',
                'setorans' => []
            ], 404);
        }

        $santri = optional($setorans->first()->santri);

        // Susun data setorannya
        $dataSetoran = $setorans->map(function ($setoran) {
            return [
                'surat' => optional($setoran->surat)->nama_surat ?? 'Tidak Diketahui',
                'ayat' => $setoran->jumlah_ayat_start == $setoran->jumlah_ayat_end
                    ? $setoran->jumlah_ayat_start
                    : $setoran->jumlah_ayat_start . ' - ' . $setoran->jumlah_ayat_end,
                'tgl_setoran' => \Carbon\Carbon::parse($setoran->tgl_setoran)->format('d M Y'),
                'nilai' => number_format($setoran->nilai),
                'pengajar' => optional($setoran->pengajar)->nama ?? 'Tidak Diketahui',
                'jenis_kelamin_pengajar' => optional($setoran->pengajar)->jenis_kelamin ?? 1,
                'keterangan' => $setoran->keterangan
            ];
        });

        return response()->json([
            'message' => 'Detail setoran santri ditemukan.',
            'santri' => [
                'nama' => $santri->nama,
                'nisn' => $santri->nisn,
            ],
            'setorans' => $dataSetoran
        ]);
    }

    public function getSetoranSantriByTarget($id_santri, $id_target)
    {
        // 1. Cari santri berdasarkan ID
        $santri = Santri::find($id_santri);

        if (!$santri) {
            return response()->json([
                'message' => 'Santri tidak ditemukan'
            ], 404);
        }

        // 2. Cek apakah target tersebut milik santri
        $target = Target::where('id_target', $id_target)
            ->where('id_santri', $id_santri)
            ->first();

        if (!$target) {
            return response()->json([
                'message' => 'Santri tidak punya target hafalan tersebut'
            ], 404);
        }

        // 3. Ambil data setorannya
        $setorans = Setoran::with(['surat', 'pengajar:id_pengajar,nama,jenis_kelamin', 'santri'])
            ->where('id_target', $id_target)
            ->orderBy('tgl_setoran', 'asc')
            ->get();

        $result = $setorans->map(function ($setoran) {
            return [
                'id_surat' => $setoran->surat?->id_surat,
                'nama_surat' => $setoran->surat?->nama_surat ?? '-',
                'ayat' => $setoran->jumlah_ayat_start . ' - ' . $setoran->jumlah_ayat_end,
                'pengajar' => $setoran->pengajar?->nama ?? '-',
                'jenis_kelamin_pengajar' => $setoran->pengajar?->jenis_kelamin ?? '-',
                'tanggal' => $setoran->tgl_setoran,
                'keterangan' => $setoran->keterangan,
            ];
        });

        return response()->json([
            'message' => sprintf('Daftar setoran untuk santri %s target %s', $id_santri, $id_target),
            'data' => $result
        ]);
    }


    public function gettargetBySantri($santri_id)
    {
        $target = Target::where('id_santri', operator: $santri_id)
            ->groupBy('id_group')
            ->get(['id_group']);

        return response()->json([
            'target' => $target
        ]);
    }

    public function getNamaSurat($group_id, $santri_id)
    {
        $target = Target::where('id_group', $group_id)
            ->where('id_santri', $santri_id)
            ->get();

        $surats = $target->map(function ($target) use ($santri_id) {
            $setoran = Setoran::where('id_target', $target->id_target)
                ->where('id_santri', $santri_id)
                ->where('status', 1)
                ->first();

            if (!$setoran) {
                return [
                    'id_surat' => $target->id_surat,
                    'nama_surat' => $target->surat ? $target->surat->nama_surat : 'Tidak diketahui'
                ];
            }
        })->filter()->unique('id_surat')->values();

        return response()->json(['surats' => $surats]);
    }


    public function validateAyat(Request $request)
    {
        $id_surat = $request->input('id_surat');
        $id_santri = $request->input('id_santri');
        $id_group = $request->input('id_group');

        $target = Target::where('id_surat', $id_surat)
            ->where('id_santri', $id_santri)
            ->where('id_group', $id_group)
            ->first();

        if ($target) {
            $setoran = Setoran::where('id_surat', $id_surat)
                ->where('id_santri', $id_santri)
                ->where('id_group', $id_group)
                ->first();

            if ($setoran) {
                $jumlahAyatStart = $setoran->jumlah_ayat_start;
                $jumlahAyatEnd = $setoran->jumlah_ayat_end;

                if (
                    $jumlahAyatStart >= $target->jumlah_ayat_target_awal &&
                    $jumlahAyatStart <= $target->jumlah_ayat_target &&
                    $jumlahAyatEnd >= $target->jumlah_ayat_target_awal &&
                    $jumlahAyatEnd <= $target->jumlah_ayat_target
                ) {
                    return response()->json([
                        'success' => true,
                        'jumlah_ayat_target_awal' => $target->jumlah_ayat_target_awal,
                        'jumlah_ayat_target' => $target->jumlah_ayat_target,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Jumlah ayat tidak valid.',
                    ]);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Setoran tidak ditemukan.',
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Target tidak ditemukan.',
        ]);
    }

    public function getTargetDetailBySurat(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id_group',
            'id_santri' => 'required|exists:santris,id_santri',
            'surat_id' => 'required|exists:surats,id_surat',
        ]);

        $target = Target::where('id_group', $request->group_id)
            ->where('id_santri', $request->santri_id)
            ->where('id_surat', $request->surat_id)
            ->first();

        if ($target) {
            return response()->json([
                'jumlah_ayat_target_awal' => $target->jumlah_ayat_target_awal,
                'jumlah_ayat_target' => $target->jumlah_ayat_target,
            ]);
        }

        return response()->json([
            'message' => 'Target tidak ditemukan untuk surat ini',
        ], 404);
    }

    public function getIdTarget(Request $request)
    {
        $id_group = $request->id_group;
        $id_santri = $request->id_santri;
        $id_surat = $request->id_surat;

        $target = Target::where('id_group', $id_group)
            ->where('id_santri', $id_santri)
            ->where('id_surat', $id_surat)
            ->first();

        if ($target) {
            return response()->json([
                'success' => true,
                'id_target' => $target->id_target
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Target tidak ditemukan'
            ]);
        }
    }
}
