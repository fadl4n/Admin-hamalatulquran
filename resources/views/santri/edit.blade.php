@extends('admin_template')

@section('title page')
    Edit Santri
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('santri.update', $santri->id_santri) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama"
                                       class="form-control @error('nama') is-invalid @enderror"
                                       value="{{ old('nama', $santri->nama) }}" required>
                                @error('nama')
                                    <span class="text-danger">Nama santri wajib diisi.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>NISN</label>
                                <input type="number" name="nisn"
                                       class="form-control @error('nisn') is-invalid @enderror"
                                       value="{{ old('nisn', $santri->nisn) }}" required>
                                @error('nisn')
                                    <span class="text-danger">NISN wajib diisi dan harus berupa angka.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Tempat Lahir</label>
                                <input type="text" name="tempat_lahir"
                                       class="form-control @error('tempat_lahir') is-invalid @enderror"
                                       value="{{ old('tempat_lahir', $santri->tempat_lahir) }}" required>
                                @error('tempat_lahir')
                                    <span class="text-danger">Tempat lahir wajib diisi.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="tgl_lahir"
                                       class="form-control @error('tgl_lahir') is-invalid @enderror"
                                       value="{{ old('tgl_lahir', $santri->tgl_lahir) }}" required>
                                @error('tgl_lahir')
                                    <span class="text-danger">Tanggal lahir wajib diisi.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email', $santri->email) }}">
                                @error('email')
                                    <span class="text-danger">Format email tidak valid.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <input type="text" name="alamat"
                                       class="form-control @error('alamat') is-invalid @enderror"
                                       value="{{ old('alamat', $santri->alamat) }}">
                                @error('alamat')
                                    <span class="text-danger">Alamat tidak boleh kosong.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Angkatan</label>
                                <input type="text" name="angkatan"
                                       class="form-control @error('angkatan') is-invalid @enderror"
                                       value="{{ old('angkatan', $santri->angkatan) }}">
                                @error('angkatan')
                                    <span class="text-danger">Angkatan wajib diisi.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Kelas</label>
                                <select name="id_kelas" class="form-control @error('id_kelas') is-invalid @enderror">
                                    <option value="">Pilih Kelas</option>
                                    @foreach($kelas as $k)
                                        <option value="{{ $k->id_kelas }}" {{ old('id_kelas', $santri->id_kelas) == $k->id_kelas ? 'selected' : '' }}>
                                            {{ $k->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('id_kelas')
                                    <span class="text-danger">Silakan pilih kelas.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                    <option value="1" {{ old('jenis_kelamin', $santri->jenis_kelamin) == 1 ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="2" {{ old('jenis_kelamin', $santri->jenis_kelamin) == 2 ? 'selected' : '' }}>Perempuan</option>
                                </select>
                                @error('jenis_kelamin')
                                    <span class="text-danger">Jenis kelamin wajib dipilih.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="1" {{ old('status', $santri->status) == 1 ? 'selected' : '' }}>Aktif</option>
                                    <option value="0" {{ old('status', $santri->status) == 0 ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">Status wajib dipilih.</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>Foto Santri</label>
                                <input type="file" name="foto_santri" class="form-control @error('foto_santri') is-invalid @enderror">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>
                                @error('foto_santri')
                                    <span class="text-danger">Format foto tidak valid atau ukuran terlalu besar.</span>
                                @enderror

                                @if ($santri->foto_santri)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $santri->foto_santri) }}" alt="Foto Santri" class="img-thumbnail" width="150">
                                    </div>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('santri.show', $santri->id_santri) }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
