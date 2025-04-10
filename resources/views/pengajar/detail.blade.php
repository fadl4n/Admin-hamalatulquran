@extends('admin_template')

@section('title page', 'Detail Pengajar')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Informasi Pengajar</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <!-- Gambar Pengajar -->
                                <div class="col-md-4 text-center">
                                    <img src="{{ $pengajar->foto_pengajar ? $pengajar->foto_pengajar : asset('assets/images/default.png') }}"
                                        alt="Foto Pengajar" class="rounded-circle border" width="150" height="150"
                                        style="object-fit: cover;">
                                </div>
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Nama</th>
                                            <td>{{ $pengajar->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>NIP</th>
                                            <td>{{ $pengajar->nip }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $pengajar->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $pengajar->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. Telepon</th>
                                            <td>{{ $pengajar->no_telp }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis Kelamin</th>
                                            <td>
                                                {{ match ($pengajar->jenis_kelamin) {
                                                    1 => 'Laki-laki',
                                                    2 => 'Perempuan',
                                                    default => 'Tidak diketahui',
                                                } }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tempat Lahir</th>
                                            <td>{{ $pengajar->tempat_lahir }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Lahir</th>
                                            <td>{{ $pengajar->tgl_lahir }}</td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div class="text-start mt-3">
                                <a href="{{ url('pengajar') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
