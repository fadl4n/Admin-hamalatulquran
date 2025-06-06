<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;

use App\Models\Target;
use App\Models\Santri;
use App\Models\Pengajar;
use App\Models\Kelas;
use App\Models\Surat;
use App\Models\Histori;
use App\Models\Setoran;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class TargetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengambil data dan mengelompokkan berdasarkan kriteria yang sama
        $target = Target::with(['santri', 'kelas', 'surat', 'pengajar'])
            ->get()
            ->groupBy(function ($item) {
                return $item->id_santri . '-' . $item->id_target;
            });
        return view('target.show', compact('target'));
    }
    public function create()
    {
        $santris = Santri::all();
        $pengajars = Pengajar::all();
        $kelas = Kelas::all();
        $surats = Surat::all();
        return view('target.create', compact('santris', 'pengajars', 'kelas', 'surats'));
    }
    public function store(Request $request)
    {
        // Validasi input data
        $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'id_pengajar' => 'required|exists:pengajars,id_pengajar',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_surat' => 'required|exists:surats,id_surat',
            'tgl_mulai' => 'required|date',
            'jumlah_ayat_target_awal' => 'required|integer|min:1',
            'jumlah_ayat_target' => 'required|integer|min:1',
            'tgl_target' => 'required|date',
            'id_group' => 'nullable|integer'
        ]);

        // Ambil jumlah ayat dari surat terkait
        $surat = Surat::findOrFail($request->id_surat);

        // Validasi jumlah_ayat_target_awal dan jumlah_ayat_target
        if ($request->jumlah_ayat_target_awal > $request->jumlah_ayat_target) {
            return back()->withErrors(['jumlah_ayat_target_awal' => 'Jumlah ayat target awal tidak boleh lebih dari jumlah ayat target.'])->withInput();
        }

        if ($request->jumlah_ayat_target > $surat->jumlah_ayat) {
            return back()->withErrors(['jumlah_ayat_target' => 'Jumlah ayat target tidak boleh lebih dari jumlah ayat surat.'])->withInput();
        }

        // Menghitung jumlah_ayat_target_end
        $jumlah_ayat_target_end = $request->jumlah_ayat_target_awal + $request->jumlah_ayat_target - 1;

        // Cek apakah kombinasi id_santri, id_group, id_surat, dan jumlah ayat sudah ada
        $existingTarget = Target::where('id_santri', $request->id_santri)
            ->where('id_surat', $request->id_surat)
            ->where('jumlah_ayat_target_awal', $request->jumlah_ayat_target_awal)
            ->where('jumlah_ayat_target', $request->jumlah_ayat_target)
            ->exists();

        if ($existingTarget) {
            return back()->withErrors(['jumlah_ayat_target' => 'Santri sudah memilih surat dengan jumlah ayat yang sama.'])->withInput();
        }
$overlapTarget = Target::where('id_santri', $request->id_santri)
    ->where('id_surat', $request->id_surat)
    ->where(function ($query) use ($request, $jumlah_ayat_target_end) {
        $query->whereBetween('jumlah_ayat_target_awal', [$request->jumlah_ayat_target_awal, $jumlah_ayat_target_end])
              ->orWhereBetween('jumlah_ayat_target', [$request->jumlah_ayat_target_awal, $jumlah_ayat_target_end]);
    })
    ->orderByDesc('jumlah_ayat_target') // Ambil yang terbesar
    ->first();

