@extends('admin_template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Pengajar</h3>
                </div>
                <div class="card-body">
                    <!-- Menampilkan notifikasi jika ada pesan error -->
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('pengajar.update', $pengajar->id_pengajar) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Input Nama -->
                        <div class="form-group">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                                   value="{{ old('nama', $pengajar->nama) }}" required>
                            @error('nama')
                                <span class="text-danger">Nama wajib diisi.</span>
                            @enderror
                        </div>

                        <!-- Input NIP -->
                        <div class="form-group">
                            <label>NIP</label>
                            <input type="text" name="nip" class="form-control @error('nip') is-invalid @enderror"
                                   value="{{ old('nip', $pengajar->nip) }}" required>
                            @error('nip')
                                <span class="text-danger">NIP sudah digunakan atau tidak valid.</span>
                            @enderror
                        </div>

                        <!-- Input Tempat Lahir -->
                        <div class="form-group">
                            <label>Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                   value="{{ old('tempat_lahir', $pengajar->tempat_lahir) }}" required>
                            @error('tempat_lahir')
                                <span class="text-danger">Tempat lahir wajib diisi.</span>
                            @enderror
                        </div>

                        <!-- Input Tanggal Lahir -->
                        <div class="form-group">
                            <label>Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror"
                                   value="{{ old('tgl_lahir', $pengajar->tgl_lahir) }}" required>
                            @error('tgl_lahir')
                                <span class="text-danger">Tanggal lahir wajib diisi.</span>
                            @enderror
                        </div>

                        <!-- Input Email -->
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $pengajar->email) }}" required>
                            @error('email')
                                <span class="text-danger">Email sudah digunakan atau tidak valid.</span>
                            @enderror
                        </div>

                        <!-- Input No. Telepon -->
                        <div class="form-group">
                            <label>No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control @error('no_telp') is-invalid @enderror"
                                   value="{{ old('no_telp', $pengajar->no_telp) }}" required>
                            @error('no_telp')
                                <span class="text-danger">Nomor telepon wajib diisi.</span>
                            @enderror
                        </div>

                        <!-- Input Jenis Kelamin -->
                        <div class="form-group">
                            <label>Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" required>
                                <option value="Laki-laki" {{ old('jenis_kelamin', $pengajar->jenis_kelamin) == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ old('jenis_kelamin', $pengajar->jenis_kelamin) == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <span class="text-danger">Jenis kelamin wajib dipilih.</span>
                            @enderror
                        </div>

                        <!-- Input Alamat -->
                        <div class="form-group">
                            <label>Alamat</label>
                            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" required>{{ old('alamat', $pengajar->alamat) }}</textarea>
                            @error('alamat')
                                <span class="text-danger">Alamat wajib diisi.</span>
                            @enderror
                        </div>

                        <!-- Input Password -->
                        <div class="form-group">
                            <label>Password (Kosongkan jika tidak ingin mengubah)</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   placeholder="******">
                            @error('password')
                                <span class="text-danger">Password minimal 6 karakter.</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="foto_pengajar">Foto Pengajar</label>
                            <input type="file" name="foto_pengajar" id="foto_pengajar"
                                class="form-control @error('foto_pengajar') is-invalid @enderror"
                                accept="image/png, image/jpeg"
                                onchange="previewImage(event)">

                            <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>

                            @error('foto_pengajar')
                                <span class="text-danger">Format gambar tidak valid atau ukuran terlalu besar.</span>
                            @enderror

                            <!-- Pratinjau Gambar -->
                            <div class="mt-2">
                                <img id="preview"
                                    src="{{ $pengajar->foto_pengajar ? asset('uploadedFile/image/pengajar/' . basename($pengajar->foto_pengajar)) : asset('assets/image/default-user.png') }}"
                                    alt="Foto Pengajar"
                                    class="img-thumbnail" width="150">
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ url('pengajar') }}" class="btn btn-secondary">Batal</a>
                    </form>
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
