@extends('admin_template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Pengajar</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('pengajar/store') }}" method="POST" autocomplete="off" enctype="multipart/form-data">
                        @csrf

<<<<<<< HEAD
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Kolom Kiri -->
                                <div class="form-group">
                                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                        value="{{ old('nama') }}" required placeholder="Masukkan nama">
                                    @error('nama')
                                        <span class="text-danger">Nama wajib diisi.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>NIP</label>
                                    <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                        value="{{ old('nip') }}" required placeholder="Masukkan NIP">
                                    @error('nip')
                                        <span class="text-danger">NIP sudah digunakan atau tidak valid.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email') }}" required placeholder="Masukkan email" autocomplete="new-email">
                                    @error('email')
                                        <span class="text-danger">Email sudah digunakan atau tidak valid.</span>
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
                                    <label>No. Telepon</label>
                                    <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                                        value="{{ old('no_telp') }}" required placeholder="Masukkan nomor telepon">
                                    @error('no_telp')
                                        <span class="text-danger">Nomor telepon wajib diisi.</span>
                                    @enderror
                                </div>
=======
                        <!-- Input Nama -->
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                    <label>Alamat</label>
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" required placeholder="Masukkan alamat">{{ old('alamat') }}</textarea>
                                    @error('alamat')
                                        <span class="text-danger">Alamat wajib diisi.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                        <option value="" selected disabled>Pilih Jenis Kelamin</option>
                                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <span class="text-danger">Jenis kelamin wajib dipilih.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                        required placeholder="Masukkan password" autocomplete="new-password">
                                    @error('password')
                                        <span class="text-danger">Password minimal 6 karakter.</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="foto_pengajar">Foto Pengajar</label>
                                    <input type="file" name="foto_pengajar" id="foto_pengajar"
                                        class="form-control-file @error('foto_pengajar') is-invalid @enderror"
                                        accept="image/png, image/jpeg"
                                        onchange="previewImage(event)">
                                    <div class="mt-2">
                                        <img id="preview" src="{{ asset('assets/image/default-user.png') }}"
                                            alt="Preview Gambar" class="img-thumbnail" width="150">
                                    </div>
                                    @error('foto_pengajar')
                                        <span class="text-danger">Format gambar tidak valid atau ukuran terlalu besar.</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Simpan dan Batal (Rata Kanan dengan jarak) -->
                        <div class="mt-4 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" style="margin-right: 10px;">Simpan</button>
                            <a href="{{ url('pengajar') }}" class="btn btn-secondary">Batal</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- JavaScript untuk Pratinjau Gambar -->
    <script>
        function previewImage(event) {
            var reader = new FileReader();
<<<<<<< HEAD
            reader.onload = function(){
=======
            reader.onload = function() {
>>>>>>> 935c067f274c92b63a81fcba184d7d4a9bc72915
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
