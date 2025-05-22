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
                        <div class="card-body">
                            <div class="mt-4 d-flex align-items-center justify-content-between">
                                <div></div> <!-- kosong di kiri supaya judul bisa tepat di tengah -->

                                <h4 class="mb-0">Data Santri</h4>

                                <div>
                                    <a href="{{ route('santri.downloadPdf', $santri->id_santri) }}"
                                        class="btn btn-danger me-2">
                                        <i class="fas fa-print"></i> Cetak PDF
                                    </a>
                                    <a href="{{ url('kelas/' . $kelas->id_kelas . '/santri') }}"
                                        class="btn btn-outline-secondary"> Kembali </a>
                                </div>
                            </div>
                            <div class="row mt-4">
                                <!-- Foto Santri -->
                                <div class="col-md-4 d-flex align-items-start justify-content-center">
                                    <div style="max-width: 100%; max-height: 350px; overflow: hidden;">
                                        <img src="{{ $santri->foto_santri ? asset($santri->foto_santri) : asset('assets/image/default-user.png') }}"
                                            alt="Foto Santri" class="img-fluid border rounded w-100 h-100"
                                            style="object-fit: cover;">
                                    </div>
                                </div>

                                <!-- Data Santri -->
                                <div class="col-md-8">
                                    <table class="table table-striped">
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
                                            <td>
                                                {{ match ($santri->jenis_kelamin) {
                                                    1 => 'Laki-laki',
                                                    2 => 'Perempuan',
                                                    default => 'Tidak diketahui',
                                                } }}
                                            </td>
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
                            </div>


                            @php
                                $ayah = $santri->keluarga->firstWhere('hubungan', 1);
                                $ibu = $santri->keluarga->firstWhere('hubungan', 2);
                                $wali = $santri->keluarga->firstWhere('hubungan', 3);
                            @endphp

                            <!-- Tabel Ayah -->
                            <table class="table table-striped">
                                <div class="mt-4 d-flex justify-content-between align-items-center">
                                    <h4>Data Ayah:</h4>
                                </div>

                                <tr>
                                    <th width="40%">Nama Ayah</th>
                                    <td>{{ $ayah->nama ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>{{ $ayah->status ?? '' }}</td>
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
                            </table>
                            <!-- Tabel Ibu -->
                            <table class="table table-striped">
                                <div class="mt-4 d-flex justify-content-between align-items-center">
                                    <h4>Data Ibu:</h4>
                                </div>

                                <tr>
                                    <th width="40%">Nama Ibu</th>
                                    <td>{{ $ibu->nama ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>{{ $ibu->status ?? '' }}</td>
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

                            <table class="table table-striped">
                                <tr>
                                    <th width="40%">Nama Wali</th>
                                    <td>{{ $wali->nama ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>{{ $wali->status ?? '' }}</td>
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

                            <!-- Tombol Kembali di Bawah
                                <div class="mt-4">
                                    <a href="{{ route('santri.index') }}" class="btn btn-outline-secondary"> Kembali
                                    </a>
                                </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Tabel Detail Nilai Santri -->
            <div class="mt-5">
                <h4>Detail Nilai Santri</h4>
                <div class="row">
                    <!-- Tabel Hafalan -->
                    <div class="col-md-6">
                        <h5>Hafalan</h5>
                        <table class="table table-bordered">
                            <thead class="bg-navy disabled">
                                <tr>
                                    <th>Surat</th>
                                    <th>Ayat</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hafalan as $item)
                                    <tr>
                                        <td>{{ $item['surat'] }}</td>
                                        <td>{{ $item['ayat'] }}</td>
                                        <td>{{ $item['nilai'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data hafalan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Muroja'ah -->
                    <div class="col-md-6">
                        <h5>Muroja'ah</h5>
                        <table class="table table-bordered">
                            <thead class="bg-navy disabled">
                                <tr>
                                    <th>Surat</th>
                                    <th>Ayat</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($murojaah as $item)
                                    <tr>
                                        <td>{{ $item['surat'] }}</td>
                                        <td>{{ $item['ayat'] ?? '-' }}</td>
                                        <td>{{ $item['nilai'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data muroja'ah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
