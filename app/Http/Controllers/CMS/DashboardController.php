<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Santri;
use App\Models\Pengajar;
use App\Models\Kelas;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
    public function dashboardStatistics()
    {
        // Ambil data jumlah santri, kelas, pengajar, dan statistik berdasarkan usia
        $santriCount = Santri::count();  // Menghitung jumlah santri
        $kelasCount = Kelas::count();    // Menghitung jumlah kelas
        $pengajarCount = Pengajar::count();  // Menghitung jumlah pengajar

 

        return response()->json([
            'santri_count' => $santriCount,
            'kelas_count' => $kelasCount,
            'pengajar_count' => $pengajarCount,

        ]);
    }


}
