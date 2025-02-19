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
                                            <select name="id_santri" id="id_santri" class="form-control" required>
                                                <option value="">- Pilih Santri -</option>
                                                @foreach ($santris as $santri)
                                                    <option value="{{ $santri->id_santri }}">{{ $santri->nama }} | {{ $santri->nisn }}</option>
                                                @endforeach
                                            </select>
                                            @error('id_santri')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="kelas">Kelas</label>
                                            <select name="id_kelas" class="form-control" required>
                                                <option value="">- Pilih Kelas -</option>
                                                @foreach ($kelas as $kelass)
                                                    <option value="{{ $kelass->id_kelas }}">{{ $kelass->nama_kelas }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="tgl_setoran">Tanggal Setoran</label>
                                            <input type="date" name="tgl_setoran" id="tgl_setoran" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="id_group">Target</label>
                                            <select name="id_group" id="id_group" class="form-control" required>
                                                <option value="">- Pilih Target -</option>
                                            </select>
                                            @error('id_group')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="nama_surat">Nama Surat</label>
                                    <select name="nama_surat" id="nama_surat" class="form-control" required>
                                        <option value="">- Pilih Nama Surat -</option>
                                    </select>
                                    @error('nama_surat')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jumlah_ayat_start">Ayat Mulai</label>
                                            <input type="number" name="jumlah_ayat_start" id="jumlah_ayat_start" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="jumlah_ayat_end">Ayat Akhir</label>
                                            <input type="number" name="jumlah_ayat_end" id="jumlah_ayat_end" class="form-control" required>
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
                                                <option value="{{ $pengajar->id_pengajar }}">{{ $pengajar->nama }}</option>
                                            @endforeach
                                        </select>
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
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('.select2').select2();

            // Ketika santri dipilih, update target yang terkait
            $('#id_santri').on('change', function() {
                var santriId = $(this).val();

                // Kosongkan dropdown target dan nama surat
                $('#id_target').empty().append('<option value="">- Pilih Target -</option>');
                $('#nama_surat').empty().append('<option value="">- Pilih Nama Surat -</option>');

                if (santriId) {
                    // Ambil target berdasarkan id_santri
                    $.ajax({
    url: "{{ route('setoran.getSantriTargets', ':santri_id') }}".replace(':santri_id', santriId),
    type: 'GET',
    success: function(data) {
        // Update dropdown target dengan data yang diperoleh
        if (data.targets.length > 0) {
            data.targets.forEach(function(target) {
                $('#id_target').append('<option value="' + target.id_target + '" data-group="' + target.id_group + '">' + target.keterangan + '</option>');
            });
        } else {
            $('#id_target').append('<option value="">- Tidak ada target -</option>');
        }
    }
});

                }
            });

            // Ketika target dipilih, update nama surat yang terkait
            $('#id_target').on('change', function() {
                var targetId = $(this).val();
                var groupId = $(this).find(':selected').data('group');

                // Kosongkan dropdown nama surat
                $('#nama_surat').empty().append('<option value="">- Pilih Nama Surat -</option>');

                if (targetId && groupId) {
                    // Ambil nama surat berdasarkan id_target dan id_group
                    $.ajax({
                        url: "{{ url('get-nama-surat') }}",  // Endpoint untuk ambil nama surat
                        type: 'GET',
                        data: { target_id: targetId, group_id: groupId },
                        success: function(data) {
                            // Update dropdown nama surat dengan data yang diperoleh
                            data.surats.forEach(function(surat) {
                                $('#nama_surat').append('<option value="' + surat.id_surat + '">' + surat.nama_surat + '</option>');
                            });
                        }
                    });
                }
            });
        });
    </script>


@endsection
