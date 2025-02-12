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
        return view('artikel.show');
    }

    public function create()
    {
        return view('artikel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required',
            'gambar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'expired_at' => 'nullable|date|after_or_equal:today',
        ]);

        $path = $request->file('gambar')->store('artikels', 'public');

        Artikel::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'gambar' => $path,
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
    $request->validate([
        'judul' => 'required|string|max:255',
        'deskripsi' => 'required',
        'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'expired_at' => 'nullable|date|after_or_equal:today',
    ]);

    $article = Artikel::findOrFail($id);

    if ($request->hasFile('gambar')) {
        Storage::delete('public/' . $article->gambar);
        $path = $request->file('gambar')->store('artikels', 'public');
        $article->gambar = $path;
    }

    $article->update([
        'judul' => $request->judul,
        'deskripsi' => $request->deskripsi,
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
    $articles = Artikel::where(function ($query) {
        $query->whereNull('expired_at') // Artikel tanpa expired_at tetap ditampilkan
              ->orWhere('expired_at', '>=', Carbon::now()); // Artikel expired disembunyikan
    })
    ->latest()
    ->select(['id', 'judul', 'deskripsi', 'gambar', 'expired_at']);

    return datatables()->of($articles)
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
            return $article->gambar ? asset('storage/' . $article->gambar) : null;
        })
        ->editColumn('expired_at', function ($article) {
            return $article->expired_at ? Carbon::parse($article->expired_at)->format('Y-m-d H:i:s') : '-';
        })
        ->editColumn('deskripsi', function ($article) {
            return strip_tags(substr($article->deskripsi, 0, 50)) . '...';
        })
        ->rawColumns(['action'])
        ->make(true);
}



}
