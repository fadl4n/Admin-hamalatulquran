<?php

namespace App\Http\Controllers\CMS\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;
use App\Models\Kelas;
use App\Models\Keluarga;
use App\Models\Histori;
use App\Models\Target;
use App\Models\Setoran;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Barryvdh\DomPDF\Facade\Pdf;

class SantriController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Santri::with('kelas')
                ->orderBy('id_kelas')
                ->get();
            return DataTables::of($data)
                ->addColumn('nama_kelas', function ($row) {
                    return $row->kelas ? $row->kelas->nama_kelas : 'Tidak Ada Kelas';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . url('santri/show/' . $row->id_santri) . '" class="btn btn-primary btn-sm" title="Preview">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="' . url('santri/' . $row->id_santri . '/edit') . '" class="btn btn-warning btn-sm">Edit</a>
                            <button class="btn btn-danger btn-sm btnDelete" data-id="' . $row->id_santri . '">Hapus</button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('santri.show');
    }
public function show($id)
{
    $santri = Santri::with(['kelas', 'keluarga'])->findOrFail($id);

    // Ambil semua target milik santri
    $targets = Target::where('id_santri', $id)->with('surat')->get();

    $hafalan = [];
    $murojaah = [];

    foreach ($targets as $target) {
        $namaSurat = $target->surat->nama_surat ?? '-';

        $nilaiHafalan = Setoran::where('id_target', $target->id_target)->avg('nilai');
        $ayatHafalanStart = Setoran::where('id_target', $target->id_target)->min('jumlah_ayat_start');
        $ayatHafalanEnd = Setoran::where('id_target', $target->id_target)->max('jumlah_ayat_end');
        $ayatHafalan = ($ayatHafalanStart && $ayatHafalanEnd) ? "$ayatHafalanStart - $ayatHafalanEnd" : '-';

        $nilaiMurojaah = Histori::where('id_target', $target->id_target)->avg('nilai');
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

    return view('santri.detail', compact('santri', 'hafalan', 'murojaah'));
}

    public function create()
    {
        $kelas = Kelas::all();
        return view('santri.create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|numeric|digits_between:1,10',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'alamat' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'angkatan' => 'nullable|integer|min:2000|max:'.(date('Y') + 1),
            'id_kelas' => 'nullable|exists:kelas,id_kelas',
            'jenis_kelamin' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
            'password' => 'required|string|min:6',
            'foto_santri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:7168',
        ], [
            'foto_santri.mimes' => 'Format file tidak didukung! Gunakan PNG, JPG, atau JPEG.',
            'foto_santri.max' => 'Ukuran file terlalu besar! Maksimal 7MB.',
        ]);

        // Pengecekan manual untuk NISN dan Email
        if (Santri::where('nisn', $request->nisn)->exists()) {
            return redirect()->back()->withInput()->with('error', 'NISN sudah digunakan.');
        }

        if (Santri::where('email', $request->email)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan.');
        }

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        // $data['foto_santri'] = Storage::url('uploadedFile/image/default-user.png');

        if ($request->hasFile('foto_santri')) {
            $image = $request->file('foto_santri');

            $filename = time() . '.' . $image->getClientOriginalExtension();

            // Create new ImageManager instance with 'gd' driver
            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

            $img = $manager->read($image->getPathname());

            // Encode to JPG (or use $img->toPng() / ->toWebp())
            $encodedImage = $img->toJpeg(75); // kualitas 75%

            // Simpan ke storage/public
            $path = 'uploadedFile/image/santri/' . $filename;
            Storage::disk('public')->put($path, (string) $encodedImage);

            $data['foto_santri'] = asset('storage/' . $path);
        }

        Santri::create($data);

        return redirect('santri')->with('success', 'Santri berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $santri = Santri::with(['keluarga'])->findOrFail($id);


        // Ambil data keluarga berdasarkan hubungan
        $ayah = $santri->keluarga->firstWhere('hubungan', 1);
        $ibu = $santri->keluarga->firstWhere('hubungan', 2);
        $wali = $santri->keluarga->firstWhere('hubungan', 3);

        $kelas = Kelas::all();


        return view('santri.edit', compact('santri', 'ayah', 'ibu', 'wali', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => 'required|numeric|digits_between:1,10',
            'tempat_lahir' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'email' => 'nullable|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'angkatan' => 'nullable|string|max:50',
            'id_kelas' => 'required|exists:kelas,id_kelas',
            'jenis_kelamin' => 'required|integer|in:1,2',
            'status' => 'required|integer|in:0,1',
            'password' => 'nullable|string|min:6',
            'foto_santri' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:7168',
            'nama_ayah' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'pendidikan_ayah' => 'nullable|string|max:255',
            'no_telp_ayah' => 'nullable|string|max:20',
            'alamat_ayah' => 'nullable|string',
            'status_ayah' => 'nullable|integer',
            'email_ayah' => 'nullable|email|max:255',
            'tempat_lahir_ayah' => 'nullable|string|max:255',
            'tgl_lahir_ayah' => 'nullable|date',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'pendidikan_ibu' => 'nullable|string|max:255',
            'no_telp_ibu' => 'nullable|string|max:20',
            'alamat_ibu' => 'nullable|string',
            'status_ibu' => 'nullable|integer',
            'email_ibu' => 'nullable|email|max:255',
            'tempat_lahir_ibu' => 'nullable|string|max:255',
            'tgl_lahir_ibu' => 'nullable|date',
            'nama_wali' => 'nullable|string|max:255',
            'pekerjaan_wali' => 'nullable|string|max:255',
            'pendidikan_wali' => 'nullable|string|max:255',
            'no_telp_wali' => 'nullable|string|max:20',
            'alamat_wali' => 'nullable|string',
            'status_wali' => 'nullable|integer',
            'email_wali' => 'nullable|email|max:255',
            'tempat_lahir_wali' => 'nullable|string|max:255',
            'tgl_lahir_wali' => 'nullable|date',
        ]);

        $santri = Santri::findOrFail($id);
        $data = $request->except('password', 'foto_santri');

        // Cek duplikasi NISN & email
        if (Santri::where('nisn', $request->nisn)->where('id_santri', '!=', $santri->id_santri)->exists()) {
            return redirect()->back()->withInput()->with('error', 'NISN sudah digunakan oleh santri lain.');
        }
        if (Santri::where('email', $request->email)->where('id_santri', '!=', $santri->id_santri)->exists()) {
            return redirect()->back()->withInput()->with('error', 'Email sudah digunakan oleh santri lain.');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

       if ($request->hasFile('foto_santri')) {
    $image = $request->file('foto_santri');
    $filename = time() . '.' . $image->getClientOriginalExtension();

    $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
    $img = $manager->read($image->getPathname());
    $encodedImage = $img->toJpeg(75);

    // Hapus file lama jika bukan default
    $defaultFoto = 'uploadedFile/image/default-user.png';
    if (!empty($santri->foto_santri) && basename($santri->foto_santri) !== basename($defaultFoto)) {
        Storage::disk('public')->delete('uploadedFile/image/santri/' . basename($santri->foto_santri));
    }

    // Simpan file baru
    $relativePath = 'uploadedFile/image/santri/' . $filename;
    Storage::disk('public')->put($relativePath, (string) $encodedImage);

    // Simpan path relatif
    $data['foto_santri'] = 'storage/' . $relativePath;
}


        // Update data keluarga
        $this->updateKeluarga($santri->id_santri, 1, $request->all(), 'ayah');
        $this->updateKeluarga($santri->id_santri, 2, $request->all(), 'ibu');
        $this->updateKeluarga($santri->id_santri, 3, $request->all(), 'wali');

        $santri->update($data);

        return redirect()->route('santri.index')->with('success', 'Santri berhasil diperbarui.');
    }

    private function updateKeluarga($id_santri, $hubungan, $data, $prefix)
    {
        // Set default values as null if not present
        $keluargaData = [
            'nama' => $data['nama_' . $prefix] ?? null,
            'pekerjaan' => $data['pekerjaan_' . $prefix] ?? null,
            'pendidikan' => $data['pendidikan_' . $prefix] ?? null,
            'no_telp' => $data['no_telp_' . $prefix] ?? null,
            'alamat' => $data['alamat_' . $prefix] ?? null,
            'status' => $data['status_' . $prefix] ?? null,
            'email' => $data['email_' . $prefix] ?? null,
            'tempat_lahir' => $data['tempat_lahir_' . $prefix] ?? null,
            'tgl_lahir' => $data['tgl_lahir_' . $prefix] ?? null,
        ];

        Keluarga::updateOrCreate(
            ['id_santri' => $id_santri, 'hubungan' => $hubungan],
            $keluargaData
        );
    }

    public function destroy($id)
    {
        $santri = Santri::findOrFail($id);
        if ($santri->foto_santri) {
            Storage::disk('public')->delete($santri->foto_santri);
        }
        $santri->delete();

        return response()->json(['success' => 'Santri berhasil dihapus.']);
    }

    public function fnGetData(Request $request)
{
    $santris = Santri::with('kelas')->select(['id_santri', 'nama', 'nisn', 'angkatan', 'id_kelas']);

    // Pengurutan dari DataTables
    if ($request->has('order')) {
        $orderColumnIndex = $request->input('order.0.column');
        $orderDirection = $request->input('order.0.dir');

        $columns = ['id_kelas', 'nama', 'nisn', 'angkatan'];

        if ($orderColumnIndex == 0) {
            $santris->orderBy('id_kelas', 'asc')->orderBy('nama', $orderDirection);
        } elseif ($orderColumnIndex == 1) {
            $santris->orderBy('id_kelas', 'asc')->orderBy('nama', 'asc');
        } else {
            $santris->orderBy('id_kelas', 'asc')->orderBy('nama', 'asc');
        }
    } else {
        $santris->orderBy('id_kelas', 'asc')->orderBy('nama', 'asc');
    }

    return DataTables::of($santris)
        ->addColumn('nama_kelas', function ($santri) {
            return $santri->kelas ? $santri->kelas->nama_kelas : 'Tidak Ada Kelas';
        })
        ->filterColumn('nama_kelas', function ($query, $keyword) {
            $query->whereHas('kelas', function ($q) use ($keyword) {
                $q->where('nama_kelas', 'like', "%$keyword%");
            });
        })
        ->addColumn('action', function ($santri) {
            return '<a href="' . url('santri/show/' . $santri->id_santri) . '" class="btn btn-primary btn-sm" title="Preview">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="' . url('santri/edit/' . $santri->id_santri) . '" class="btn btn-warning btn-sm" title="Edit">
                        <i class="fa fa-edit"></i>
                    </a>
                    <button class="btn btn-danger btn-sm btnDelete" data-id="' . $santri->id_santri . '" title="Hapus">
                        <i class="fas fa-trash"></i>
                    </button>';
        })
        ->rawColumns(['action'])
        ->make(true);
}
public function downloadPdf($id)
{
    $santri = Santri::with(['kelas', 'keluarga'])->findOrFail($id);
    $targets = Target::where('id_santri', $id)->with('surat')->get();

    $ayah = $santri->keluarga->firstWhere('hubungan', 1);
    $ibu = $santri->keluarga->firstWhere('hubungan', 2);
    $wali = $santri->keluarga->firstWhere('hubungan', 3);

    $hafalan = [];
    $murojaah = [];

    foreach ($targets as $target) {
        $namaSurat = $target->surat->nama_surat ?? '-';

        $nilaiHafalan = Setoran::where('id_target', $target->id_target)->avg('nilai');
        $ayatHafalanStart = Setoran::where('id_target', $target->id_target)->min('jumlah_ayat_start');
        $ayatHafalanEnd = Setoran::where('id_target', $target->id_target)->max('jumlah_ayat_end');
        $ayatHafalan = ($ayatHafalanStart && $ayatHafalanEnd) ? "$ayatHafalanStart - $ayatHafalanEnd" : '-';

        $nilaiMurojaah = Histori::where('id_target', $target->id_target)->avg('nilai');
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

    $pdf = Pdf::loadView('santri.pdf', compact('santri', 'ayah', 'ibu', 'wali', 'hafalan', 'murojaah'))
                ->setPaper('A4', 'portrait');

    return $pdf->download('detail-santri.pdf');
}

}