if ($overlapTarget) {
    $ayatTerakhir = $overlapTarget->jumlah_ayat_target;
    return back()->withErrors([
        'jumlah_ayat_target' => "Rentang jumlah ayat ini sudah ada, ayat terakhir pada target sebelumnya yaitu ayat ke-{$ayatTerakhir}."
    ])->withInput();
}

        if (Carbon::parse($request->tgl_mulai)->gt(Carbon::parse($request->tgl_target))) {
    return back()->withErrors([
        'tgl_mulai' => 'Tanggal mulai tidak boleh lebih besar dari tanggal target.',
    ])->withInput();
}

        $santri = Santri::findOrFail($request->id_santri);
        if ($santri->id_kelas != $request->id_kelas) {
            return back()->withErrors(['id_kelas' => 'Santri ini terdaftar di kelas ' . $santri->kelas->nama_kelas . ', bukan di kelas yang dipilih.'])->withInput();
        }
        // Simpan data target ke database
        $target = Target::create([
            'id_santri' => $request->id_santri,
            'id_pengajar' => $request->id_pengajar,
            'id_kelas' => $request->id_kelas,
            'id_surat' => $request->id_surat,
            'tgl_mulai' => $request->tgl_mulai,
            'jumlah_ayat_target_awal' => $request->jumlah_ayat_target_awal,
            'jumlah_ayat_target' => $request->jumlah_ayat_target,
            'tgl_target' => $request->tgl_target,
        ]);
$today = now();
$tglTarget = Carbon::parse($target->tgl_target)->endOfDay();  // set ke 23:59:59 tanggal target
$status = $today->greaterThan($tglTarget) ? 3 : 0;



        // Simpan histori berdasarkan target
        Histori::create([
            'id_santri' => $target->id_santri,
            'id_surat' => $target->id_surat,
            'id_kelas' => $target->id_kelas,
            'persentase' => 0.00,
            'status' => $status,  // Status default yang bisa kamu sesuaikan
            'id_target' => $target->id_target,  // Menghubungkan histori dengan target
            'nilai' => null,  // Nilai default, bisa disesuaikan jika diperlukan
        ]);

        // Cek apakah tombol "Continue" ditekan
        if ($request->has('continue')) {
            return redirect()->route('target.create')->with('success', 'Data berhasil disimpan!')->withInput();
        }

        // Jika tombol simpan ditekan, kembali ke halaman index
        return redirect()->route('target.index')->with('success', 'Target berhasil ditambahkan!');
    }

    private function validateTargetOverlap(Request $request, $jumlah_ayat_target_end)
    {
        // Ambil target yang sudah ada
        $target = Target::where('id_surat', $request->id_surat)
            ->where('id_santri', $request->id_santri)
            ->get();

        $jumlah_tumpang_tindih = 0;

        foreach ($target as $existingTarget) {
            if (
                ($request->jumlah_ayat_target_awal >= $existingTarget->jumlah_ayat_target_awal &&
                    $request->jumlah_ayat_target_awal <= $existingTarget->jumlah_ayat_target_end) ||
                ($jumlah_ayat_target_end >= $existingTarget->jumlah_ayat_target_awal &&
                    $jumlah_ayat_target_end <= $existingTarget->jumlah_ayat_target_end) ||
                ($request->jumlah_ayat_target_awal <= $existingTarget->jumlah_ayat_target_awal &&
                    $jumlah_ayat_target_end >= $existingTarget->jumlah_ayat_target_end)
            ) {
                $jumlah_tumpang_tindih++;
            }
        }

        if ($jumlah_tumpang_tindih > 0) {
            return redirect()->back()->withErrors([
                'jumlah_ayat_target_awal' => "Rentang ayat target tumpang tindih dengan $jumlah_tumpang_tindih target yang sudah ada."
            ])->withInput();
        }
    }
    public function show($id)
    {
        $target = Target::with(['santri', 'pengajar', 'kelas', 'surat'])->findOrFail($id);
        return view('target.show', compact('target'));
    }
    public function edit(Target $target)
    {
        $santris = Santri::all();
        $pengajars = Pengajar::all();
        $kelas = Kelas::all();
        $surats = Surat::all();
        return view('target.edit', compact('target', 'santris', 'pengajars', 'kelas', 'surats'));
    }
    public function update(Request $request, Target $target)
    {
        // Validasi input data
        $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'id_pengajar' => 'required|exists:pengajars,id_pengajar',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_surat' => 'required|exists:surats,id_surat',
            'tgl_mulai' => 'required|date',
            'jumlah_ayat_target_awal' => 'required|integer|min:1',
            'jumlah_ayat_target' => 'required|integer|min:1',
            'tgl_target' => 'required|date',
        ]);

        // Ambil jumlah ayat dari surat terkait
        $surat = Surat::findOrFail($request->id_surat);

        // Validasi jumlah_ayat_target_awal dan jumlah_ayat_target
        if ($request->jumlah_ayat_target_awal > $request->jumlah_ayat_target) {
            return back()->withErrors(['jumlah_ayat_target_awal' => 'Jumlah ayat target awal tidak boleh lebih dari jumlah ayat target.'])->withInput();
        }

        if ($request->jumlah_ayat_target > $surat->jumlah_ayat) {
            return back()->withErrors(['jumlah_ayat_target' => 'Jumlah ayat target tidak boleh lebih dari jumlah ayat surat.'])->withInput();
        }

        // Menghitung jumlah_ayat_target_end
        $jumlah_ayat_target_end = $request->jumlah_ayat_target_awal + $request->jumlah_ayat_target - 1;

       $existingTarget = Target::where('id_santri', $request->id_santri)
    ->where('id_surat', $request->id_surat)
    ->where('jumlah_ayat_target_awal', $request->jumlah_ayat_target_awal)
    ->where('jumlah_ayat_target', $request->jumlah_ayat_target)
    ->where('id_target', '!=', $target->id_target) // ← Tambahkan pengecualian ini
    ->exists();


    $overlapTarget = Target::where('id_santri', $request->id_santri)
    ->where('id_surat', $request->id_surat)
        ->where('id_target', '!=', $target->id_target) // ← Tambahkan ini juga
    ->where(function ($query) use ($request, $jumlah_ayat_target_end) {
        $query->whereBetween('jumlah_ayat_target_awal', [$request->jumlah_ayat_target_awal, $jumlah_ayat_target_end])
              ->orWhereBetween('jumlah_ayat_target', [$request->jumlah_ayat_target_awal, $jumlah_ayat_target_end]);
    })
    ->orderByDesc('jumlah_ayat_target') // Ambil yang terbesar
    ->first();

