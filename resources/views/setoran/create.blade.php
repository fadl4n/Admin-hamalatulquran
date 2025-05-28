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
                                                    </option>
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
        // Inisialisasi Select2
        $('.select2').select2();

        // Saat Santri dipilih
        $('#id_santri').on('change', function() {
            var santriId = $(this).val();

            // Kosongkan dropdown target, surat, dan kelas
            $('#id_target').empty().append('<option value="">- Pilih Target -</option>');
            $('#id_surat').empty().append('<option value="">- Pilih Nama Surat -</option>');
            $('#id_kelas').val(null).trigger('change.select2');

            if (santriId) {
                // Ambil target
                $.ajax({
                    url: "{{ route('setoran.gettargetsBySantri', ':santri_id') }}".replace(':santri_id', santriId),
                    type: 'GET',
                    success: function(data) {
                        if (data.target.length > 0) {
                            data.target.forEach(function(target, index) {
                                $('#id_target').append(
                                    '<option value="' + target.id_target + '">Target ' + (index + 1) + '</option>'
                                );
                            });
                        } else {
                            $('#id_target').append('<option value="">- Tidak ada target -</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error mengambil target:", error);
                    }
                });

                // Ambil kelas
                $.ajax({
                    url: "{{ route('setoran.getKelasBySantri', ':id') }}".replace(':id', santriId),
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

        // Saat Target dipilih
        $('#id_target').on('change', function() {
            var targetId = $(this).val();
            $('#id_surat').empty().append('<option value="">- Pilih Nama Surat -</option>');

            if (targetId) {
                $.ajax({
                    url: "{{ url('get-nama-surat') }}",
                    type: 'GET',
                    data: {
                        group_id: targetId // hanya kirim id_target, sesuai permintaan
                    },
                    success: function(data) {
                        if (data.surats && data.surats.length > 0) {
                            data.surats.forEach(function(surat) {
                                $('#id_surat').append('<option value="' + surat.id_surat + '">' + surat.nama_surat + '</option>');
                            });
                        } else {
                            $('#id_surat').append('<option value="">- Tidak ada surat -</option>');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error mengambil surat:", error);
                    }
                });
            }
        });

        // Validasi jumlah ayat saat nama surat dipilih
        $('#id_surat').on('change', function() {
            var idSurat = $(this).val();
            var idSantri = $('#id_santri').val();
            var idTarget = $('#id_target').val();

            if (idSurat && idSantri && idTarget) {
                $.ajax({
                    url: "{{ url('get-ayats-validation') }}",
                    type: 'GET',
                    data: {
                        id_surat: idSurat,
                        id_santri: idSantri,
                        id_target: idTarget
                    },
                    success: function(data) {
                        if (data.success) {
                            var jumlahAwal = data.jumlah_ayat_target_awal;
                            var jumlahAkhir = data.jumlah_ayat_target;

                            // Validasi jumlah ayat start
                            $('#jumlah_ayat_start').off('input').on('input', function() {
                                var start = parseInt($(this).val());
                                if (start < jumlahAwal || start > jumlahAkhir) {
                                    alert('Jumlah ayat start harus antara ' + jumlahAwal + ' dan ' + jumlahAkhir);
                                }
                            });

                            // Validasi jumlah ayat end
                            $('#jumlah_ayat_end').off('input').on('input', function() {
                                var end = parseInt($(this).val());
                                if (end < jumlahAwal || end > jumlahAkhir) {
                                    alert('Jumlah ayat end harus antara ' + jumlahAwal + ' dan ' + jumlahAkhir);
                                }
                            });
                        } else {
                            alert(data.message);
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
