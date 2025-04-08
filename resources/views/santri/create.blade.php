@extends('admin_template')

@section('title page')
    Tambah Santri
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary">
                        <div class="card-body">
                            <!-- Menampilkan notifikasi jika ada error -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ url('santri/store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                           value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <span class="text-danger">Nama wajib diisi.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>NISN</label>
                                    <input type="number" name="nisn" class="form-control @error('nisn') is-invalid @enderror"
                                           value="{{ old('nisn') }}" required>
                                    @error('nisn')
                                        <span class="text-danger">NISN wajib diisi dan harus unik.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                           value="{{ old('tempat_lahir') }}" required>
                                    @error('tempat_lahir')
                                        <span class="text-danger">Tempat lahir wajib diisi.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror"
                                           value="{{ old('tgl_lahir') }}" required>
                                    @error('tgl_lahir')
                                        <span class="text-danger">Tanggal lahir wajib diisi.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}" required>
                                    @error('email')
                                        <span class="text-danger">Email wajib diisi dan harus unik.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control @error('alamat') is-invalid @enderror"
                                           value="{{ old('alamat') }}">
                                    @error('alamat')
                                        <span class="text-danger">Alamat tidak boleh kosong.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Angkatan</label>
                                    <input type="number" name="angkatan" class="form-control @error('angkatan') is-invalid @enderror"
                                           value="{{ old('angkatan') }}">
                                    @error('angkatan')
                                        <span class="text-danger">Angkatan tidak boleh kosong.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="id_kelas" class="form-control @error('id_kelas') is-invalid @enderror">
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id_kelas }}" {{ old('id_kelas') == $k->id_kelas ? 'selected' : '' }}>
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
                                        <option value="1" {{ old('jenis_kelamin') == '1' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="2" {{ old('jenis_kelamin') == '2' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <span class="text-danger">Jenis kelamin wajib dipilih.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">Status wajib dipilih.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                           required>
                                    @error('password')
                                        <span class="text-danger">Password minimal 6 karakter.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="foto_santri">Foto Santri</label>
                                    <input type="file" name="foto_santri" id="foto_santri"
                                        class="form-control @error('foto_santri') is-invalid @enderror"
                                        accept="image/png, image/jpeg"
                                        onchange="previewImage(event)">

                                    <small class="text-muted">Kosongkan jika tidak ingin mengunggah foto.</small>

                                    @error('foto_santri')
                                        <span class="text-danger">Format gambar tidak valid atau ukuran terlalu besar.</span>
                                    @enderror

                                    <!-- Pratinjau Gambar -->
                                    <div class="mt-2">
                                        <img id="preview"
                                            src="{{ asset('assets/image/default-user.png') }}"
                                            alt="Foto Santri"
                                            class="img-thumbnail" width="150">
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="{{ url('santri') }}" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
