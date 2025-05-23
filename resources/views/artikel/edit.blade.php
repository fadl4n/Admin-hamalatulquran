@extends('admin_template')

@section('title page')
    Edit Artikel
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('artikel.update', $artikels->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT') {{-- Diperlukan untuk update data --}}
                                <div class="form-group">
                                    <label>Judul Artikel</label>
                                    <input type="text" name="judul" class="form-control" value="{{ $artikels->judul }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="4" required>{{ $artikels->deskripsi }}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="expired_at">Tanggal Expired</label>
                                    <input type="date" name="expired_at" id="expired_at" class="form-control"
                                           value="{{ old('expired_at', $artikels->expired_at ? \Carbon\Carbon::parse($artikels->expired_at)->format('Y-m-d') : '') }}">
                                </div>

                                <div class="form-group">
                                    <label for="gambar">Gambar</label>
                                    <input type="file" name="gambar" id="gambar" class="form-control @error('gambar') is-invalid @enderror" accept="image/*" onchange="previewImage(event)">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengubah foto.</small>

                                    @error('gambar')
                                        <span class="text-danger">Format foto tidak valid atau ukuran terlalu besar.</span>
                                    @enderror

                                    <!-- Pratinjau Gambar -->
                                    <div class="mt-2">
                                        <img id="preview"
                                             src="{{ $artikels->gambar ? $artikels->gambar : asset('assets/image/default-user.png') }}"
                                             alt="Gambar"
                                             class="img-thumbnail"
                                             style="width: 150px; height: 150px; object-fit: cover;">
                                    </div>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Update</button>
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
            let reader = new FileReader();
            reader.onload = function(){
                let output = document.getElementById('preview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>

@endsection
