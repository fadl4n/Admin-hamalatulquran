@extends('admin_template')

@section('title page')
    Edit Surat
@endsection

@section('content')
    <div class="card col-md-10 offset-md-1">
        <div class="card-body">
            <form action="{{ route('surat.update', $surat->id_surat) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Nama Surat</label>
                    <input type="text" name="nama_surat" class="form-control" value="{{ $surat->nama_surat }}" required>
                </div>
                <div class="form-group">
                    <label>Jumlah Ayat</label>
                    <input type="number" name="jumlah_ayat" class="form-control" value="{{ $surat->jumlah_ayat }}" required min="1">
                </div>
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" class="form-control">{{ $surat->deskripsi }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
@endsection
