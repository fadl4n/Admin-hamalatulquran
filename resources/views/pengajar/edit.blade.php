@extends('admin_template')

@section('title page')
    Edit Pengajar
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-body">
                            @if (session('error'))
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form action="{{ route('pengajar.update', $pengajar->id_pengajar) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Kolom Foto -->
                                    <div class="col-md-4 text-center">
                                        <div class="form-group">
                                            <label for="foto_pengajar">Foto Pengajar</label>
                                            <div class="mt-2">
                                                <img id="preview"
                                                    src="{{ !empty($pengajar->foto_pengajar) && file_exists(public_path('uploadedFile/image/pengajar/' . basename($pengajar->foto_pengajar))) ? asset('uploadedFile/image/pengajar/' . basename($pengajar->foto_pengajar)) : asset('assets/image/default-user.png') }}"
                                                    alt="Foto Pengajar" class="img-thumbnail"
                                                    style="max-height: 350px; width: 100%; height: auto; object-fit: cover;">

                                            </div>

                                            <!-- Tombol Custom Upload -->
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-primary"
                                                    onclick="document.getElementById('foto_pengajar').click();">
                                                    Choose Image
                                                </button>
                                                <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengubah
                                                    foto.</small>
                                            </div>

                                            <input type="file" name="foto_pengajar" id="foto_pengajar"
                                                class="d-none @error('foto_pengajar') is-invalid @enderror" accept="image/*"
                                                onchange="previewImage(event)">

                                            @error('foto_pengajar')
                                                <span class="text-danger d-block">Format gambar tidak valid atau ukuran terlalu
                                                    besar.</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Kolom Form Data -->
                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Nama</label>
                                            <input type="text" name="nama"
                                                class="form-control @error('nama') is-invalid @enderror"
                                                value="{{ old('nama', $pengajar->nama) }}" required>
                                            @error('nama')
                                                <span class="text-danger">Nama wajib diisi.</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>NIP</label>
                                            <input type="text" name="nip"
                                                class="form-control @error('nip') is-invalid @enderror"
                                                value="{{ old('nip', $pengajar->nip) }}" required>
                                            @error('nip')
                                                <span class="text-danger">NIP sudah digunakan atau tidak valid.</span>
                                            @enderror
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Tempat Lahir</label>
                                                <input type="text" name="tempat_lahir"
                                                    class="form-control @error('tempat_lahir') is-invalid @enderror"
                                                    value="{{ old('tempat_lahir', $pengajar->tempat_lahir) }}" required>
                                                @error('tempat_lahir')
                                                    <span class="text-danger">Tempat lahir wajib diisi.</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Tanggal Lahir</label>
                                                <input type="date" name="tgl_lahir"
                                                    class="form-control @error('tgl_lahir') is-invalid @enderror"
                                                    value="{{ old('tgl_lahir', $pengajar->tgl_lahir) }}" required>
                                                @error('tgl_lahir')
                                                    <span class="text-danger">Tanggal lahir wajib diisi.</span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $pengajar->email) }}" required>
                                            @error('email')
                                                <span class="text-danger">Email sudah digunakan atau tidak valid.</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>No. Telepon</label>
                                            <input type="text" name="no_telp"
                                                class="form-control @error('no_telp') is-invalid @enderror"
                                                value="{{ old('no_telp', $pengajar->no_telp) }}" required>
                                            @error('no_telp')
                                                <span class="text-danger">Nomor telepon wajib diisi.</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Jenis Kelamin</label>
                                            <select name="jenis_kelamin"
                                                class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                                <option value="">Pilih Jenis Kelamin</option>
                                                <option value="1"
                                                    {{ old('jenis_kelamin', $pengajar->jenis_kelamin) == 1 ? 'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="2"
                                                    {{ old('jenis_kelamin', $pengajar->jenis_kelamin) == 2 ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                            @error('jenis_kelamin')
                                                <span class="text-danger">Jenis kelamin wajib dipilih.</span>
                                            @enderror
                                        </div>


                                        <div class="form-group">
                                            <label>Alamat</label>
                                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $pengajar->alamat) }}</textarea>
                                            @error('alamat')
                                                <span class="text-danger">Alamat wajib diisi.</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label>Password (Kosongkan jika tidak ingin mengubah)</label>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" placeholder="">
                                            @error('password')
                                                <span class="text-danger">Password minimal 6 karakter.</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Tombol -->
                                <div class="mt-4 text-right">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ url('pengajar') }}" class="btn btn-secondary">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Script Preview Gambar -->
    <script>
        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
