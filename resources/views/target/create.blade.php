@extends('admin_template')

@section('title page')
    Tambah Target
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-body">
                            {{-- Tampilkan pesan error jika ada --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('target.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    {{-- Kolom Kiri --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Santri</label>
                                            <select name="id_santri" class="form-control" id="id_santri" required>
                                                <option value="">-- Pilih Santri --</option>
                                                @foreach ($santris as $santri)
                                                    <option value="{{ $santri->id_santri }}" data-id_kelas="{{ $santri->id_kelas }}">
                                                        {{ $santri->nama }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Nama Pengajar</label>
                                            <select name="id_pengajar" class="form-control" required>
                                                <option value="">-- Pilih Pengajar --</option>
                                                @foreach ($pengajars as $pengajar)
                                                    <option value="{{ $pengajar->id_pengajar }}">{{ $pengajar->nama }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Nama Kelas</label>
                                                <select name="id_kelas" class="form-control" id="id_kelas" required>
                                                    <option value="">-- Pilih Kelas --</option>
                                                    @foreach ($kelas as $kls)
                                                        <option value="{{ $kls->id_kelas }}">{{ $kls->nama_kelas }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Nama Surat</label>
                                                <select name="id_surat" class="form-control" id="id_surat" required>
                                                    <option value="">-- Pilih Surat --</option>
                                                    @foreach ($surats as $surat)
                                                        <option value="{{ $surat->id_surat }}" data-jumlah="{{ $surat->jumlah_ayat }}">
                                                            {{ $surat->nama_surat }} ({{ $surat->jumlah_ayat }} ayat)
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Kolom Kanan --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Jumlah Ayat Target Awal</label>
                                            <input type="number" name="jumlah_ayat_target_awal"
                                                class="form-control @error('jumlah_ayat_target_awal') is-invalid @enderror"
                                                id="jumlah_ayat_target_awal" min="1"
                                                value="{{ old('jumlah_ayat_target_awal') }}" required>
                                            @error('jumlah_ayat_target_awal')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label>Jumlah Ayat Target</label>
                                            <input type="number" name="jumlah_ayat_target"
                                                class="form-control @error('jumlah_ayat_target') is-invalid @enderror"
                                                id="jumlah_ayat_target" min="1" value="{{ old('jumlah_ayat_target') }}"
                                                required>
                                            @error('jumlah_ayat_target')
                                                <small class="text-danger">{{ $message }}</small>
                                            @enderror
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label>Tanggal Mulai</label>
                                                <input type="date" name="tgl_mulai" class="form-control" required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label>Tanggal Target</label>
                                                <input type="date" name="tgl_target" class="form-control" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tombol Aksi --}}
                                <div class="form-group mt-4 d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary" style="margin-right: 5px;">Simpan</button>
                                    <button type="submit" class="btn btn-info" name="continue" value="true" id="btnContinue" style="margin-right: 5px;">Lanjut</button>
                                    <a href="{{ route('target.index') }}" class="btn btn-secondary" style="margin-right: 5px;">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const santriSelect = document.querySelector('select[name="id_santri"]');
            const kelasSelect = document.querySelector('select[name="id_kelas"]');
            const jumlahAyatInput = document.getElementById("jumlah_ayat_target");
            const suratSelect = document.getElementById("id_surat");
            const errorText = document.getElementById("error_jumlah_ayat");
            const form = document.querySelector("form");
            const btnContinue = document.getElementById("btnContinue");

            // Auto-pilih kelas dari santri
            santriSelect.addEventListener('change', function() {
                const selectedSantri = santriSelect.options[santriSelect.selectedIndex];
                const kelasId = selectedSantri.getAttribute('data-id_kelas');

                if (kelasId) {
                    for (let i = 0; i < kelasSelect.options.length; i++) {
                        if (kelasSelect.options[i].value === kelasId) {
                            kelasSelect.selectedIndex = i;
                            break;
                        }
                    }
                }
            });

            // Validasi jumlah ayat
            jumlahAyatInput.addEventListener("input", function() {
                let selectedSurat = suratSelect.options[suratSelect.selectedIndex];
                let maxAyat = selectedSurat.dataset.jumlah;

                if (jumlahAyatInput.value > maxAyat) {
                    errorText.textContent = "Jumlah ayat target tidak boleh lebih dari " + maxAyat;
                    jumlahAyatInput.value = maxAyat;
                } else {
                    errorText.textContent = "";
                }
            });

            // Reset error dan jumlah ayat saat ganti surat
            suratSelect.addEventListener("change", function() {
                jumlahAyatInput.value = "";
                errorText.textContent = "";
            });
        });
        document.addEventListener("DOMContentLoaded", function () {
        const santriSelect = document.getElementById("id_santri");
        const kelasSelect = document.getElementById("id_kelas");

        santriSelect.addEventListener("change", function () {
            const selectedSantri = this.options[this.selectedIndex];
            const idKelas = selectedSantri.getAttribute("data-id_kelas");

            if (idKelas) {
                // Pilih opsi kelas yang sesuai
                for (let i = 0; i < kelasSelect.options.length; i++) {
                    if (kelasSelect.options[i].value === idKelas) {
                        kelasSelect.selectedIndex = i;
                        break;
                    }
                }
            } else {
                kelasSelect.selectedIndex = 0; // Reset kalau tidak ditemukan
            }
        });
    });

    </script>

@endsection
