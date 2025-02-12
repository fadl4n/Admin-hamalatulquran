@extends('admin_template')

@section('title page')
    Edit Artikel
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('artikel.update', $artikels->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT') {{-- Diperlukan untuk update data --}}
                                <div class="form-group">
                                    <label>Judul Artikel</label>
                                    <input type="text" name="judul" class="form-control" value="{{ $artikels->judul }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="4" required>{{ $artikels->deskripsi }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label>Gambar</label>
                                    <input type="file" name="gambar" class="form-control">
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$artikels->gambar) }}" width="150" class="img-thumbnail">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Expired</label>
                                    <input type="date" name="expired_at" class="form-control" value="{{ $artikels->expired_at }}">
                                </div>
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('artikel.index') }}" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
