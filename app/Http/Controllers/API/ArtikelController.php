<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artikel;
use Carbon\Carbon;

class ArtikelController extends Controller
{
    /**
     * Display a listing of the resource.
     */

public function index()
{
    $today = Carbon::today();

    $artikel = Artikel::whereDate('expired_at', '>=', $today)
        ->get(['judul', 'gambar', 'deskripsi']); // hanya ambil data yang belum expired

    return response()->json([
        'data' => $artikel
    ]);
}


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
