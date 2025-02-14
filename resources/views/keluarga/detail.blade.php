@extends('admin_template')

@section('title page')
    Detail Keluarga
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
                            <h3 class="card-title">Data Keluarga :</h3>
                            <a href="{{ url('keluarga/edit/' . $keluarga->id) }}" class="btn btn-success btn-sm ml-auto">Ubah
                                Bagian Ini</a>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th width="40%">Nama</th>
                                            <td>{{ $keluarga->nama }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pekerjaan</th>
                                            <td>{{ $keluarga->pekerjaan }}</td>
                                        </tr>
                                        <tr>
                                            <th>Pendidikan</th>
                                            <td>{{ $keluarga->pendidikan }}</td>
                                        </tr>
                                        <tr>
                                            <th>No. Telepon</th>
                                            <td>{{ $keluarga->no_telp }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $keluarga->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Tempat, Tanggal Lahir</th>
                                            <td>{{ $keluarga->tempat_lahir }}, {{ $keluarga->tgl_lahir }}</td>
                                        </tr>
                                        <tr>
                                            <th>Alamat</th>
                                            <td>{{ $keluarga->alamat }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Santri</th>
                                            <td>{{ $keluarga->santri ? $keluarga->santri->nama : 'Tidak Terdaftar' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <a href="{{ url('keluarga') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
