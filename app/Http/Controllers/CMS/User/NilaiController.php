<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Santri;
use App\Models\Target;
use App\Models\Setoran;
use App\Models\Histori;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    /**
     * Menampilkan daftar santri dengan opsi untuk melihat detail nilai.
     */
    public function index(Request $request)
    {
        $query = Santri::with('kelas', 'target');

        // Jika ada input pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%")
                    ->orWhereHas('kelas', function ($kelasQuery) use ($search) {
                        $kelasQuery->where('nama_kelas', 'like', "%$search%");
                    })
                    ->orWhereHas('target', function ($targetQuery) use ($search) {
                        // Deteksi jika input mengandung "Target X" atau angka langsung
                        if (preg_match('/^Target (\d+)$/i', $search, $matches)) {
                            $idtarget = $matches[1]; // Ambil angka setelah "Target "
                            $targetQuery->where('id_target', $idtarget);
                        } elseif (is_numeric($search)) {
                            $targetQuery->where('id_target', $search);
                        } else {
                            $targetQuery->where('id_target', 'like', "%$search%");
                        }
                    });
            });
        }

        $santris = $query->get();

        return view('nilai.show', compact('santris'));
    }



    /**
     * Menampilkan detail nilai hafalan dan muroja'ah untuk seorang santri berdasarkan id_target.
     */
   public function show($idSantri, $idtarget,  Request $request)
{
    $santri = Santri::findOrFail($idSantri);

  $searchSurat = $request->query('search_surat');

    // Query Target berdasarkan id_santri
    $targets = Target::where('id_santri', $idSantri)
        ->when($searchSurat, function ($query, $searchSurat) {
            // Filter surat dengan relasi 'surat' dan kolom 'nama_surat' LIKE search
            $query->whereHas('surat', function($q) use ($searchSurat) {
                $q->where('nama_surat', 'like', "%$searchSurat%");
            });
        })
        ->get();

    $hafalan = [];
    $murojaah = [];

    foreach ($targets as $target) {
        $namaSurat = $target->surat->nama_surat;

        // Rata-rata nilai hafalan dari setoran
        $nilaiHafalan = Setoran::where('id_target', $target->id_target)->avg('nilai');

        // Ambil ayat start-end dari setoran hafalan, ambil yang min dan max
        $ayatHafalanStart = Setoran::where('id_target', $target->id_target)->min('jumlah_ayat_start');
        $ayatHafalanEnd = Setoran::where('id_target', $target->id_target)->max('jumlah_ayat_end');
        $ayatHafalan = ($ayatHafalanStart && $ayatHafalanEnd) ? "$ayatHafalanStart - $ayatHafalanEnd" : '-';

        // Rata-rata nilai murojaah dari histori
        $nilaiMurojaah = Histori::where('id_target', $target->id_target)->avg('nilai');

        // Ayat murojaah dari target range jumlah_ayat_target_awal sampai jumlah_ayat_target
    $ayatMurojaahStart = $target->jumlah_ayat_target_awal;
    $ayatMurojaahEnd = $target->jumlah_ayat_target;
    $ayatMurojaah = ($ayatMurojaahStart && $ayatMurojaahEnd) ? "$ayatMurojaahStart - $ayatMurojaahEnd" : '-';


        $hafalan[] = [
            'surat' => $namaSurat,
            'nilai' => number_format($nilaiHafalan ?? 0, 2),
            'ayat'  => $ayatHafalan,
        ];

        $murojaah[] = [
            'surat' => $namaSurat,
            'nilai' => number_format($nilaiMurojaah ?? 0, 2),
            'ayat'  => $ayatMurojaah,
        ];
    }

    return view('nilai.detail', compact('hafalan', 'murojaah', 'idtarget', 'santri'));
}

    public function fnGetData(Request $request)
    {
        $santris = Santri::with('kelas');

        // If there's a search input
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            $santris->where(function ($query) use ($search) {
                $query->where('nama', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%")
                    ->orWhereHas('kelas', function ($kelasQuery) use ($search) {
                        $kelasQuery->where('nama_kelas', 'like', "%$search%");
                    });
            });
        }

        // Paginate the results
        $santris = $santris->paginate(10); // Adjust the number per page as needed

        // Map the result into the correct format for DataTable
        $data = $santris->items();

        $data = array_map(function ($santri) {
            return [
                'nama' => $santri->nama,
                'nisn' => $santri->nisn,
                'kelas' => $santri->kelas->nama_kelas ?? '-',
                'action' => '<a href="' . route('nilai.show', ['idSantri' => $santri->id_santri, 'idtarget' => 1]) . '" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>',
            ];
        }, $data);

        return response()->json([
            'data' => $data,
            'recordsTotal' => $santris->total(),  // Total records without filter
            'recordsFiltered' => $santris->total() // Total records after filter
        ]);
    }


}
