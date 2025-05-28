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
                <div class="col-md-10 offset-md-1">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Form Tambah Setoran</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('setoran.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                     {{-- Kolom Kiri --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_santri">Nama Santri</label>
                                            <select name="id_santri" id="id_santri" class="form-control" required>
                                                <option value="">- Pilih Santri -</option>
                                                @foreach ($santris as $santri)
                                                    <option value="{{ $santri->id_santri }}">
                                                        {{ $santri->nama }} | {{ $santri->nisn }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('id_santri')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="form-group">
                                            <label for="id_pengajar">Pengajar</label>
                                            <select name="id_pengajar" id="id_pengajar" class="form-control">
                                                <option value="">Pilih Pengajar</option>
                                                @foreach ($pengajars as $pengajar)
                                                    <option value="{{ $pengajar->id_pengajar }}">{{ $pengajar->nama }}
                                          <div class=""></div>           </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="tgl_setoran">Tanggal Setoran</label>
                                                <input type="date" name="tgl_setoran" id="tgl_setoran" class="form-control"
                                                    required>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="id_kelas">Kelas</label>
                                                <select name="id_kelas" id="id_kelas" class="form-control" required>
                                                    <option value="">- Pilih Kelas -</option>
                                                    @foreach ($kelas as $kelass)
                                                        <option value="{{ $kelass->id_kelas }}">
                                                            {{ $kelass->nama_kelas }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('id_kelas')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="id_target">Target</label>
                                                <select name="id_target" id="id_target" class="form-control" required>
                                                    <option value="">- Pilih Target -</option>
                                                </select>
                                                @error('id_target')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="id_surat">Nama Surat</label>
                                                <select name="id_surat" id="id_surat" class="form-control" required>
                                                    <option value="">- Pilih Nama Surat -</option>
                                                    @foreach ($surats as $s)
                                                        <option value="{{ $s->id_surat }}">{{ $s->nama_surat }}</option>
                                                    @endforeach
                                                </select>
                                                @error('id_surat')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Kolom Kanan --}}
                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="jumlah_ayat_start">Ayat Mulai</label>
                                                <input type="number" name="jumlah_ayat_start" id="jumlah_ayat_start"
                                                    class="form-control" required value="{{ old('jumlah_ayat_start') }}">
                                                @if ($errors->has('jumlah_ayat_start'))
                                                    <div class="text-danger">{{ $errors->first('jumlah_ayat_start') }}</div>
                                                @endif
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="jumlah_ayat_end">Ayat Akhir</label>
                                                <input type="number" name="jumlah_ayat_end" id="jumlah_ayat_end"
                                                    class="form-control" required value="{{ old('jumlah_ayat_end') }}">
                                                @if ($errors->has('jumlah_ayat_end'))
                                                    <div class="text-danger">{{ $errors->first('jumlah_ayat_end') }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="nilai">Nilai</label>
                                            <input type="number" name="nilai" id="nilai" class="form-control"
                                                required value="{{ old('nilai') }}">
                                            @if ($errors->has('nilai'))
                                                <div class="text-danger">{{ $errors->first('nilai') }}</div>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan</label>
                                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control" placeholder="Masukkan keterangan..."></textarea>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tombol Aksi --}}
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
    <script>
        $(document).ready(function() {
            $('#id_santri').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var idKelas = selectedOption.data('id_kelas');

                // Set value dropdown kelas
                $('#id_kelas').val(idKelas);
            });

            // Inisialisasi Select2
            $('.select2').select2();
            $('#id_santri').on('change', function() {
                var santriId = $(this).val();

                // Reset dropdown target, surat, dan kelas dulu
                $('#id_target').empty().append('<option value="">- Pilih Target -</option>');
                $('#id_surat').empty().append('<option value="">- Pilih Nama Surat -</option>');
                $('#id_kelas').val(null).trigger('change.select2');

                if (santriId) {
                    // Ambil target
                    $.ajax({
                        url: "{{ route('setoran.gettargetsBySantri', ':santri_id') }}".replace(
                            ':santri_id', santriId),
                        type: 'GET',
                        success: function(data) {
                            if (data.target.length > 0) {
                                data.target.forEach(function(target, index) {
                                    $('#id_target').append(
                                        '<option value="' + target.id_target +
                                        '">Target ' + (index + 1) + '</option>'
                                    );
                                });
                            } else {
                                $('#id_target').append(
                                    '<option value="">- Tidak ada target -</option>');
                            }

                        },
                        error: function(xhr, status, error) {
                            console.error("Error mengambil target:", error);
                        }
                    });

                    // Ambil kelas
                    $.ajax({
                        url: "{{ route('setoran.getKelasBySantri', ':id') }}".replace(':id',
                            santriId),
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success && response.id_kelas) {
                                $('#id_kelas').val(response.id_kelas).trigger('change.select2');
                            } else {
                                $('#id_kelas').val(null).trigger('change.select2');
                            }
                        },
                        error: function() {
                            $('#id_kelas').val(null).trigger('change.select2');
                        }
                    });
                }
            });



            // Ketika target dipilih, update nama surat yang terkait
            $('#id_target').on('change', function() {
                var groupId = $(this).val();
                var santriId = $('#id_santri').val(); // Ambil ID Santri yang dipilih

                // Kosongkan dropdown id_surat (bukan nama_surat lagi)
                $('#id_surat').empty().append('<option value="">- Pilih Nama Surat -</option>');

                if (groupId && santriId) {
                    $.ajax({
                        url: "{{ url('get-nama-surat') }}",
                        type: 'GET',
                        data: {
                            group_id: groupId,
                            santri_id: santriId
                        },
                        success: function(data) {
                            console.log("Surat yang diterima:", data); // Debugging

                            let uniqueSurats = new Set();

                            if (data.surats && data.surats.length > 0) {
                                data.surats.forEach(function(surat) {
                                    if (surat.id_surat && !uniqueSurats.has(surat
                                            .id_surat)) {
                                        uniqueSurats.add(surat.id_surat);
                                        $('#id_surat').append('<option value="' + surat
                                            .id_surat + '">' + surat.nama_surat +
                                            '</option>');
                                    }
                                });
                            } else {
                                $('#id_surat').append(
                                    '<option value="">- Tidak ada surat -</option>');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error mengambil surat:", error);
                        }
                    });
                }
            });

            // Ketika nama surat dipilih, lakukan validasi jumlah ayat
            $('#nama_surat').on('change', function() {
                var idSurat = $(this).val();
                var idSantri = $('#id_santri').val();
                var idGroup = $('#id_target').val();

                if (idSurat && idSantri && idGroup) {
                    $.ajax({
                        url: "{{ url('get-ayats-validation') }}",
                        type: 'GET',
                        data: {
                            id_surat: idSurat,
                            id_santri: idSantri,
                            id_target: idGroup
                        },
                        success: function(data) {
                            console.log("Validasi target diterima:", data); // Debugging

                            if (data.success) {
                                var jumlahAyatTargetAwal = data.jumlah_ayat_target_awal;
                                var jumlahAyatTarget = data.jumlah_ayat_target;

                                // Validasi input jumlah ayat start
                                $('#jumlah_ayat_start').on('input', function() {
                                    var jumlahAyatStart = $(this).val();
                                    if (jumlahAyatStart < jumlahAyatTargetAwal ||
                                        jumlahAyatStart > jumlahAyatTarget) {
                                        alert('Jumlah ayat start tidak boleh lebih kecil dari ' +
                                            jumlahAyatTargetAwal +
                                            ' dan tidak boleh lebih besar dari ' +
                                            jumlahAyatTarget + '.');
                                    }
                                });

                                // Validasi input jumlah ayat end
                                $('#jumlah_ayat_end').on('input', function() {
                                    var jumlahAyatEnd = $(this).val();
                                    if (jumlahAyatEnd < jumlahAyatTargetAwal ||
                                        jumlahAyatEnd > jumlahAyatTarget) {
                                        alert('Jumlah ayat end tidak boleh lebih kecil dari ' +
                                            jumlahAyatTargetAwal +
                                            ' dan tidak boleh lebih besar dari ' +
                                            jumlahAyatTarget + '.');
                                    }
                                });
                            } else {
                                alert(data.message); // Menampilkan pesan kesalahan dari server
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error validasi target:", error);
                        }
                    });
                }
            });
        });
    </script>
@endsection
