@extends('admin_template')

@section('title page')
    Tambah Artikel
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('artikel.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label>Judul Artikel</label>
                                    <input type="text" name="judul" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                                </div>

                                <div class="form-group">
                                    <label>Tanggal Expired</label>
                                    <input type="date" name="expired_at" class="form-control"
                                        value="{{ old('expired_at') }}">

                                    @error('expired_at')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="gambar">Gambar Artikel</label>
                                    <input type="file" name="gambar" id="gambar"
                                        class="form-control @error('gambar') is-invalid @enderror"
                                        accept="image/png, image/jpeg" onchange="previewImage(event)">

                                    <small class="text-muted">Kosongkan jika tidak ingin mengunggah foto.</small>

                                    @error('gambar')
                                        <span class="text-danger">Format gambar tidak valid atau ukuran terlalu besar.</span>
                                    @enderror

                                    <!-- Pratinjau Gambar -->
                                    <!-- Pratinjau Gambar -->
                                    <div class="mt-2">
                                        <img id="preview" src="{{ asset('assets/image/default-user.png') }}"
                                            alt="Gambar Artikel" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;"
                                            onerror="this.onerror=null;this.src='{{ asset('assets/image/default.png') }}'">
                                    </div>

                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                    <a href="{{ route('artikel.index') }}" class="btn btn-secondary ml-2">Batal</a>
                                </div>
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
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
