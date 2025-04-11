<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Pengajar;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data statistik per angkatan
        $santriPerAngkatan = Santri::selectRaw('angkatan, COUNT(*) as count')
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'asc')
            ->get();

        // Kirimkan data ke view
        return view('dashboard', compact('santriPerAngkatan'));
    }

    public function dashboardStatistics()
    {
        // Ambil data jumlah santri, kelas, dan pengajar
        $santriCount = Santri::where('status', 1)->count();
        $kelasCount = Kelas::count();
        $pengajarCount = Pengajar::count();

        // Ambil statistik jumlah santri per angkatan
        $santriPerAngkatan = Santri::selectRaw('angkatan, COUNT(*) as count')
            ->groupBy('angkatan')
            ->get();

        return response()->json([
            'santri_count' => $santriCount,
            'kelas_count' => $kelasCount,
            'pengajar_count' => $pengajarCount,
            'santri_per_angkatan' => $santriPerAngkatan
        ]);
    }
}
