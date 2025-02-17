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
                        <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah', $ayah->nama ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan_ayah" class="form-control" value="{{ old('pekerjaan_ayah', $ayah->pekerjaan ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Pendidikan</label>
                        <input type="text" name="pendidikan_ayah" class="form-control" value="{{ old('pendidikan_ayah', $ayah->pendidikan ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp_ayah" class="form-control" value="{{ old('no_telp_ayah', $ayah->no_telp ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat_ayah" class="form-control">{{ old('alamat_ayah', $ayah->alamat ?? '') }}</textarea>
                    </div>
                    <!-- Email Ayah -->
<div class="form-group">
    <label>Email Ayah</label>
    <input type="email" name="email_ayah"
           class="form-control @error('email_ayah') is-invalid @enderror"
           value="{{ old('email_ayah', $ayah->email ?? '') }}">
    @error('email_ayah')
        <span class="text-danger">Format email tidak valid.</span>
    @enderror
</div>
                    <div class="form-group">
                        <label>Tempat lahir</label>
                        <input type="text" name="tempat_lahir_ayah" class="form-control" value="{{ old('tempat_lahir_ayah', $ayah->tempat_lahir ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir_ayah" class="form-control mt-2" value="{{ old('tgl_lahir_ayah', $ayah->tgl_lahir ?? '') }}">
                    </div>

                    <h4>Ibu</h4>
                    <div class="form-group">
                        <label>Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu', $ibu->nama ?? '') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan_ibu" class="form-control" value="{{ old('pekerjaan_ibu', $ibu->pekerjaan ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Pendidikan</label>
                        <input type="text" name="pendidikan_ibu" class="form-control" value="{{ old('pendidikan_ibu', $ibu->pendidikan ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp_ibu" class="form-control" value="{{ old('no_telp_ibu', $ibu->no_telp ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat_ibu" class="form-control">{{ old('alamat_ibu', $ibu->alamat ?? '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Email Ibu</label>
                        <input type="email" name="email_ibu"
                               class="form-control @error('email_ibu') is-invalid @enderror"
                               value="{{ old('email_ibu', $ibu->email ?? '') }}">
                        @error('email_ibu')
                            <span class="text-danger">Format email tidak valid.</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label>Tempat lahir</label>
                        <input type="text" name="tempat_lahir_ibu" class="form-control" value="{{ old('tempat_lahir_ibu', $ibu->tempat_lahir ?? '') }}">
                    </div>
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir_ibu" class="form-control mt-2" value="{{ old('tgl_lahir_ibu', $ibu->tgl_lahir ?? '') }}">
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ url('santri/show/'.$santri->id_santri) }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
