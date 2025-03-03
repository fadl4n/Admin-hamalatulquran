@extends('admin_template')

@section('title page')
    Detail Santri
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="card-title mb-0">Data Pribadi :</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Nama</th>
                                            <td>{{ $santri->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>NISN</th>
                                            <td>{{ $santri->nisn }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tempat, Tanggal Lahir</th>
                                            <td>{{ $santri->tempat_lahir }}, {{ $santri->tgl_lahir }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $santri->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $santri->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <th>Angkatan</th>
                                            <td>{{ $santri->angkatan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Kelamin</th>
                                            <td>{{ $santri->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Kelas</th>
                                            <td>{{ $santri->kelas ? $santri->kelas->nama_kelas : 'Tidak Ada Kelas' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>{{ $santri->status == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-4 text-center">
                                    <img src="{{ $santri->foto_santri ? asset('uploadedFile/image/santri/' . basename($santri->foto_santri)) : asset('assets/image/default-user.png') }}"
                                        alt="Foto Santri" class="img-fluid rounded" width="150">
                                </div>

                            </div>
                            <div class="mt-4 d-flex justify-content-between align-items-center">
                                <h4>Data Orang Tua:</h4>
                            </div>

                            @php
                                $ayah = $santri->keluarga->firstWhere('hubungan', 1);
                                $ibu = $santri->keluarga->firstWhere('hubungan', 2);
                                $wali = $santri->keluarga->firstWhere('hubungan', 3);
                            @endphp

                            <!-- Tabel Ayah dan Ibu -->
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Nama Ayah</th>
                                    <td>{{ $ayah->nama ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan </th>
                                    <td>{{ $ayah->pekerjaan ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan </th>
                                    <td>{{ $ayah->pendidikan ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon </th>
                                    <td>{{ $ayah->no_telp ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat </th>
                                    <td>{{ $ayah->alamat ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Email </th>
                                    <td>{{ $ayah->email ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat, Tanggal Lahir </th>
                                    <td>{{ $ayah->tempat_lahir ?? '' }}, {{ $ayah->tgl_lahir ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Ibu</th>
                                    <td>{{ $ibu->nama ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan </th>
                                    <td>{{ $ibu->pekerjaan ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan </th>
                                    <td>{{ $ibu->pendidikan ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon </th>
                                    <td>{{ $ibu->no_telp ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat </th>
                                    <td>{{ $ibu->alamat ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Email </th>
                                    <td>{{ $ibu->email ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat, Tanggal Lahir </th>
                                    <td>{{ $ibu->tempat_lahir ?? '' }}, {{ $ibu->tgl_lahir ?? '' }}</td>
                                </tr>
                            </table>

                            <!-- Bagian Data Wali -->
                            <div class="mt-4 d-flex justify-content-between align-items-center">
                                <h4>Data Wali:</h4>
                            </div>

                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Nama Wali</th>
                                    <td>{{ $wali->nama ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Pekerjaan Wali</th>
                                    <td>{{ $wali->pekerjaan ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan Wali</th>
                                    <td>{{ $wali->pendidikan ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon Wali</th>
                                    <td>{{ $wali->no_telp ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat Wali</th>
                                    <td>{{ $wali->alamat ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Email Wali</th>
                                    <td>{{ $wali->email ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Tempat, Tanggal Lahir Wali</th>
                                    <td>{{ $wali->tempat_lahir ?? '' }}, {{ $wali->tgl_lahir ?? '' }}</td>
                                </tr>
                            </table>




                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
