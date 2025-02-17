<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use App\Models\Setoran;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Surat;
use App\Models\Pengajar;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class SetoranController extends Controller
{
    public function index()
    {
        $setorans = Setoran::with(['santri', 'kelas', 'surat','pengajar'])->get();

        return view('setoran.show', compact('setorans'));
    }

    public function create()
    {
        $santris = Santri::all();
        $kelas = Kelas::all();
        $surats = Surat::all();
        $pengajars = Pengajar::all();
        return view('setoran.create', compact('santris', 'kelas', 'surats','pengajars'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'id_santri' => 'required',
            'tgl_setoran' => 'required|date',
            'status' => 'required',
            'id_kelas' => 'required',
            'id_surat' => 'required',
            'id_pengajar' => 'required',
            'jumlah_ayat_start' => 'required|numeric',
            'jumlah_ayat_end' => 'required|numeric',
        ]);

        // Ambil data surat yang dipilih
        $surat = Surat::find($request->id_surat);
        if (!$surat) {
            return redirect()->back()->withErrors(['surat' => 'Surat tidak ditemukan.']);
        }

        // Validasi apakah jumlah_ayat_end lebih besar dari jumlah ayat surat
        if ($request->jumlah_ayat_end > $surat->jumlah_ayat) {
            return redirect()->back()->withErrors(['jumlah_ayat_end' => 'Jumlah ayat dalam surat '. $surat->nama_surat . ' adalah ' . $surat->jumlah_ayat . '.']);
        }

        // Validasi apakah jumlah_ayat_start lebih besar dari jumlah_ayat_end
        if ($request->jumlah_ayat_start > $request->jumlah_ayat_end) {
            return redirect()->back()->withErrors(['jumlah_ayat_start' => 'Jumlah ayat mulai tidak boleh lebih besar dari jumlah ayat akhir.']);
        }

        // Simpan data setoran
        Setoran::create([
            'id_santri' => $request->id_santri,
            'tgl_setoran' => $request->tgl_setoran,
            'status' => $request->status,
            'id_kelas' => $request->id_kelas,
            'id_surat' => $request->id_surat,
            'id_pengajar' => $request->id_pengajar,
            'jumlah_ayat_start' => $request->jumlah_ayat_start,
            'jumlah_ayat_end' => $request->jumlah_ayat_end,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('setoran.index')->with('success', 'Setoran berhasil ditambahkan');
    }




    public function edit(Setoran $setoran)
    {
        $santris = Santri::all();
        $kelas = Kelas::all();
        $surats = Surat::all();
        $pengajars = Pengajar::all();
        return view('setoran.edit', compact('setoran', 'santris', 'kelas', 'surats','pengajars'));
    }

    public function update(Request $request, Setoran $setoran)
    {
        $request->validate([
            'id_santri' => 'required',
            'tgl_setoran' => 'required|date',
            'status' => 'required',
            'id_kelas' => 'required',
            'id_pengajar' => 'required',
            'id_surat' => 'required'
        ]);

        $setoran->update($request->all());
        return redirect()->route('setoran.index')->with('success', 'Setoran berhasil diperbarui');
    }

    public function destroy(Setoran $setoran)
    {
        $setoran->delete();
        return response()->json(['success' => 'Setoran berhasil dihapus']);
    }

    public function fnGetData()
    {
        $setorans = Setoran::with(['santri', 'kelas', 'surat'])->get();
        return DataTables::of($setorans)
            ->addIndexColumn()
            ->addColumn('santri', function ($row) {
                return $row->santri->nama ?? '-';
            })
            ->addColumn('kelas', function ($row) {
                return $row->kelas->nama_kelas ?? '-';
            })
            ->addColumn('surat', function ($row) {
                // Menggunakan nama_surat dan jumlah_ayat dari relasi surat
                return $row->surat->nama_surat ?? '-';  // Menggunakan hanya nama_surat saja
            })
            ->addColumn('pengajar', function ($row) {
                return $row->pengajar->nama ?? '-';
            })
            ->addColumn('jumlah_ayat', function ($row) {
                // Menampilkan jumlah_ayat dari relasi surat
                return $row->surat->jumlah_ayat ?? '-';
            })
            ->addColumn('jumlah_ayat_start', function ($row) {
                return $row->jumlah_ayat_start ?? '-';
            })
            ->addColumn('jumlah_ayat_end', function ($row) {
                return $row->jumlah_ayat_end ?? '-';
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
