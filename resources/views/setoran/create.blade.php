@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/daterangepicker/daterangepicker.css') }}">
@endsection

@section('title page')
    Tambah Setoran
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Tambah Setoran</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('setoran.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="santri">Nama Santri</label>
                                            <select name="id_santri" class="form-control" required>
                                                <option value="">- Pilih Santri -</option>
                                                @foreach ($santris as $santri)
                                                    <option value="{{ $santri->id_santri }}">{{ $santri->nama }} |
                                                        {{ $santri->nisn }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kelas">Kelas</label>
                                            <select name="id_kelas" class="form-control" required>
                                                <option value="">- Pilih Kelas -</option>
                                                @foreach ($kelas as $kelass)
                                                    <option value="{{ $kelass->id_kelas }}">{{ $kelass->nama_kelas }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_setoran">Tanggal Setoran</label>
                                            <input type="date" name="tgl_setoran" id="tgl_setoran" class="form-control"
                                                required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="surat">Surat</label>
                                            <select name="id_surat" class="form-control" required>
                                                <option value="">- Pilih Surat -</option>
                                                @foreach ($surats as $surat)
                                                    <option value="{{ $surat->id_surat }}">{{ $surat->nama_surat }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <!-- Formulir HTML dengan penyesuaian -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jumlah_ayat_start">Ayat Mulai</label>
                                            <input type="number" name="jumlah_ayat_start" id="jumlah_ayat_start"
                                                class="form-control @error('jumlah_ayat_start') is-invalid @enderror"
                                                required>
                                            @error('jumlah_ayat_start')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jumlah_ayat_end">Ayat Akhir</label>
                                            <input type="number" name="jumlah_ayat_end" id="jumlah_ayat_end"
                                                class="form-control @error('jumlah_ayat_end') is-invalid @enderror"
                                                value="{{ old('jumlah_ayat_end') }}" required>
                                            @error('jumlah_ayat_end')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>




                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="1">Selesai</option>
                                                <option value="0">Proses</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_pengajar">Pengajar</label>
                                        <select name="id_pengajar" id="id_pengajar" class="form-control">
                                            <option value="">Pilih Pengajar</option>
                                            @foreach($pengajars as $pengajar)
                                                <option value="{{ $pengajar->id_pengajar }}" {{ old('id_pengajar') == $pengajar->id_pengajar ? 'selected' : '' }}>
                                                    {{ $pengajar->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_pengajar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="keterangan">Keterangan</label>
                                    <textarea name="keterangan" id="keterangan" rows="3" class="form-control" placeholder="Masukkan keterangan..."></textarea>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-success">Simpan Setoran</button>
                                    <a href="{{ route('setoran.index') }}" class="btn btn-secondary ml-2">Batal</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script src="{{ asset('/bower_components/admin-lte/plugins/select2/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('/bower_components/admin-lte/plugins/moment/moment.min.js') }}"></script>
    <script src="{{ asset('/bower_components/admin-lte/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script>
        // Inisialisasi Select2
        $('.select2').select2();

        // Fungsi untuk memeriksa dan memperbarui input jumlah ayat
        function updateJumlahAyat() {
            var suratId = $('#surat').val();
            if (suratId) {
                $.ajax({
                    url: "{{ url('/surats') }}/" + suratId,
                    method: 'GET',
                    success: function(response) {
                        let totalAyat = response.jumlah_ayat;
                        $('#jumlah_ayat_end').attr('max', totalAyat); // Menyesuaikan maksimal jumlah ayat
                    }
                });
            } else {
                $('#jumlah_ayat_start').val('');
                $('#jumlah_ayat_end').val('');
            }
        }

        // Event listener untuk perubahan pada dropdown surat
        $('#surat').on('change', function() {
            updateJumlahAyat();
        });

        // Event listener untuk perubahan pada input jumlah ayat
        $('#jumlah_ayat_end').on('input', function() {
            var startAyat = $('#jumlah_ayat_start').val();
            var endAyat = $(this).val();
            var suratId = $('#surat').val();

            if (suratId) {
                $.ajax({
                    url: "{{ url('/surats') }}/" + suratId,
                    method: 'GET',
                    success: function(response) {
                        let totalAyat = response.jumlah_ayat;
                        if (endAyat > totalAyat) {
                            alert('Jumlah ayat dalam surat ini adalah ' + totalAyat + '.');
                            $('#jumlah_ayat_end').val('');
                        }
                    }
                });
            }
        });
    </script>
@endsection
