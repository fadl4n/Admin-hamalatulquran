<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Infaq;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class InfaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $tanggal = $request->input('tgl_infaq', Carbon::now()->toDateString());

        // Ambil semua kelas
        $semuaKelas = Kelas::all();

        // Ambil semua infaq yang sesuai tanggal
        $infaqs = Infaq::where('tgl_infaq', $tanggal)->get()->keyBy('id_kelas');

        return view('infaq.show', [
            'kelasList' => $semuaKelas,
            'infaqs' => $infaqs,
            'tanggal' => $tanggal,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelasList = Kelas::all();
        return view('infaq.create', compact('kelasList'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'tgl_infaq' => 'required|date',
            'nominal_infaq' => 'required|integer|min:0',
        ]);

        // Cek apakah data infaq sudah ada untuk kelas dan tanggal yg sama
        $exists = Infaq::where('id_kelas', $request->id_kelas)
            ->where('tgl_infaq', $request->tgl_infaq)
            ->first();

        if ($exists) {
            return back()->withInput()->withErrors(['id_kelas' => 'Data infaq untuk kelas dan tanggal ini sudah ada.']);
        }

        Infaq::create([
            'id_kelas' => $request->id_kelas,
            'tgl_infaq' => $request->tgl_infaq,
            'nominal_infaq' => $request->nominal_infaq,
        ]);

        return redirect()->route('infaq.index')->with('success', 'Data infaq berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $infaq = Infaq::findOrFail($id);
        $kelasList = Kelas::all();

        return view('infaq.edit', compact('infaq', 'kelasList'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $infaq = Infaq::findOrFail($id);

        $request->validate([
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'tgl_infaq' => 'required|date',
            'nominal_infaq' => 'required|integer|min:0',
        ]);

        // Cek apakah data infaq sudah ada untuk kelas dan tanggal yg sama (kecuali data saat ini)
        $exists = Infaq::where('id_kelas', $request->id_kelas)
            ->where('tgl_infaq', $request->tgl_infaq)
            ->where('id', '!=', $infaq->id)
            ->first();

        if ($exists) {
            return back()->withInput()->withErrors(['id_kelas' => 'Data infaq untuk kelas dan tanggal ini sudah ada.']);
        }

        $infaq->update([
            'id_kelas' => $request->id_kelas,
            'tgl_infaq' => $request->tgl_infaq,
            'nominal_infaq' => $request->nominal_infaq,
        ]);

        return redirect()->route('infaq.index')->with('success', 'Data infaq berhasil diperbarui.');
    }
}
