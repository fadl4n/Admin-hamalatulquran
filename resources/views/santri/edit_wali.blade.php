@extends('admin_template')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Data Wali</h3>
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

                <form action="{{ route('santri.update.wali', $santri->id_santri) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label>Nama Wali</label>
                        <input type="text" name="nama_wali"
                               class="form-control @error('nama_wali') is-invalid @enderror"
                               value="{{ old('nama_wali', $wali->nama ?? '') }}" required>
                        @error('nama_wali')
                            <span class="text-danger">Nama wali wajib diisi.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Pekerjaan</label>
                        <input type="text" name="pekerjaan_wali"
                               class="form-control @error('pekerjaan_wali') is-invalid @enderror"
                               value="{{ old('pekerjaan_wali', $wali->pekerjaan ?? '') }}">
                        @error('pekerjaan_wali')
                            <span class="text-danger">Pekerjaan wali tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Pendidikan</label>
                        <input type="text" name="pendidikan_wali"
                               class="form-control @error('pendidikan_wali') is-invalid @enderror"
                               value="{{ old('pendidikan_wali', $wali->pendidikan ?? '') }}">
                        @error('pendidikan_wali')
                            <span class="text-danger">Pendidikan wali tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp_wali"
                               class="form-control @error('no_telp_wali') is-invalid @enderror"
                               value="{{ old('no_telp_wali', $wali->no_telp ?? '') }}">
                        @error('no_telp_wali')
                            <span class="text-danger">Nomor telepon harus valid.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat_wali"
                                  class="form-control @error('alamat_wali') is-invalid @enderror">{{ old('alamat_wali', $wali->alamat ?? '') }}</textarea>
                        @error('alamat_wali')
                            <span class="text-danger">Alamat tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email_wali"
                               class="form-control @error('email_wali') is-invalid @enderror"
                               value="{{ old('email_wali', $wali->email ?? '') }}">
                        @error('email_wali')
                            <span class="text-danger">Format email tidak valid.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tempat_lahir_wali"
                               class="form-control @error('tempat_lahir_wali') is-invalid @enderror"
                               value="{{ old('tempat_lahir_wali', $wali->tempat_lahir ?? '') }}">
                        @error('tempat_lahir_wali')
                            <span class="text-danger">Tempat lahir tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir_wali"
                               class="form-control @error('tgl_lahir_wali') is-invalid @enderror"
                               value="{{ old('tgl_lahir_wali', $wali->tgl_lahir ?? '') }}">
                        @error('tgl_lahir_wali')
                            <span class="text-danger">Tanggal lahir tidak boleh kosong.</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ url('santri/show/'.$santri->id_santri) }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
