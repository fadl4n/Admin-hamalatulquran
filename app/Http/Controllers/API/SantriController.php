<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Santri;

class SantriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $santris = Santri::with(['kelas', 'keluarga'])->get();

        $data = $santris->map(function ($santri) {
            $ayah = $santri->keluarga->firstWhere('hubungan', 1);
            $ibu  = $santri->keluarga->firstWhere('hubungan', 2);
            $wali = $santri->keluarga->firstWhere('hubungan', 3);

            return [
                'id_santri'       => $santri->id_santri,
                'nama'            => $santri->nama,
                'nisn'            => $santri->nisn,
                'tempat_lahir'    => $santri->tempat_lahir,
                'tgl_lahir'       => $santri->tgl_lahir,
                'email'           => $santri->email,
                'alamat'          => $santri->alamat,
                'angkatan'        => $santri->angkatan,
                'jenis_kelamin'   => $santri->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan',
                'status'          => $santri->status == 1 ? 'Aktif' : 'Tidak Aktif',
                'kelas'           => $santri->kelas ? $santri->kelas->nama_kelas : 'Tidak Ada Kelas',
                'foto'            => $santri->foto_santri ? asset($santri->foto_santri) : asset('assets/image/default-user.png'),
                'ayah' => [
                    'nama'         => $ayah->nama ?? null,
                    'pekerjaan'    => $ayah->pekerjaan ?? null,
                    'pendidikan'   => $ayah->pendidikan ?? null,
                    'no_telp'      => $ayah->no_telp ?? null,
                    'alamat'       => $ayah->alamat ?? null,
                    'email'        => $ayah->email ?? null,
                    'tempat_lahir' => $ayah->tempat_lahir ?? null,
                    'tgl_lahir'    => $ayah->tgl_lahir ?? null,
                ],
                'ibu' => [
                    'nama'         => $ibu->nama ?? null,
                    'pekerjaan'    => $ibu->pekerjaan ?? null,
                    'pendidikan'   => $ibu->pendidikan ?? null,
                    'no_telp'      => $ibu->no_telp ?? null,
                    'alamat'       => $ibu->alamat ?? null,
                    'email'        => $ibu->email ?? null,
                    'tempat_lahir' => $ibu->tempat_lahir ?? null,
                    'tgl_lahir'    => $ibu->tgl_lahir ?? null,
                ],
                'wali' => [
                    'nama'         => $wali->nama ?? null,
                    'pekerjaan'    => $wali->pekerjaan ?? null,
                    'pendidikan'   => $wali->pendidikan ?? null,
                    'no_telp'      => $wali->no_telp ?? null,
                    'alamat'       => $wali->alamat ?? null,
                    'email'        => $wali->email ?? null,
                    'tempat_lahir' => $wali->tempat_lahir ?? null,
                    'tgl_lahir'    => $wali->tgl_lahir ?? null,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Data semua santri berhasil diambil.',
            'data'    => $data
        ]);
    }


}
