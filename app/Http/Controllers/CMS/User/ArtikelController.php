<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artikel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ArtikelController extends Controller
{
    public function index()
{
    // Ambil semua artikel yang belum expired atau tidak memiliki expired_at
    $articles = Artikel::where(function ($query) {
        $query->whereNull('expired_at')
              ->orWhere('expired_at', '>=', Carbon::now());
    })
    ->latest()
    ->get();

    // Kirim data artikel ke view `show.blade.php`
    return view('artikel.show', compact('articles'));
}

public function create()
{
    // Langsung tampilkan form tambah artikel
    return view('artikel.create');
}


    public function store(Request $request)
    {
        // Validasi input selain gambar
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'expired_at' => 'nullable|date|after_or_equal:today',
        ]);

        // Set gambar default
        $data['gambar'] = asset('assets/image/default.png');

        // Jika ada file gambar yang diupload
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $allowedFileTypes = ['png', 'jpg', 'jpeg'];
            $extension = $file->getClientOriginalExtension();

            // Validasi tipe file
            if (!in_array($extension, $allowedFileTypes)) {
                return redirect()->back()->with('error', 'Tipe file tidak diizinkan. Hanya file png, jpg, dan jpeg yang diizinkan.');
            }

            // Buat nama file unik
            $name_original = date('YmdHis') . '_' . $file->getClientOriginalName();

            // Simpan file ke folder public
            $file->move(public_path('uploadedFile/image/santri'), $name_original);

            // Simpan path gambar
            $data['gambar'] = url('uploadedFile/image/santri') . '/' . $name_original;
        }

        // Simpan data artikel
        Artikel::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'gambar' => $data['gambar'], // Simpan path gambar, bukan array
            'expired_at' => $request->expired_at
                ? Carbon::parse($request->expired_at)->endOfDay() // Set jam ke 23:59:59
                : null,
        ]);

        return redirect()->route('artikel.index')->with('success', 'Artikel berhasil ditambahkan');
    }


    public function edit($id)
    {
        $artikels = Artikel::findOrFail($id);
        return view('artikel.edit', compact('artikels'));
    }

    public function update(Request $request, $id)
{
    // Validasi input termasuk gambar
    $request->validate([
        'judul' => 'required|string|max:255',
        'deskripsi' => 'required',
        'expired_at' => 'nullable|date|after_or_equal:today',
    ]);

    // Ambil data artikel berdasarkan ID
    $article = Artikel::findOrFail($id);

    // Jika tidak ada gambar baru yang diunggah, gunakan gambar yang lama
    $gambar = $article->gambar;

    // Jika ada file gambar yang diupload
    if ($request->hasFile('gambar')) {
        $file = $request->file('gambar');
        $allowedFileTypes = ['png', 'jpg', 'jpeg'];
        $extension = $file->getClientOriginalExtension();

        // Validasi tipe file
        if (!in_array($extension, $allowedFileTypes)) {
            return redirect()->back()->with('error', 'Tipe file tidak diizinkan. Hanya file png, jpg, dan jpeg yang diizinkan.');
        }

        // Hapus gambar lama jika ada
        if ($article->gambar && $article->gambar !== asset('assets/image/default.png')) {
            $oldImagePath = str_replace(url('/'), public_path(), $article->gambar);
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Buat nama file unik
        $name_original = date('YmdHis') . '_' . $file->getClientOriginalName();

        // Simpan file ke folder public
        $file->move(public_path('uploadedFile/image/santri'), $name_original);

        // Simpan path gambar baru
        $gambar = url('uploadedFile/image/santri') . '/' . $name_original;
    }

    // Update data artikel
    $article->update([
        'judul' => $request->judul,
        'deskripsi' => $request->deskripsi,
        'gambar' => $gambar, // Simpan path gambar
        'expired_at' => $request->expired_at
            ? Carbon::parse($request->expired_at)->endOfDay() // Set jam ke 23:59:59
            : null,
    ]);

    return redirect()->route('artikel.index')->with('success', 'Artikel berhasil diperbarui');
}



    public function destroy($id)
    {
        $article = Artikel::findOrFail($id);
        Storage::delete('public/' . $article->gambar);
        $article->delete();
        return redirect()->route('artikel.index')->with('success', 'Artikel berhasil dihapus');
    }

    public function fnGetData()
    {
        // Ambil hanya artikel yang belum expired
        $articles = Artikel::where(function ($query) {
            $query->whereNull('expired_at')
                  ->orWhere('expired_at', '>=', Carbon::now());
        })
        ->latest()
        ->select(['id', 'judul', 'deskripsi', 'gambar', 'expired_at']);

        return datatables()->of($articles)
        ->addIndexColumn()
        ->addColumn('action', function ($article) {
                return '
                    <a href="' . route('artikel.edit', $article->id) . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                    <form action="' . route('artikel.destroy', $article->id) . '" method="POST" class="d-inline">
                        ' . csrf_field() . method_field("DELETE") . '
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus?\')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                ';
            })
            ->editColumn('gambar', function ($article) {
                $imageUrl = $article->gambar ? asset($article->gambar) : asset('uploadedFile/image/artikel/default.png');
                return '<img src="' . $imageUrl . '" width="100" alt="Gambar Artikel">';
            })
            ->editColumn('expired_at', function ($article) {
                return $article->expired_at ? Carbon::parse($article->expired_at)->format('Y-m-d') : '-';
            })
            ->editColumn('deskripsi', function ($article) {
                return strip_tags(substr($article->deskripsi, 0, 50));
            })

            ->rawColumns(['action', 'gambar'])
            ->make(true);
    }



}
