<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Setoran;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Surat;
use App\Models\Target;
use App\Models\Pengajar;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class SetoranController extends Controller
{
    public function index()
    {
        // Mengambil data setoran dengan relasi
        $setorans = Setoran::with(['santri', 'kelas', 'pengajar', 'targets', 'surat'])->get();

        // Mengelompokkan setoran berdasarkan id_santri dan id_group dari tabel targets
        $setoransGrouped = $setorans->groupBy(function($setoran) {
            // Menggunakan id_santri dari relasi santri dan id_group dari relasi targets
            return $setoran->santri->id_santri . '-' . $setoran->targets->first()->id_group;
        });

        return view('setoran.show', compact('setoransGrouped'));
    }



    public function show($groupKey)
    {
        // Pecah groupKey menjadi id_santri dan id_group
        list($idSantri, $idGroup) = explode('-', $groupKey);

        // Ambil semua setoran yang berhubungan dengan id_santri dan id_group
        $setorans = Setoran::with(['santri', 'kelas', 'pengajar', 'targets', 'surat'])
                            ->whereHas('santri', function ($query) use ($idSantri) {
                                $query->where('id_santri', $idSantri);
                            })
                            ->whereHas('targets', function ($query) use ($idGroup) {
                                $query->where('id_group', $idGroup); // Menggunakan id_group bukan id_target
                            })
                            ->get();

        // Kirim data ke view
        return view('setoran.detail', compact('setorans'));
    }

    public function destroyByTarget($idGroup)
    {
        // Temukan semua setoran yang memiliki id_group tertentu
        $setorans = Setoran::whereHas('targets', function ($query) use ($idGroup) {
            $query->where('id_group', $idGroup); // Menggunakan id_group
        })->get();

        // Hapus setoran yang ditemukan
        $setorans->each(function($setoran) {
            $setoran->delete();
        });

        return response()->json(['success' => 'Setoran berhasil dihapus.']);
    }
    public function destroy($idSetoran)
{
    // Temukan semua setoran yang memiliki id_target tertentu
    $setorans = Setoran::where('id_setoran', $idSetoran)->get();  // Asumsi ada kolom id_setoran

    // Hapus setoran yang ditemukan
    $setorans->each(function($setoran) {
        $setoran->delete();
    });

    return response()->json(['success' => 'Setoran  berhasil dihapus.']);
}





    public function create()
    {
        $santris = Santri::all();
        $kelas = Kelas::all();
        $surats = Surat::all();
        $pengajars = Pengajar::all();
        $targets = Target::all(); // Ambil data target
        return view('setoran.create', compact('santris', 'kelas', 'pengajars', 'targets', 'surats'));
    }
    public function getTargetsBySantri($santri_id)
{
    $targets = Target::where('id_santri', $santri_id)
                     ->groupBy('id_group')
                     ->get(['id_group']);

    return response()->json([
        'targets' => $targets
    ]);
}
public function getIdTarget(Request $request)
{
    $id_group = $request->id_group;
    $id_santri = $request->id_santri;
    $id_surat = $request->id_surat;

    // Cari id_target berdasarkan id_group, id_santri, dan id_surat
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

public function validateAyat(Request $request)
{
    // Ambil parameter yang dikirimkan
    $id_surat = $request->input('id_surat');
    $id_santri = $request->input('id_santri');
    $id_group = $request->input('id_group');

    // Query untuk mendapatkan data dari tabel target
    $target = Target::where('id_surat', $id_surat)
                    ->where('id_santri', $id_santri)
                    ->where('id_group', $id_group)
                    ->first();

    // Jika target ditemukan, lanjutkan validasi
    if ($target) {
        // Query untuk mendapatkan setoran berdasarkan id_surat, id_santri, dan id_group
        $setoran = Setoran::where('id_surat', $id_surat)
                         ->where('id_santri', $id_santri)
                         ->where('id_group', $id_group)
                         ->first();

        // Jika setoran ditemukan, lakukan validasi jumlah ayat
        if ($setoran) {
            $jumlahAyatStart = $setoran->jumlah_ayat_start;
            $jumlahAyatEnd = $setoran->jumlah_ayat_end;

            // Validasi jumlah ayat
            if ($jumlahAyatStart >= $target->jumlah_ayat_target_awal && $jumlahAyatStart <= $target->jumlah_ayat_target &&
                $jumlahAyatEnd >= $target->jumlah_ayat_target_awal && $jumlahAyatEnd <= $target->jumlah_ayat_target) {
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

    // Jika target tidak ditemukan
    return response()->json([
        'success' => false,
        'message' => 'Target tidak ditemukan.',
    ]);
}

public function getTargetDetailBySurat(Request $request)
{
    // Validasi input
    $request->validate([
        'group_id' => 'required|exists:groups,id_group',
        'santri_id' => 'required|exists:santris,id_santri',
        'surat_id' => 'required|exists:surats,id_surat',
    ]);

    // Ambil target berdasarkan group_id, santri_id, dan surat_id
    $target = Target::where('id_group', $request->group_id)
                    ->where('id_santri', $request->santri_id)
                    ->where('id_surat', $request->surat_id)
                    ->first();

    // Periksa apakah data target ditemukan
    if ($target) {
        return response()->json([
            'jumlah_ayat_target_awal' => $target->jumlah_ayat_target_awal,
            'jumlah_ayat_target' => $target->jumlah_ayat_target,
        ]);
    }

    // Jika target tidak ditemukan
    return response()->json([
        'message' => 'Target tidak ditemukan untuk surat ini',
    ], 404);
}

public function getNamaSurat(Request $request)
{
    $groupId = $request->input('group_id');
    $santriId = $request->input('santri_id'); // Ambil ID Santri

    // Cari target dengan id_group dan id_santri yang diberikan
    $targets = Target::where('id_group', $groupId)
                     ->where('id_santri', $santriId)
                     ->get();

    // Ambil daftar id_surat yang unik dari target, pastikan id_target di tabel setoran belum selesai (status != 1)
    $surats = $targets->map(function ($target) use ($santriId) {
        // Mengecek apakah id_target terkait sudah selesai (status 1) di tabel setoran
        $setoran = Setoran::where('id_target', $target->id_target)
                         ->where('id_santri', $santriId)
                         ->where('status', 1) // Status selesai
                         ->first();

        // Jika setoran tidak ditemukan atau statusnya bukan 1, maka tampilkan id_surat dan nama_surat
        if (!$setoran) {
            return [
                'id_surat' => $target->id_surat, // Ambil id_surat dari tabel target
                'nama_surat' => $target->surat ? $target->surat->nama_surat : 'Tidak diketahui' // Ambil nama_surat jika ada
            ];
        }
    })->filter()->unique('id_surat')->values(); // Menghapus nilai null dan memastikan id_surat unik

    return response()->json(['surats' => $surats]);
}

public function store(Request $request)
{
    // Validasi input
    $request->validate([
        'id_santri' => 'required|exists:santris,id_santri',
        'tgl_setoran' => 'required|date',
        'id_kelas' => 'required|exists:kelas,id_kelas',
        'id_surat' => 'required',
        'nilai' =>'required',
        'jumlah_ayat_start' => 'required|numeric',
        'jumlah_ayat_end' => 'required|numeric',
        'id_group' => 'required', // Tidak divalidasi dengan `exists`, karena hanya ada di `targets`
    ]);

    // Ambil target berdasarkan 4 parameter utama
    $target = Target::where([
        ['id_santri', '=', $request->id_santri],
        ['id_kelas', '=', $request->id_kelas],
        ['id_surat', '=', $request->id_surat],
        ['id_group', '=', $request->id_group]
    ])->first();

    // Validasi jika jumlah_ayat_start lebih besar dari jumlah_ayat_end
    if ($request->jumlah_ayat_start > $request->jumlah_ayat_end) {
        return redirect()->back()->withErrors(['jumlah_ayat_start' => 'Jumlah ayat mulai tidak boleh lebih besar dari jumlah ayat akhir, jumlah ayat akhir adalah ' . $request->jumlah_ayat_end]);
    }

    // Validasi jika jumlah_ayat_end lebih besar dari target ayat
    if ($request->jumlah_ayat_end > $target->jumlah_ayat_target) {
        return redirect()->back()->withErrors(['jumlah_ayat_end' => 'Jumlah ayat akhir tidak boleh lebih dari target ayat, target ayat surat ini adalah ' . $target->jumlah_ayat_target]);
    }

    // Validasi jika jumlah_ayat_start lebih besar dari target ayat
    if ($request->jumlah_ayat_start > $target->jumlah_ayat_target) {
        return redirect()->back()->withErrors(['jumlah_ayat_start' => 'Jumlah ayat mulai tidak boleh lebih besar dari jumlah ayat yang ditargetkan, jumlah ayat yang ditargetkan adalah ' . $target->jumlah_ayat_target]);
    }

    // Validasi jika jumlah_ayat_start lebih kecil dari target awal ayat
    if ($request->jumlah_ayat_start < $target->jumlah_ayat_target_awal) {
        return redirect()->back()->withErrors(['jumlah_ayat_start' => 'Jumlah ayat mulai tidak boleh lebih kecil dari target awal ayat surat ini, target awal ayat ini adalah ' . $target->jumlah_ayat_target_awal]);
    }

    // Validasi jika jumlah_ayat_end lebih kecil dari target awal ayat
    if ($request->jumlah_ayat_end < $target->jumlah_ayat_target_awal) {
        return redirect()->back()->withErrors(['jumlah_ayat_end' => 'Jumlah ayat akhir tidak boleh lebih kecil dari target awal ayat surat ini, jumlah ayat target awal adalah ' . $request->jumlah_ayat_target_awal]);
    }

    // Cek rentang yang valid dengan mengambil setoran sebelumnya
    $setorans = Setoran::where('id_target', $target->id_target)
                       ->orderBy('jumlah_ayat_start') // Urutkan berdasarkan jumlah_ayat_start
                       ->get();

    // Periksa apakah jumlah_ayat_start yang diinputkan valid
    $valid = false;
    $previousEnd = 0; // Nilai sebelumnya untuk perbandingan

    foreach ($setorans as $setoran) {
        // Jika ayat yang diminta lebih besar dari ayat akhir sebelumnya, setoran dapat diterima
        if ($request->jumlah_ayat_start > $previousEnd && $request->jumlah_ayat_start <= $setoran->jumlah_ayat_start) {
            $valid = true;
            break;
        }
        $previousEnd = $setoran->jumlah_ayat_end;
    }

    // Jika jumlah_ayat_start tidak valid, kirim error
    if (!$valid && ($request->jumlah_ayat_start > $previousEnd && $request->jumlah_ayat_start <= $target->jumlah_ayat_target)) {
        $valid = true; // jika itu ayat terakhir
    }

    if (!$valid) {
        return redirect()->back()->withErrors(['jumlah_ayat_start' => 'Jumlah ayat mulai tidak boleh lebih kecil dari jumlah ayat akhir pada setoran sebelumnya, dan harus berada di rentang yang kosong antara setoran-setoran yang ada.']);
    }

    // Hitung persentase setoran
    $totalAyat = $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal;
    $persentase = (($request->jumlah_ayat_end - $request->jumlah_ayat_start + 1) / $totalAyat) * 100;

    // Tentukan status berdasarkan persentase
    $status = $persentase >= 100 ? '1' : '0'; // 1 untuk selesai, 0 untuk proses

    // Simpan data setoran dengan id_target yang sesuai dan status yang dihitung
    Setoran::create([
        'id_santri' => $request->id_santri,
        'tgl_setoran' => $request->tgl_setoran,
        'status' => $status, // Status ditentukan otomatis
        'id_kelas' => $request->id_kelas,
        'id_target' => $target->id_target, // Gunakan id_target yang ditemukan
        'id_surat' => $request->id_surat,
        'nilai' => $request->nilai,
        'jumlah_ayat_start' => $request->jumlah_ayat_start,
        'jumlah_ayat_end' => $request->jumlah_ayat_end,
        'id_pengajar' => $request->id_pengajar,
        'keterangan' => $request->keterangan,
    ]);

    return redirect()->route('setoran.index')->with('success', 'Setoran berhasil ditambahkan');
}
// public function validateSetoran(Request $request)
// {
//     // Ambil rentang setoran yang sudah ada untuk id_target
//     $existingRanges = $this->getExistingRanges($request->id_target);

//     // Cek apakah jumlah_ayat_start lebih kecil dari jumlah_ayat_end sebelumnya
//     foreach ($existingRanges as $range) {
//         if ($request->jumlah_ayat_start < $range['jumlah_ayat_end']) {
//             return redirect()->back()->withErrors([
//                 'jumlah_ayat_start' => 'Jumlah ayat mulai tidak boleh lebih kecil dari jumlah ayat akhir pada setoran sebelumnya.'
//             ]);
//         }
//     }

//     // Cek apakah jumlah_ayat_start berada di antara rentang yang kosong
//     $validRangeFound = false;
//     for ($i = 0; $i < count($existingRanges) - 1; $i++) {
//         // Ambil rentang kosong antara setoran
//         $currentEnd = $existingRanges[$i]['jumlah_ayat_end'];
//         $nextStart = $existingRanges[$i + 1]['jumlah_ayat_start'];

//         // Validasi jika jumlah_ayat_start baru berada dalam rentang kosong
//         if ($request->jumlah_ayat_start >= $currentEnd + 1 && $request->jumlah_ayat_start <= $nextStart - 1) {
//             $validRangeFound = true;
//             break;
//         }
//     }

//     // Jika rentang kosong tidak ditemukan, tampilkan pesan
//     if (!$validRangeFound) {
//         return redirect()->back()->withErrors([
//             'jumlah_ayat_start' => 'Jumlah ayat mulai harus berada di rentang yang kosong antara setoran-setoran yang ada.'
//         ]);
//     }

//     // Jika valid, lanjutkan ke proses penyimpanan setoran
//     return $this->store($request);
// }










    public function edit(Setoran $setoran)
    {
        $santris = Santri::all();
        $kelas = Kelas::all();
        $surats = Surat ::all();
        $pengajars = Pengajar::all();
        $targets = Target::all(); // Ambil data target
        return view('setoran.edit', compact('setoran', 'santris', 'kelas','pengajars','targets','surats'));
    }

    public function update(Request $request, Setoran $setoran)
    {
        $request->validate([
            'id_santri' => 'required',
            'tgl_setoran' => 'required|date',
            'status' => 'required',
            'id_kelas' => 'required',
            'nilai' =>'required',
            'id_pengajar' => 'required',
        ]);

        $setoran->update($request->all());
        return redirect()->route('setoran.index')->with('success', 'Setoran berhasil diperbarui');
    }







    public function fnGetData()
    {
        $setorans = Setoran::with(['santri', 'kelas','targets','pengajar'])->get();
        return DataTables::of($setorans)
            ->addIndexColumn()
            ->addColumn('santri', function ($row) {
                return $row->santri->nama ?? '-';
            })
            ->addColumn('kelas', function ($row) {
                return $row->kelas->nama_kelas ?? '-';
            })
            ->addColumn('target', function ($row) {
                // Menggunakan nama_surat dan jumlah_ayat dari relasi surat
                return $row->target->surat->nama_surat ?? '-';  // Menggunakan hanya nama_surat saja
            })
            ->addColumn('pengajar', function ($row) {
                return $row->pengajar->nama ?? '-';
            })
            ->addColumn('target', function ($row) {
                return $row->target->keterangan ?? '-';
            })
            ->addColumn('jumlah_ayat', function ($row) {
                // Menampilkan jumlah_ayat dari relasi surat
                return $row->target->jumlah_ayat_target ?? '-';
            })
            ->addColumn('nilai', function ($row) {
                return $row->nilai ?? '-';
            })
            ->addColumn('jumlah_ayat_start', function ($row) {
                return $row->jumlah_ayat_start ?? '-';
            })
            ->addColumn('jumlah_ayat_end', function ($row) {
                return $row->jumlah_ayat_end ?? '-';
            })
            ->addColumn('persentase', function ($row) {
                return $row->persentase ?? '-';
            })
            ->addColumn('status', function ($row) {
                return $row->status == 1 ? 'Selesai' : 'Proses';  // Ganti dengan status sesuai kebutuhan
            })
            ->addColumn('action', function ($row) {
                return '<a href="'.route('setoran.edit', $row->id_setoran).'" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger btnDelete" data-id="'.$row->id_setoran.'">Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}
