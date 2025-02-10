@extends('admin_template')

@section('title page')
    Edit Kelas
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('kelas.update', $kelas->id_kelas) }}" method="POST">
                                @csrf
                                @method('PUT') {{-- Ini harus ada agar sesuai dengan route di web.php --}}
                                <div class="form-group">
                                    <label>Nama Kelas</label>
                                    <input type="text" name="nama_kelas" class="form-control" value="{{ $kelas->nama_kelas }}" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ url('kelas') }}" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
