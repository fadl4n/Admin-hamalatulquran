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
        $query = Santri::with('kelas', 'targets');

        // Jika ada input pencarian
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                    ->orWhere('nisn', 'like', "%$search%")
                    ->orWhereHas('kelas', function ($kelasQuery) use ($search) {
                        $kelasQuery->where('nama_kelas', 'like', "%$search%");
                    })
                    ->orWhereHas('targets', function ($targetQuery) use ($search) {
                        // Deteksi jika input mengandung "Target X" atau angka langsung
                        if (preg_match('/^Target (\d+)$/i', $search, $matches)) {
                            $idGroup = $matches[1]; // Ambil angka setelah "Target "
                            $targetQuery->where('id_group', $idGroup);
                        } elseif (is_numeric($search)) {
                            $targetQuery->where('id_group', $search);
                        } else {
                            $targetQuery->where('id_group', 'like', "%$search%");
                        }
                    });
            });
        }

        $santris = $query->get();

        return view('nilai.show', compact('santris'));
    }



    /**
     * Menampilkan detail nilai hafalan dan muroja'ah untuk seorang santri berdasarkan id_group.
     */
    public function show($idSantri, $idGroup)
    {
        $santri = Santri::findOrFail($idSantri);
        // Ambil target berdasarkan santri dan id_group
        $targets = Target::where('id_santri', $idSantri)
            ->where('id_group', $idGroup)
            ->get();

        // Buat array untuk menyimpan nilai hafalan dan muroja'ah
        $hafalan = [];
        $murojaah = [];

        foreach ($targets as $target) {
            $namaSurat = $target->surat->nama_surat;

            // Hitung rata-rata nilai dari setoran (hafalan)
            $nilaiHafalan = Setoran::where('id_target', $target->id_target)
                ->avg('nilai');

            // Hitung rata-rata nilai dari histori (muroja'ah)
            $nilaiMurojaah = Histori::where('id_target', $target->id_target)
                ->avg('nilai');

            $hafalan[] = [
                'surat' => $namaSurat,
                'nilai' => number_format($nilaiHafalan ?? 0, 2)
            ];

            $murojaah[] = [
                'surat' => $namaSurat,
                'nilai' => number_format($nilaiMurojaah ?? 0, 2)
            ];
        }

        return view('nilai.detail', compact('hafalan', 'murojaah', 'idGroup', 'santri'));
    }
}
