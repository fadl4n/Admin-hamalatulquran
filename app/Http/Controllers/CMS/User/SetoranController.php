<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Setoran;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Surat;
use App\Models\Target;
use App\Models\Histori;
use App\Models\Pengajar;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class SetoranController extends Controller
{
    public function index()
    {
        // Mengambil data setoran dengan relasi
        $setorans = Setoran::with(['santri', 'kelas', 'pengajar', 'targets', 'surat'])->get();

        // Mengelompokkan setoran berdasarkan id_santri dan id_group dari tabel targets
        $setoransGrouped = $setorans->groupBy(function ($setoran) {
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

    public function destroyByTarget($idSantri, $idGroup)
    {
        // Temukan semua setoran yang memiliki id_santri dan id_group tertentu melalui relasi ke targets
        $setorans = Setoran::where('id_santri', $idSantri)
            ->whereHas('targets', function ($query) use ($idGroup) {
                $query->where('id_group', $idGroup);
            })->get();

        // Perbarui histori terkait dengan setoran yang akan dihapus
        $setorans->each(function ($setoran) {
            // Cari histori yang terkait dengan setoran ini
            $histori = Histori::where('id_setoran', $setoran->id_setoran)->first();

            if ($histori) {
                // Update histori: set persentase ke 0 dan status ke 'Proses'
                $histori->update([
                    'persentase' => 0,
                    'status' => 0,
                ]);
            }
            // Hapus setoran yang terkait
            $setoran->delete();
        });

        return response()->json(['success' => 'Setoran dan histori terkait berhasil dihapus.']);
    }
    public function destroy($idSetoran)
    {
        // Temukan setoran berdasarkan id_setoran
        $setoran = Setoran::findOrFail($idSetoran);
        // Cari histori yang terkait dengan setoran ini
        $histori = Histori::where('id_setoran', $setoran->id_setoran)->first();
        if ($histori) {
            // Set id_setoran menjadi null terlebih dahulu pada histori yang terkait
            $histori->update([
                'id_setoran' => null,
            ]);
        }
        // Hapus setoran yang terkait
        $setoran->delete();
        // Setelah setoran dihapus, kita perlu menghitung ulang status dan persentase histori
        if ($histori) {
            // Cari target terkait histori
            $target = $histori->target;

            // Hitung total ayat yang disetorkan setelah penghapusan
            $totalAyatDisetorkan = Setoran::where('id_target', $target->id_target)
                ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

            // Hitung total ayat target
            $totalAyat = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);
            $persentaseBaru = number_format(($totalAyatDisetorkan / $totalAyat) * 100, 2);

            // Tentukan status berdasarkan total ayat yang disetorkan
            if ($totalAyatDisetorkan >= $totalAyat) {
                $statusHistori = 2; // Selesai
            } elseif ($totalAyatDisetorkan > 0) {
                $statusHistori = 1; // Proses
            } else {
                $statusHistori = 0; // Belum mulai
            }

            // Update histori dengan status dan persentase baru
            $histori->update([
                'persentase' => $persentaseBaru,
                'status' => $statusHistori,
            ]);
        }
        // Setelah setoran dihapus, perbarui setoran lainnya di target yang sama
        $setoransTersisa = Setoran::where('id_target', $target->id_target)->get();

        // Hitung ulang total ayat yang telah disetorkan
        $totalAyatTercapai = 0;
        foreach ($setoransTersisa as $setoranTersisa) {
            $totalAyatTercapai += ($setoranTersisa->jumlah_ayat_end - $setoranTersisa->jumlah_ayat_start + 1);
        }

        // Tentukan apakah setoran yang tersisa harus diubah statusnya
        $statusBaru = ($totalAyatTercapai >= $totalAyat) ? 1 : 0;

        // Update semua setoran yang tersisa
        foreach ($setoransTersisa as $setoranTersisa) {
            $setoranTersisa->update(['status' => $statusBaru]);
        }
        return response()->json(['success' => 'Setoran dan histori terkait berhasil dihapus.']);
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
                if (
                    $jumlahAyatStart >= $target->jumlah_ayat_target_awal && $jumlahAyatStart <= $target->jumlah_ayat_target &&
                    $jumlahAyatEnd >= $target->jumlah_ayat_target_awal && $jumlahAyatEnd <= $target->jumlah_ayat_target
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
            'id_santri' => 'required|exists:santris,id_santri',
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
        $santriId = $request->input('id_santri'); // Ambil ID Santri

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
            'id_pengajar' => 'required',
            'nilai' => 'required|numeric|min:0|max:100',
            'jumlah_ayat_start' => 'required|numeric',
            'jumlah_ayat_end' => 'required|numeric',
            'id_group' => 'required', // Tidak divalidasi dengan exists, karena hanya ada di targets
        ]);

        // Pengecekan apakah id_kelas sesuai dengan santri
        $santri = Santri::find($request->id_santri);
        if (!$santri) {
            return back()->withErrors(['id_santri' => 'Santri tidak ditemukan.'])->withInput();
        }

        if ($santri->id_kelas != $request->id_kelas) {
            return back()->withErrors(['id_kelas' => 'Santri ini terdaftar di kelas ' . $santri->kelas->nama_kelas . ', bukan di kelas yang dipilih.'])->withInput();
        }

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
            return redirect()->back()->withErrors(['jumlah_ayat_end' => 'Jumlah ayat akhir tidak boleh lebih kecil dari target awal ayat surat ini, jumlah ayat target awal adalah ' . $target->jumlah_ayat_target_awal]);
        }


        // Ambil setoran sebelumnya untuk validasi tumpang tindih
        $setorans = Setoran::where('id_target', $target->id_target)
            ->orderBy('jumlah_ayat_start') // Urutkan berdasarkan jumlah_ayat_start
            ->get();

        // Periksa apakah jumlah_ayat_start yang diinputkan valid
        $valid = false;
        $previousEnd = 0; // Nilai sebelumnya untuk perbandingan

        foreach ($setorans as $setoran) {
            // Cek apakah ayat yang diminta tidak tumpang tindih dengan setoran yang ada
            if ($request->jumlah_ayat_start >= $previousEnd && $request->jumlah_ayat_start < $setoran->jumlah_ayat_start) {
                $valid = true;
                break;
            }
            $previousEnd = $setoran->jumlah_ayat_end;
        }

        // Cek apakah ayat terakhir berada setelah setoran terakhir
        if (!$valid && $request->jumlah_ayat_start > $previousEnd && $request->jumlah_ayat_start <= $target->jumlah_ayat_target) {
            $valid = true; // jika itu ayat terakhir
        }

        // Ambil jumlah ayat akhir pada setoran sebelumnya untuk digunakan dalam pesan error
        $previousEndAyat = $previousEnd; // Nilai sebelumnya untuk ayat akhir

        if (!$valid) {
            return redirect()->back()->withErrors([
                'jumlah_ayat_start' => 'Jumlah ayat mulai tidak boleh lebih kecil dari jumlah ayat akhir pada setoran sebelumnya (ayat akhir sebelumnya: ' . $previousEndAyat . '), dan harus berada di rentang yang kosong antara setoran-setoran yang ada.'
            ]);
        }

        $totalAyat = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);

        $persentase = number_format((($request->jumlah_ayat_end - $request->jumlah_ayat_start + 1) / $totalAyat) * 100, 2);

        $status = $persentase >= 100 ? '1' : '0'; // 1 untuk selesai, 0 untuk proses




        // Simpan data setoran dengan id_target yang sesuai dan status yang dihitung
        $setoran = Setoran::create([
            'id_santri' => $request->id_santri,
            'tgl_setoran' => $request->tgl_setoran,
            'status' => $status, // Status ditentukan otomatis
            'id_kelas' => $request->id_kelas,
            'persentase' => $persentase,
            'id_target' => $target->id_target, // Gunakan id_target yang ditemukan
            'id_surat' => $request->id_surat,
            'nilai' => $request->nilai,
            'jumlah_ayat_start' => $request->jumlah_ayat_start,
            'jumlah_ayat_end' => $request->jumlah_ayat_end,
            'id_pengajar' => $request->id_pengajar,
            'keterangan' => $request->keterangan,
        ]);

        // Periksa setoran lainnya dan ubah statusnya jika sudah selesai
        $setorans = Setoran::where('id_target', $target->id_target)->get();

        // Perbarui status setoran pertama jika sudah mencapai jumlah_ayat_target
        $totalAyatTercapai = 0;
        foreach ($setorans as $setoran) {
            $totalAyatTercapai += $setoran->jumlah_ayat_end - $setoran->jumlah_ayat_start + 1;
        }

        // Jika total ayat yang dicapai sudah mencapai target, ubah status setoran menjadi selesai
        if ($totalAyatTercapai >= $target->jumlah_ayat_target) {
            foreach ($setorans as $setoran) {
                $setoran->status = '1'; // Ubah status menjadi selesai
                $setoran->save();
            }
        }
        // Periksa apakah histori sudah ada
        $histori = Histori::where('id_santri', $request->id_santri)
            ->where('id_target', $target->id_target)
            ->where('id_surat', $request->id_surat)
            ->first();

        // Jika histori ditemukan, perbarui status dan persentase
        if ($histori) {
            // Hitung total ayat yang sudah disetorkan termasuk setoran yang baru
            $totalAyatDisetorkan = Setoran::where('id_target', $target->id_target)
                ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

            // Hitung persentase baru
            $totalAyat = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);
            $persentaseBaru = number_format(($totalAyatDisetorkan / $totalAyat) * 100, 2);

            // Update persentase dan id_setoran di histori
            $histori->persentase = $persentaseBaru;
            $histori->id_setoran = $setoran->id_setoran;

            // Tentukan status berdasarkan total ayat yang disetorkan dan tanggal setoran
            if ($request->tgl_setoran > $target->tgl_target) {
                $statusHistori = 3; // Terlambat
            } elseif ($persentaseBaru >= 100) {
                $statusHistori = 2; // Selesai
            } else {
                $statusHistori = 1; // Proses
            }

            // Update status histori
            $histori->status = $statusHistori;
            $histori->save();
        } else {
            // Jika histori tidak ditemukan, buat histori baru
            $totalAyatDisetorkan = Setoran::where('id_target', $target->id_target)
                ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

            // Hitung persentase baru
            $totalAyat = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);
            $persentaseBaru = number_format(($totalAyatDisetorkan / $totalAyat) * 100, 2);

            // Tentukan status baru
            if ($request->tgl_setoran > $target->tgl_target) {
                $statusHistori = 3; // Terlambat
            } elseif ($persentaseBaru >= 99.99) {
                $statusHistori = 2; // Selesai
            } else {
                $statusHistori = 1; // Proses
            }

            // Buat histori baru
            Histori::create([
                'id_santri' => $request->id_santri,
                'id_target' => $target->id_target,
                'id_surat' => $request->id_surat,
                'persentase' => $persentaseBaru,
                'id_setoran' => $setoran->id_setoran,
                'status' => $statusHistori,
            ]);
        }


        return redirect()->route('setoran.index')->with('success', 'Setoran berhasil ditambahkan');
    }





    public function edit(Setoran $setoran)
    {
        $santris = Santri::all();
        $kelas = Kelas::all();
        $surats = Surat::all();
        $pengajars = Pengajar::all();
        $targets = Target::all(); // Ambil data target
        return view('setoran.edit', compact('setoran', 'santris', 'kelas', 'pengajars', 'targets', 'surats'));
    }

    public function update(Request $request, Setoran $setoran)
    {
        // dd($request->all());
        // Validasi input
        $request->validate([
            'id_santri' => 'required|exists:santris,id_santri',
            'tgl_setoran' => 'required|date',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'id_surat' => 'required',
            'id_pengajar' => 'nullable', // Pengajar boleh null
            'nilai' => 'required|numeric|min:0|max:100',
            'jumlah_ayat_start' => 'required|numeric',
            'jumlah_ayat_end' => 'required|numeric',
            'id_group' => 'nullable', // Bisa kosong jika tidak diubah
        ]);

        // Pengecekan apakah id_kelas sesuai dengan santri
        $santri = Santri::find($request->id_santri);
        if (!$santri) {
            return back()->withErrors(['id_santri' => 'Santri tidak ditemukan.'])->withInput();
        }
        if ($santri->id_kelas != $request->id_kelas) {
            return back()->withErrors(['id_kelas' => 'Santri ini terdaftar di kelas ' . $santri->kelas->nama_kelas . ', bukan di kelas yang dipilih.'])->withInput();
        }

        // Ambil target berdasarkan 4 parameter utama
        $target = Target::where([
            ['id_santri', '=', $request->id_santri],
            ['id_kelas', '=', $request->id_kelas],
            ['id_surat', '=', $request->id_surat],
            ['id_group', '=', $request->id_group ?? $setoran->target->id_group] // Jika id_group tidak ada, gunakan grup yang lama
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
            return redirect()->back()->withErrors(['jumlah_ayat_end' => 'Jumlah ayat akhir tidak boleh lebih kecil dari target awal ayat surat ini, jumlah ayat target awal adalah ' . $target->jumlah_ayat_target_awal]);
        }
        if ($request->tgl_setoran < $target->tgl_mulai) {
            return redirect()->back()->withErrors(['tgl_setoran' => 'tanggal setoran tidak boleh sebelum tanggal mulai target, tanggal mulainya adalah ' . $target->tanggal_mulai]);
        }


        // Ambil setoran sebelumnya untuk validasi tumpang tindih
        $setorans = Setoran::where('id_target', $target->id_target)
            ->where('id_setoran', '!=', $setoran->id_setoran) // Jangan periksa setoran yang sedang diedit
            ->orderBy('jumlah_ayat_start') // Urutkan berdasarkan jumlah_ayat_start
            ->get();

        // Periksa apakah jumlah_ayat_start yang diinputkan valid
        $valid = false;
        $previousEnd = 0; // Nilai sebelumnya untuk perbandingan

        foreach ($setorans as $setoranItem) {
            // Cek apakah ayat yang diminta tidak tumpang tindih dengan setoran yang ada
            if ($request->jumlah_ayat_start >= $previousEnd && $request->jumlah_ayat_start < $setoranItem->jumlah_ayat_start) {
                $valid = true;
                break;
            }
            $previousEnd = $setoranItem->jumlah_ayat_end;
        }

        // Cek apakah ayat terakhir berada setelah setoran terakhir
        if (!$valid && $request->jumlah_ayat_start > $previousEnd && $request->jumlah_ayat_start <= $target->jumlah_ayat_target) {
            $valid = true; // jika itu ayat terakhir
        }

        // Ambil jumlah ayat akhir pada setoran sebelumnya untuk digunakan dalam pesan error
        $previousEndAyat = $previousEnd; // Nilai sebelumnya untuk ayat akhir

        if (!$valid) {
            return redirect()->back()->withErrors([
                'jumlah_ayat_start' => 'Jumlah ayat mulai tidak boleh lebih kecil dari jumlah ayat akhir pada setoran sebelumnya (ayat akhir sebelumnya: ' . $previousEndAyat . '), dan harus berada di rentang yang kosong antara setoran-setoran yang ada.'
            ]);
        }

        // Hitung persentase setoran
        $totalAyat = $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal;
        $persentase = (($request->jumlah_ayat_end - $request->jumlah_ayat_start + 1) / $totalAyat) * 100;

        // Tentukan status berdasarkan persentase
        $status = $persentase >= 100 ? '1' : '0'; // 1 untuk selesai, 0 untuk proses

        // Pengecekan apakah id_kelas sesuai dengan santri
        $santri = Santri::findOrFail($request->id_santri);
        if ($santri->id_kelas != $request->id_kelas) {
            return back()->withErrors(['id_kelas' => 'Santri ini terdaftar di kelas ' . $santri->kelas->nama_kelas . ', bukan di kelas yang dipilih.'])->withInput();
        }


        // Update data setoran dengan id_target yang sesuai dan status yang dihitung
        $setoran->update([
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
            'persentase' => $persentase, // Tambahkan kolom persentase
        ]);

        // Ambil histori yang terkait dengan setoran yang baru dimasukkan
        $histori = Histori::where('id_santri', $request->id_santri)
            ->where('id_target', $target->id_target)
            ->where('id_surat', $request->id_surat)
            ->first();

        // Jika histori ditemukan, perbarui status dan persentase
        if ($histori) {
            // Hitung total ayat yang sudah disetorkan termasuk setoran yang baru
            $totalAyatDisetorkan = Setoran::where('id_target', $target->id_target)
                ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

            // Hitung persentase baru
            $totalAyat = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);
            $persentaseBaru = number_format(($totalAyatDisetorkan / $totalAyat) * 100, 2);

            // Update persentase dan id_setoran di histori
            $histori->persentase = $persentaseBaru;
            $histori->id_setoran = $setoran->id_setoran;

            // Tentukan status berdasarkan total ayat yang disetorkan dan tanggal setoran
            if ($request->tgl_setoran > $target->tgl_target) {
                $statusHistori = 3; // Terlambat
            } elseif ($persentaseBaru >= 100) {
                $statusHistori = 2; // Selesai
            } else {
                $statusHistori = 1; // Proses
            }

            // Update status histori
            $histori->status = $statusHistori;
            $histori->save();
        } else {
            // Jika histori tidak ditemukan, buat histori baru
            $totalAyatDisetorkan = Setoran::where('id_target', $target->id_target)
                ->sum(DB::raw('jumlah_ayat_end - jumlah_ayat_start + 1'));

            // Hitung persentase baru
            $totalAyat = max(1, $target->jumlah_ayat_target - $target->jumlah_ayat_target_awal + 1);
            $persentaseBaru = number_format(($totalAyatDisetorkan / $totalAyat) * 100, 2);

            // Tentukan status baru
            if ($request->tgl_setoran > $target->tgl_target) {
                $statusHistori = 3; // Terlambat
            } elseif ($persentaseBaru >= 99.99) {
                $statusHistori = 2; // Selesai
            } else {
                $statusHistori = 1; // Proses
            }

            // Buat histori baru
            Histori::create([
                'id_santri' => $request->id_santri,
                'id_target' => $target->id_target,
                'id_surat' => $request->id_surat,
                'persentase' => $persentaseBaru,
                'id_setoran' => $setoran->id_setoran,
                'status' => $statusHistori,
            ]);
        }


        return redirect()->route('setoran.index')->with('success', 'Setoran berhasil diperbarui');
    }

    public function fnGetData()
    {
        $setorans = Setoran::with(['santri', 'kelas', 'targets', 'pengajar'])->get();
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
                return '<a href="' . route('setoran.edit', $row->id_setoran) . '" class="btn btn-sm btn-warning">Edit</a>
                        <button class="btn btn-sm btn-danger btnDelete" data-id="' . $row->id_setoran . '">Hapus</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
