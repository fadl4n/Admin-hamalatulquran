@extends('admin_template')

@section('title page', 'Tambah Keluarga')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('keluarga.store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Pekerjaan</label>
                                    <input type="text" name="pekerjaan" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Pendidikan</label>
                                    <input type="text" name="pendidikan" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" name="no_telp" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Santri</label>
                                    <select name="id_santri" class="form-control">
                                        <option value="">- Pilih Santri -</option>
                                        @foreach($santris as $santri)
                                        <option value="{{ $santri->id_santri }}">{{ $santri->nama }} | {{ $santri->nisn }}</option>

                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('keluarga.index') }}" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
