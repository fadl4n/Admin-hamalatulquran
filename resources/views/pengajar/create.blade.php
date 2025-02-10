@extends('admin_template')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Pengajar</h3>
            </div>
            <div class="card-body">
                <form action="{{ url('pengajar/store') }}" method="POST" autocomplete="off">
                    @csrf

                    <!-- Input Nama -->
                    <div class="form-group">
                        <label>Nama</label>
                        <input type="text" name="nama" class="form-control" required placeholder="Masukkan nama">
                    </div>

                    <!-- Input NIP -->
                    <div class="form-group">
                        <label>NIP</label>
                        <input type="text" name="nip" class="form-control" required placeholder="Masukkan NIP">
                    </div>

                    <!-- Input Email -->
                    <div class="form-group">
                        <label>Email</label>
                        <!-- Tambahkan autocomplete="new-email" agar tidak terisi otomatis -->
                        <input type="email" name="email" class="form-control" required placeholder="Masukkan email" autocomplete="new-email">
                    </div>

                    <!-- Input No. Telepon -->
                    <div class="form-group">
                        <label>No. Telepon</label>
                        <input type="text" name="no_telp" class="form-control" required placeholder="Masukkan nomor telepon">
                    </div>

                    <!-- Input Jenis Kelamin -->
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="" selected disabled>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>

                    <!-- Input Alamat -->
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" required placeholder="Masukkan alamat"></textarea>
                    </div>

                    <!-- Input Password -->
                    <div class="form-group">
                        <label>Password</label>
                        <!-- Tambahkan autocomplete="new-password" agar tidak terisi otomatis -->
                        <input type="password" name="password" class="form-control" required placeholder="Masukkan password" autocomplete="new-password">
                    </div>

                    <!-- Tombol Simpan dan Batal -->
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="{{ url('pengajar') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
