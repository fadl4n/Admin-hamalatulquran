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
                            <form action="{{ route('santri.update', $santri->id_santri) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <!-- Kolom Foto Santri -->
                                    <div class="col-md-4 text-center">
                                        <div class="form-group">
                                            <label for="foto_santri">Foto Santri</label>
                                            <div class="mt-2">
                                                <img id="preview"
                                                    src="{{ !empty($santri->foto_santri) ? asset($santri->foto_santri) : asset('assets/image/default-user.png') }}"
                                                    alt="Foto Santri" class="img-thumbnail"
                                                    style="width: auto; height: auto; max-height: 350px; object-fit: cover;">

                                            </div>

                                            <!-- Custom Button -->
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-primary"
                                                    onclick="document.getElementById('foto_santri').click();">
                                                    Choose Image
                                                </button>
                                                <small class="text-muted d-block mt-1">Kosongkan jika tidak ingin mengubah
                                                    foto.</small>
                                            </div>

                                            <!-- Hidden File Input -->
                                            <input type="file" name="foto_santri" id="foto_santri"
                                                class="d-none @error('foto_santri') is-invalid @enderror" accept="image/*"
                                                onchange="previewImage(event)">

                                            @error('foto_santri')
                                                <span class="text-danger d-block">Format foto tidak valid atau ukuran terlalu
                                                    besar.</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Kolom Form Data Santri -->
                                    <div class="col-md-8">
                                        {{-- Seluruh form input santri pindahkan ke sini --}}
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

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Tempat Lahir</label>
                                                <input type="text" name="tempat_lahir"
                                                    class="form-control @error('tempat_lahir') is-invalid @enderror"
                                                    value="{{ old('tempat_lahir', $santri->tempat_lahir) }}" required>
                                                @error('tempat_lahir')
                                                    <span class="text-danger">Tempat lahir wajib diisi.</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Tanggal Lahir</label>
                                                <input type="date" name="tgl_lahir"
                                                    class="form-control @error('tgl_lahir') is-invalid @enderror"
                                                    value="{{ old('tgl_lahir', $santri->tgl_lahir) }}" required>
                                                @error('tgl_lahir')
                                                    <span class="text-danger">Tanggal lahir wajib diisi.</span>
                                                @enderror
                                            </div>
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

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Angkatan</label>
                                                <input type="text" name="angkatan"
                                                    class="form-control @error('angkatan') is-invalid @enderror"
                                                    value="{{ old('angkatan', $santri->angkatan) }}">
                                                @error('angkatan')
                                                    <span class="text-danger">Angkatan wajib diisi.</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Kelas</label>
                                                <select name="id_kelas"
                                                    class="form-control @error('id_kelas') is-invalid @enderror">
                                                    <option value="">Pilih Kelas</option>
                                                    @foreach ($kelas as $k)
                                                        <option value="{{ $k->id_kelas }}"
                                                            {{ old('id_kelas', $santri->id_kelas) == $k->id_kelas ? 'selected' : '' }}>
                                                            {{ $k->nama_kelas }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_kelas')
                                                    <span class="text-danger">Silakan pilih kelas.</span>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label>Jenis Kelamin</label>
                                                <select name="jenis_kelamin"
                                                    class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                                    <option value="1"
                                                        {{ old('jenis_kelamin', $santri->jenis_kelamin) == 1 ? 'selected' : '' }}>
                                                        Laki-laki</option>
                                                    <option value="2"
                                                        {{ old('jenis_kelamin', $santri->jenis_kelamin) == 2 ? 'selected' : '' }}>
                                                        Perempuan</option>
                                                </select>
                                                @error('jenis_kelamin')
                                                    <span class="text-danger">Jenis kelamin wajib dipilih.</span>
                                                @enderror
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label>Status</label>
                                                <select name="status"
                                                    class="form-control @error('status') is-invalid @enderror">
                                                    <option value="1"
                                                        {{ old('status', $santri->status) == 1 ? 'selected' : '' }}>Aktif
                                                    </option>
                                                    <option value="0"
                                                        {{ old('status', $santri->status) == 0 ? 'selected' : '' }}>
                                                        Nonaktif</option>
                                                </select>
                                                @error('status')
                                                    <span class="text-danger">Status wajib dipilih.</span>
                                                @enderror
                                            </div>
                                        </div>

                                    </div>
                                </div>


                              <h4>Ayah</h4>
<div class="form-group">
    <label>Nama Ayah</label>
    <input type="text" name="nama_ayah" class="form-control"
        value="{{ old('nama_ayah', $ayah->nama ?? '') }}">
</div>
<div class="form-group">
    <label>Status</label>
    <select name="status_ayah" class="form-control">
        <option value="1"
            {{ old('status_ayah', optional($ayah)->status) == 1 ? 'selected' : '' }}>Hidup
        </option>
        <option value="2"
            {{ old('status_ayah', optional($ayah)->status) == 2 ? 'selected' : '' }}>
            Meninggal</option>
    </select>
</div>
<div class="form-group">
    <label>Pekerjaan</label>
    <input type="text" name="pekerjaan_ayah" class="form-control ayah-input"
        value="{{ old('pekerjaan_ayah', $ayah->pekerjaan ?? '') }}">
</div>
<div class="form-group">
    <label>Pendidikan</label>
    <input type="text" name="pendidikan_ayah" class="form-control ayah-input"
        value="{{ old('pendidikan_ayah', $ayah->pendidikan ?? '') }}">
</div>
<div class="form-group">
    <label>Alamat</label>
    <textarea name="alamat_ayah" class="form-control ayah-input">{{ old('alamat_ayah', $ayah->alamat ?? '') }}</textarea>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Email Ayah</label>
            <input type="email" name="email_ayah"
                class="form-control ayah-input @error('email_ayah') is-invalid @enderror"
                value="{{ old('email_ayah', $ayah->email ?? '') }}">
            @error('email_ayah')
                <span class="text-danger">Format email tidak valid.</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>No. Telepon</label>
            <input type="text" name="no_telp_ayah" class="form-control ayah-input"
                value="{{ old('no_telp_ayah', $ayah->no_telp ?? '') }}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Tempat Lahir</label>
            <input type="text" name="tempat_lahir_ayah" class="form-control"
                value="{{ old('tempat_lahir_ayah', $ayah->tempat_lahir ?? '') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="tgl_lahir_ayah" class="form-control"
                value="{{ old('tgl_lahir_ayah', $ayah->tgl_lahir ?? '') }}">
        </div>
    </div>
</div>


                               <h4>Ibu</h4>
<div class="form-group">
    <label>Nama Ibu</label>
    <input type="text" name="nama_ibu" class="form-control"
        value="{{ old('nama_ibu', $ibu->nama ?? '') }}">
</div>
<div class="form-group">
    <label>Status</label>
    <select name="status_ibu" class="form-control">
        <option value="1"
            {{ old('status_ibu', optional($ibu)->status) == 1 ? 'selected' : '' }}>Hidup
        </option>
        <option value="2"
            {{ old('status_ibu', optional($ibu)->status) == 2 ? 'selected' : '' }}>
            Meninggal</option>
    </select>
</div>
<div class="form-group">
    <label>Pekerjaan</label>
    <input type="text" name="pekerjaan_ibu" class="form-control ibu-input"
        value="{{ old('pekerjaan_ibu', $ibu->pekerjaan ?? '') }}">
</div>
<div class="form-group">
    <label>Pendidikan</label>
    <input type="text" name="pendidikan_ibu" class="form-control ibu-input"
        value="{{ old('pendidikan_ibu', $ibu->pendidikan ?? '') }}">
</div>
<div class="form-group">
    <label>Alamat</label>
    <textarea name="alamat_ibu" class="form-control ibu-input">{{ old('alamat_ibu', $ibu->alamat ?? '') }}</textarea>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Email Ibu</label>
            <input type="email" name="email_ibu"
                class="form-control ibu-input @error('email_ibu') is-invalid @enderror"
                value="{{ old('email_ibu', $ibu->email ?? '') }}">
            @error('email_ibu')
                <span class="text-danger">Format email tidak valid.</span>
            @enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>No. Telepon</label>
            <input type="text" name="no_telp_ibu" class="form-control ibu-input"
                value="{{ old('no_telp_ibu', $ibu->no_telp ?? '') }}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Tempat Lahir</label>
            <input type="text" name="tempat_lahir_ibu" class="form-control"
                value="{{ old('tempat_lahir_ibu', $ibu->tempat_lahir ?? '') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="tgl_lahir_ibu" class="form-control"
                value="{{ old('tgl_lahir_ibu', $ibu->tgl_lahir ?? '') }}">
        </div>
    </div>
</div>



                                <h4>Wali</h4>
<div class="form-group">
    <label>Nama Wali</label>
    <input type="text" name="nama_wali"
        class="form-control @error('nama_wali') is-invalid @enderror"
        value="{{ old('nama_wali', $wali->nama ?? '') }}">
</div>
<div class="form-group">
    <label>Status</label>
    <select name="status_wali" class="form-control">
        <option value="1"
            {{ old('status_wali', optional($wali)->status) == 1 ? 'selected' : '' }}>Hidup
        </option>
        <option value="2"
            {{ old('status_wali', optional($wali)->status) == 2 ? 'selected' : '' }}>
            Meninggal</option>
    </select>
</div>
<div class="form-group">
    <label>Pekerjaan</label>
    <input type="text" name="pekerjaan_wali"
        class="form-control wali-input @error('pekerjaan_wali') is-invalid @enderror"
        value="{{ old('pekerjaan_wali', $wali->pekerjaan ?? '') }}">
</div>
<div class="form-group">
    <label>Pendidikan</label>
    <input type="text" name="pendidikan_wali"
        class="form-control wali-input @error('pendidikan_wali') is-invalid @enderror"
        value="{{ old('pendidikan_wali', $wali->pendidikan ?? '') }}">
</div>
<div class="form-group">
    <label>Alamat</label>
    <textarea name="alamat_wali" class="form-control wali-input @error('alamat_wali') is-invalid @enderror">{{ old('alamat_wali', $wali->alamat ?? '') }}</textarea>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email_wali"
                class="form-control wali-input @error('email_wali') is-invalid @enderror"
                value="{{ old('email_wali', $wali->email ?? '') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>No. Telepon</label>
            <input type="text" name="no_telp_wali"
                class="form-control wali-input @error('no_telp_wali') is-invalid @enderror"
                value="{{ old('no_telp_wali', $wali->no_telp ?? '') }}">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label>Tempat Lahir</label>
            <input type="text" name="tempat_lahir_wali"
                class="form-control @error('tempat_lahir_wali') is-invalid @enderror"
                value="{{ old('tempat_lahir_wali', $wali->tempat_lahir ?? '') }}">
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="tgl_lahir_wali"
                class="form-control @error('tgl_lahir_wali') is-invalid @enderror"
                value="{{ old('tgl_lahir_wali', $wali->tgl_lahir ?? '') }}">
        </div>
    </div>
</div>


                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('santri.index') }}" class="btn btn-secondary">Batal</a>
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

    function toggleInputs(statusSelectorName, inputClass) {
        const status = document.querySelector(`select[name="${statusSelectorName}"]`).value;
        const isMeninggal = status == '2'; // 2 = Meninggal
        const inputs = document.querySelectorAll(`.${inputClass}`);

        inputs.forEach(input => {
            input.disabled = isMeninggal;
            if (isMeninggal) {
                input.value = ''; // Kosongkan otomatis jika meninggal
            }
        });
    }

    function toggleAyahInputs() {
        toggleInputs('status_ayah', 'ayah-input');
    }

    function toggleIbuInputs() {
        toggleInputs('status_ibu', 'ibu-input');
    }

    function toggleWaliInputs() {
        toggleInputs('status_wali', 'wali-input');
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Jalankan saat halaman dimuat
        toggleAyahInputs();
        toggleIbuInputs();
        toggleWaliInputs();

        // Pasang event listener ketika status berubah
        document.querySelector('select[name="status_ayah"]').addEventListener('change', toggleAyahInputs);
        document.querySelector('select[name="status_ibu"]').addEventListener('change', toggleIbuInputs);
        document.querySelector('select[name="status_wali"]').addEventListener('change', toggleWaliInputs);
    });
</script>


@endsection