if ($overlapTarget) {
    $ayatTerakhir = $overlapTarget->jumlah_ayat_target;
    return back()->withErrors([
        'jumlah_ayat_target' => "Rentang jumlah ayat ini sudah ada, ayat terakhir pada target sebelumnya yaitu ayat ke-{$ayatTerakhir}."
    ])->withInput();
}


        if (Carbon::parse($request->tgl_mulai)->gt(Carbon::parse($request->tgl_target))) {
    return back()->withErrors([
        'tgl_mulai' => 'Tanggal mulai tidak boleh lebih besar dari tanggal target.',
    ])->withInput();
}

        // // Cek apakah ada target yang sama dengan id_santri, id_surat, id_group tetapi berbeda tgl_mulai dan tgl_target
        // $existingTargetDates = Target::where('id_santri', $request->id_santri)
        //     ->where('id_group', $request->id_group)
        //     ->where(function ($query) use ($request) {
        //         $query->where('tgl_mulai', '!=', $request->tgl_mulai)
        //             ->orWhere('tgl_target', '!=', $request->tgl_target);
        //     })
        //     ->where('id_target', '!=', $target->id_target)  // Menghindari perbaruan target yang sama
        //     ->first();

        // if ($existingTargetDates) {
        //     return back()->withErrors([
        //         'tgl_mulai' => 'Sudah ada target untuk santri ini dengan tanggal mulai ' . Carbon::parse($existingTargetDates->tgl_mulai)->format('d-m-Y') . '.',
        //         'tgl_target' => 'Sudah ada target untuk santri ini dengan tanggal target ' . Carbon::parse($existingTargetDates->tgl_target)->format('d-m-Y') . '.'
        //     ])->withInput();
        // }

        // Pengecekan apakah id_kelas sesuai dengan santri
        $santri = Santri::findOrFail($request->id_santri);
        if ($santri->id_kelas != $request->id_kelas) {
            return back()->withErrors(['id_kelas' => 'Santri ini terdaftar di kelas ' . $santri->kelas->nama_kelas . ', bukan di kelas yang dipilih.'])->withInput();
        }

        // Update data target
        $target->update($request->all());

        // Setelah update target, perbarui histori yang terkait
        $histori = Histori::where('id_target', $target->id_target)->first();

        if ($histori) {
            // Hitung ulang persentase berdasarkan jumlah ayat yang baru
            $totalAyatDisetorkan = Setoran::where('id_target', $target->id_target)
                ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

            $persentaseBaru = number_format(($totalAyatDisetorkan / max(1, $request->jumlah_ayat_target)) * 100, 2);

            // Tentukan status baru
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

        return redirect()->route('target.index')->with('success', 'Target dan histori berhasil diperbarui!');
    }


   public function destroy($id_target)
{
    try {
        // Mencari target berdasarkan id_target
        $target = Target::find($id_target);

        // Pastikan target ditemukan
        if (!$target) {
            return response()->json(['error' => 'Target tidak ditemukan'], 404);
        }

        // Hapus target
        $target->delete();

        return response()->json(['success' => 'Target berhasil dihapus']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal menghapus target'], 500);
    }
}

    public function destroyByIdTarget($id_target)
    {
        try {
            // Mencari target berdasarkan id_target
            $target = Target::findOrFail($id_target);

            // Hapus target
            $target->delete();

            return response()->json(['success' => 'Target berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus target'], 500);
        }
    }
    // Untuk DataTables
    public function fnGetData()
    {
        $target = Target::with(['santri', 'pengajar', 'kelas', 'surat'])
            ->select('target.*');

        return DataTables::of($target)
            ->addIndexColumn()
            ->addColumn('nama_santri', function ($row) {
                return $row->santri->nama;
            })
            ->addColumn('nama_pengajar', function ($row) {
                return $row->pengajar->nama;
            })
            ->addColumn('nama_kelas', function ($row) {
                return $row->kelas->nama_kelas;
            })
            ->addColumn('nama_surat', function ($row) {
                return $row->surat->nama_surat;
            })
            ->addColumn('jumlah_ayat_target_awal', function ($row) {  // ✅ Menggunakan jumlah_ayat_target
                return $row->jumlah_ayat_target_awal;
            })
            ->addColumn('jumlah_ayat_target', function ($row) {  // ✅ Menggunakan jumlah_ayat_target
                return $row->jumlah_ayat_target;
            })
            ->addColumn('tgl_mulai', function ($row) {
                return $row->tgl_mulai;
            })
            ->addColumn('tgl_target', function ($row) {
                return $row->tgl_target;
            })
            ->filterColumn('nama_kelas', function ($query, $keyword) {
            $query->whereHas('kelas', function ($q) use ($keyword) {
                $q->where('nama_kelas', 'like', "%$keyword%");
            });
        })

            ->addColumn('action', function ($row) {
                return '
                <a href="' . route('target.edit', $row->id) . '" class="btn btn-sm btn-warning">Edit</a>
                <button class="btn btn-sm btn-danger btnDelete" data-id="' . $row->id . '">Hapus</button>
            ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
    public function detail(Request $request, $id_target)
    {
        // Ambil parameter id_santri dari query string
        $id_santri = $request->query('id_santri'); // Bisa juga pakai $request->input('id_santri')

        // Query untuk mengambil data berdasarkan filter id_target dan id_santri
        $targets = Target::with(['santri', 'kelas', 'surat', 'pengajar'])
            ->when($id_target, function ($query, $id_target) {
                return $query->where('id_target', $id_target);
            })
            ->when($id_santri, function ($query, $id_santri) {
                return $query->where('id_santri', $id_santri);
            })
            ->orderBy('id_surat')
            ->get();

        // Lakukan pengelompokan berdasarkan id_santri dan id_group
        $targets = $targets->groupBy(function ($item) {
            return $item->id_santri . '-' . $item->id_target;
        });

        return view('target.detail', compact('targets'));
    }
}
