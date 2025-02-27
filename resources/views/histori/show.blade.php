@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection

@section('title page')
    Histori Santri
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filterSantri">Pilih Nama Santri:</label>
                                    <select id="filterSantri" class="form-control">
                                        <option value="">Semua Santri</option>
                                        @foreach ($santris as $santri)
                                            <option value="{{ $santri->id_santri }}">{{ $santri->nama }} | {{$santri->nisn}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered table-hover histori-list">
                                <thead class="bg-navy text-white">
                                    <tr>
                                        <th>Nama Santri</th>
                                        <th>Kelas</th>
                                        <th>Nama Surat</th>
                                        <th>Ayat</th>
                                        <th>Persentase</th>
                                        <th>Status</th>
                                        <th>Nilai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal untuk input/edit nilai -->
    <div class="modal fade" id="nilaiModal" tabindex="-1" aria-labelledby="nilaiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nilaiModalLabel">Input/Edit Nilai</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="nilaiForm">
                    <div class="modal-body">
                        <input type="hidden" id="histori_id" name="histori_id">
                        <div class="form-group">
                            <label for="nilai">Nilai:</label>
                            <input type="number" id="nilai" name="nilai" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    var oDataList = $('.histori-list').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url: "{{ route('histori.fnGetData') }}",
            data: function (d) {
                d.id_santri = $('#filterSantri').val();
            }
        },
        columns: [
            { data: 'nama', name: 'nama' },
            { data: 'kelas', name: 'kelas' },
            { data: 'nama_surat', name: 'nama_surat' },
            { data: 'ayat', name: 'ayat' },
            { data: 'persentase', name: 'persentase' },
            { data: 'status', name: 'status' },
            { data: 'nilai', name: 'nilai' },
            { data: 'aksi', name: 'aksi', orderable: false, searchable: false, render: function(data, type, row) {
    return `<button class="btn btn-primary btn-sm edit-nilai" data-id="${row.id_target}" data-nilai="${row.nilai}"><i class="fas fa-edit"></i></button>`;
}}

        ]
    });

    // Event ketika filter santri diubah
    $('#filterSantri').change(function () {
        oDataList.ajax.reload();
    });

    // Tampilkan modal edit nilai
    $(document).on('click', '.edit-nilai', function() {
        var id = $(this).data('id');
        var nilai = $(this).data('nilai');
        $('#histori_id').val(id);
        $('#nilai').val(nilai);
        $('#nilaiModal').modal('show');
    });

    $('#nilaiForm').submit(function(e) {
    e.preventDefault();
    var id_target = $('#histori_id').val(); // Ambil id_target
    var nilai = $('#nilai').val();
    $.ajax({
        url: `/histori/update-nilai/${id_target}`, // Kirim id_target
        method: 'POST',
        data: { nilai: nilai, _token: '{{ csrf_token() }}' },
        success: function(response) {
            $('#nilaiModal').modal('hide');
            oDataList.ajax.reload();
        },
        error: function(error) {
            alert('Gagal menyimpan nilai');
        }
    });
});

</script>
@endsection
