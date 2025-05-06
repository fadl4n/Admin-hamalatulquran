<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Surat;
use Exception;

class SuratController extends Controller
{
    public function index()
    {
        $data = Surat::all(); // atau kamu bisa sorting berdasarkan nomor surat
        return response()->json($data);
    }
}
