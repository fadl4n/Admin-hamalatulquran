<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SantriResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_santri' => $this->id_santri,
            'nama' => $this->nama,
            'nisn' => $this->nisn,
            'id_kelas' => $this->id_kelas,
            'nama_kelas' => $this->kelas->nama_kelas,
            'tgl_lahir' => $this->tgl_lahir,
            'tempat_lahir' => $this->tempat_lahir,
            'alamat' => $this->alamat,
            'email' => $this->email,
            'foto_santri' => $this->foto_santri,
            'angkatan' => $this->angkatan,
            'jenis_kelamin' => $this->jenis_kelamin,
            'status' => $this->status,
        ];
    }
}
