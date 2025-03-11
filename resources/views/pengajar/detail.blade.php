@extends('admin_template')

@section('title page', 'Detail Pengajar')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mt-4">
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Nama</th>
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
                                            <td>{{ $pengajar->jenis_kelamin == 1 ? 'Laki-laki' : 'Perempuan' }}</td>
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
                                <div class="col-md-4 text-center">
                                    <img src="{{ $pengajar->foto_pengajar ? asset($pengajar->foto_pengajar) : asset('assets/image/default.png') }}" alt="Foto Pengajar" class="img-fluid rounded" width="150">
                                </div>
                            </div>
                            <div class="mt-4">
                                <a href="{{ url('pengajar') }}" class="btn btn-secondary">Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
