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

                                    <td>
                                        {{ [1 => 'Hidup', 2 => 'Meninggal'][$ayah?->status] ?? '-' }}
                                    </td>
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
                                    <td>
                                        {{ [1 => 'Hidup', 2 => 'Meninggal'][$ibu?->status] ?? '-' }}
                                    </td>
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
                                    <td>
                                        {{ [1 => 'Hidup', 2 => 'Meninggal'][$wali?->status] ?? '-' }}
                                    </td>

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
            <div class="row mt-5">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-center mb-4">Rapor Nilai Santri</h4>

                            <!-- Tabel Hafalan -->
                            <h5 class="mb-3">Nilai Hafalan</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Surat</th>
                                        <th>Ayat</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalHafalan = 0;
                                        $countHafalan = 0;
                                    @endphp
                                    @forelse ($hafalan as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item['surat'] }}</td>
                                            <td>{{ $item['ayat'] }}</td>
                                            <td class="text-center">{{ $item['nilai'] ?? '-' }}</td>
                                        </tr>
                                        @if (is_numeric($item['nilai']))
                                            @php
                                                $totalHafalan += $item['nilai'];
                                                $countHafalan++;
                                            @endphp
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada data hafalan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($countHafalan > 0)
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Rata-rata</td>
                                            <td class="text-center fw-bold">
                                                {{ number_format($totalHafalan / $countHafalan, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>

                            <!-- Tabel Muroja'ah -->
                            <h5 class="mt-5 mb-3">Nilai Muroja'ah</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th>Surat</th>
                                        <th>Ayat</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalMurojaah = 0;
                                        $countMurojaah = 0;
                                    @endphp
                                    @forelse ($murojaah as $index => $item)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $item['surat'] }}</td>
                                            <td>{{ $item['ayat'] ?? '-' }}</td>
                                            <td class="text-center">{{ $item['nilai'] ?? '-' }}</td>
                                        </tr>
                                        @if (is_numeric($item['nilai']))
                                            @php
                                                $totalMurojaah += $item['nilai'];
                                                $countMurojaah++;
                                            @endphp
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">Tidak ada data muroja'ah.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if ($countMurojaah > 0)
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Rata-rata</td>
                                            <td class="text-center fw-bold">
                                                {{ number_format($totalMurojaah / $countMurojaah, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </section>
@endsection
