@extends('admin_template')

@section('title page')
    Edit Data Orang Tua
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Data Orang Tua</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('santri.update.orangtua', $santri->id_santri) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <h4>Ayah</h4>
                    <div class="form-group">
                        <label>Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="form-control @error('nama_ayah') is-invalid @enderror"
                               value="{{ old('nama_ayah', $ayah->nama ?? '') }}" required>
                        @error('nama_ayah')
                            <span class="text-danger">Nama ayah wajib diisi.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan_ayah" class="form-control @error('pekerjaan_ayah') is-invalid @enderror"
                               value="{{ old('pekerjaan_ayah', $ayah->pekerjaan ?? '') }}">
                        @error('pekerjaan_ayah')
                            <span class="text-danger">Pekerjaan ayah tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Pendidikan</label>
                        <input type="text" name="pendidikan_ayah" class="form-control @error('pendidikan_ayah') is-invalid @enderror"
                               value="{{ old('pendidikan_ayah', $ayah->pendidikan ?? '') }}">
                        @error('pendidikan_ayah')
                            <span class="text-danger">Pendidikan ayah tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp_ayah" class="form-control @error('no_telp_ayah') is-invalid @enderror"
                               value="{{ old('no_telp_ayah', $ayah->no_telp ?? '') }}">
                        @error('no_telp_ayah')
                            <span class="text-danger">Nomor telepon harus valid.</span>
                        @enderror
                    </div>

                    <h4>Ibu</h4>
                    <div class="form-group">
                        <label>Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control @error('nama_ibu') is-invalid @enderror"
                               value="{{ old('nama_ibu', $ibu->nama ?? '') }}" required>
                        @error('nama_ibu')
                            <span class="text-danger">Nama ibu wajib diisi.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan_ibu" class="form-control @error('pekerjaan_ibu') is-invalid @enderror"
                               value="{{ old('pekerjaan_ibu', $ibu->pekerjaan ?? '') }}">
                        @error('pekerjaan_ibu')
                            <span class="text-danger">Pekerjaan ibu tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Pendidikan</label>
                        <input type="text" name="pendidikan_ibu" class="form-control @error('pendidikan_ibu') is-invalid @enderror"
                               value="{{ old('pendidikan_ibu', $ibu->pendidikan ?? '') }}">
                        @error('pendidikan_ibu')
                            <span class="text-danger">Pendidikan ibu tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp_ibu" class="form-control @error('no_telp_ibu') is-invalid @enderror"
                               value="{{ old('no_telp_ibu', $ibu->no_telp ?? '') }}">
                        @error('no_telp_ibu')
                            <span class="text-danger">Nomor telepon harus valid.</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ url('santri/'.$santri->id_santri) }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
