@extends('admin_template')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Setoran</h3>
                    </div>
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <form action="{{ route('setoran.update', $setoran->id_setoran) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                {{-- Kolom Kiri --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_santri">Nama Santri</label>
                                        <select name="id_santri" id="id_santri" class="form-control @error('id_santri') is-invalid @enderror" required>
                                            <option value="">- Pilih Santri -</option>
                                            @foreach ($santris as $santri)
                                                <option
                                                    value="{{ $santri->id_santri }}"
                                                    data-id_kelas="{{ $santri->id_kelas }}"
                                                    {{ old('id_santri', $setoran->id_santri) == $santri->id_santri ? 'selected' : '' }}>
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
                                        <select name="id_pengajar" id="id_pengajar" class="form-control @error('id_pengajar') is-invalid @enderror">
                                            <option value="">Pilih Pengajar</option>
                                            @foreach ($pengajars as $pengajar)
                                                <option value="{{ $pengajar->id_pengajar }}" {{ old('id_pengajar', $setoran->id_pengajar) == $pengajar->id_pengajar ? 'selected' : '' }}>
                                                    {{ $pengajar->nama }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_pengajar')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="tgl_setoran">Tanggal Setoran</label>
                                        <input type="date" name="tgl_setoran" id="tgl_setoran" class="form-control @error('tgl_setoran') is-invalid @enderror" value="{{ old('tgl_setoran', $setoran->tgl_setoran) }}" required>
                                        @error('tgl_setoran')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="id_kelas">Kelas</label>
                                        <select name="id_kelas" id="id_kelas" class="form-control @error('id_kelas') is-invalid @enderror" required>
                                            <option value="">- Pilih Kelas -</option>
                                            @foreach ($kelas as $kelass)
                                                <option value="{{ $kelass->id_kelas }}" {{ old('id_kelas', $setoran->id_kelas) == $kelass->id_kelas ? 'selected' : '' }}>
                                                    {{ $kelass->nama_kelas }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_kelas')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Kolom Kanan --}}
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="id_target">Target</label>
                                        <select name="id_target" id="id_target" class="form-control @error('id_target') is-invalid @enderror" required>
                                            <option value="">- Pilih Target -</option>
                                            @foreach ($targets as $target)
                                                <option value="{{ $target->id_target }}" {{ old('id_target', $setoran->id_target) == $target->id_target ? 'selected' : '' }}>
                                                    Target {{ $loop->iteration }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_target')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="id_surat">Nama Surat</label>
                                        <select name="id_surat" id="id_surat" class="form-control  @error('id_surat') is-invalid @enderror" required>
                                            <option value="">- Pilih Nama Surat -</option>
                                            @foreach ($surats as $surat)
                                                <option value="{{ $surat->id_surat }}" {{ old('id_surat', $setoran->id_surat) == $surat->id_surat ? 'selected' : '' }}>
                                                    {{ $surat->nama_surat }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('id_surat')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label for="jumlah_ayat_start">Ayat Mulai</label>
                                            <input type="number" name="jumlah_ayat_start" id="jumlah_ayat_start" class="form-control @error('jumlah_ayat_start') is-invalid @enderror" value="{{ old('jumlah_ayat_start', $setoran->jumlah_ayat_start) }}" required>
                                            @error('jumlah_ayat_start')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group col-md-6">
                                            <label for="jumlah_ayat_end">Ayat Akhir</label>
                                            <input type="number" name="jumlah_ayat_end" id="jumlah_ayat_end" class="form-control @error('jumlah_ayat_end') is-invalid @enderror" value="{{ old('jumlah_ayat_end', $setoran->jumlah_ayat_end) }}" required>
                                            @error('jumlah_ayat_end')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="nilai">Nilai</label>
                                        <input type="number" name="nilai" id="nilai" class="form-control @error('nilai') is-invalid @enderror" value="{{ old('nilai', $setoran->nilai) }}" required>
                                        @error('nilai')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="keterangan">Keterangan</label>
                                        <textarea name="keterangan" id="keterangan" rows="3" class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $setoran->keterangan) }}</textarea>
                                        @error('keterangan')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <a href="{{ route('setoran.index') }}" class="btn btn-secondary ml-2">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


    @section('script')
    <script>
        $(document).ready(function () {
            // Inisialisasi Select2
            $('.select2').select2();

            // Saat Santri dipilih
            $('#id_santri').on('change', function () {
                const selectedSantri = $(this).find('option:selected');
                const santriId = $(this).val();
                const idKelas = selectedSantri.data('id_kelas');

                // Reset dropdown
                $('#id_target').empty().append('<option value="">- Pilih Target -</option>');
                $('#id_surat').empty().append('<option value="">- Pilih Nama Surat -</option>');
                $('#id_kelas').val(null).trigger('change.select2');

                // Fallback: set kelas jika ada di data attribute
                if (idKelas) {
                    $('#id_kelas').val(idKelas).trigger('change.select2');
                }

                if (santriId) {
                    // Ambil target
                    $.ajax({
                        url: "{{ route('setoran.gettargetsBySantri', ':santri_id') }}".replace(':santri_id', santriId),
                        type: 'GET',
                        success: function (data) {
                            if (data.target.length > 0) {
                                data.target.forEach(function (target, index) {
                                    $('#id_target').append(`<option value="${target.id_target}">Target ${index + 1}</option>`);
                                });
                            } else {
                                $('#id_target').append('<option value="">- Tidak ada target -</option>');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error mengambil target:", error);
                        }
                    });

                    // Ambil kelas
                    $.ajax({
                        url: "{{ route('setoran.getKelasBySantri', ':id') }}".replace(':id', santriId),
                        type: 'GET',
                        dataType: 'json',
                        success: function (response) {
                            if (response.success && response.id_kelas) {
                                $('#id_kelas').val(response.id_kelas).trigger('change.select2');
                            } else {
                                $('#id_kelas').val(null).trigger('change.select2');
                            }
                        },
                        error: function () {
                            $('#id_kelas').val(null).trigger('change.select2');
                        }
                    });
                }
            });

            // Ketika target dipilih, ambil nama surat
            $('#id_target').on('change', function () {
                var groupId = $(this).val();
                var santriId = $('#id_santri').val();

                $('#id_surat').empty().append('<option value="">- Pilih Nama Surat -</option>');

                if (groupId && santriId) {
                    $.ajax({
                        url: "{{ url('get-nama-surat') }}",
                        type: 'GET',
                        data: {
                            group_id: groupId,
                            santri_id: santriId
                        },
                        success: function (data) {
                            let uniqueSurats = new Set();

                            if (data.surats && data.surats.length > 0) {
                                data.surats.forEach(function (surat) {
                                    if (surat.id_surat && !uniqueSurats.has(surat.id_surat)) {
                                        uniqueSurats.add(surat.id_surat);
                                        $('#id_surat').append(`<option value="${surat.id_surat}">${surat.nama_surat}</option>`);
                                    }
                                });
                            } else {
                                $('#id_surat').append('<option value="">- Tidak ada surat -</option>');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error mengambil surat:", error);
                        }
                    });
                }
            });

            // Validasi jumlah ayat saat surat dipilih
            $('#id_surat').on('change', function () {
                var idSurat = $(this).val();
                var idSantri = $('#id_santri').val();
                var idGroup = $('#id_target').val();

                if (idSurat && idSantri && idtarget) {
                    $.ajax({
                        url: "{{ url('get-ayats-validation') }}",
                        type: 'GET',
                        data: {
                            id_surat: idSurat,
                            id_santri: idSantri,
                            id_target: idtarget
                        },
                        success: function (data) {
                            if (data.success) {
                                var jumlahAwal = data.jumlah_ayat_target_awal;
                                var jumlahAkhir = data.jumlah_ayat_target;

                                $('#jumlah_ayat_start').on('input', function () {
                                    var start = $(this).val();
                                    if (start < jumlahAwal || start > jumlahAkhir) {
                                        alert('Jumlah ayat start tidak boleh di luar rentang ' + jumlahAwal + ' - ' + jumlahAkhir + '.');
                                    }
                                });

                                $('#jumlah_ayat_end').on('input', function () {
                                    var end = $(this).val();
                                    if (end < jumlahAwal || end > jumlahAkhir) {
                                        alert('Jumlah ayat end tidak boleh di luar rentang ' + jumlahAwal + ' - ' + jumlahAkhir + '.');
                                    }
                                });
                            } else {
                                alert(data.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error validasi target:", error);
                        }
                    });
                }
            });
        });
    </script>


    @endsection
