@extends('admin_template')

@section('title page')
    Tambah Kelas
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ url('kelas/store') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Nama Kelas</label>
                                    <input type="text" name="nama_kelas" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ url('kelas') }}" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
