@extends('admin_template')

@section('title page')
    Detail Pengajar
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-body">
                            <div class="mt-4 d-flex justify-content-between align-items-center">
                                <h4>Data Pengajar</h4>
                                <a href="{{ url('pengajar') }}" class="btn btn-outline-secondary">Kembali</a>
                            </div>

                            <div class="row mt-4">
                                <!-- Foto Pengajar -->
                                <div class="col-md-4 d-flex align-items-start justify-content-center">
                                    <div style="max-width: 100%; max-height: 350px; overflow: hidden;">
                                        <img src="{{ $pengajar->foto_pengajar ? asset($pengajar->foto_pengajar) : asset('assets/image/default-user.png') }}"
                                            alt="Foto Pengajar" class="img-fluid border rounded"
                                            style="object-fit: cover; width: 100%; height: auto; max-height: 350px;">
                                    </div>

                                </div>

                                <!-- Data Pengajar -->
                                <div class="col-md-8">
                                    <table class="table table-striped">
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
                                            <th>Tempat, Tanggal Lahir</th>
                                            <td>{{ $pengajar->tempat_lahir }}, {{ $pengajar->tgl_lahir }}</td>
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
                                            <th>Alamat</th>
                                            <td>{{ $pengajar->alamat }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
    </section>
@endsection
