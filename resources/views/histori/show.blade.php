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
                            <div class="d-flex justify-content-between pb-2">
                                <h5>Histori Santri</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover histori-list w-100">
                                    <thead class="bg-navy disabled">
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
        </div>
    </section>

    <!-- Modal Edit Nilai -->
    <div class="modal fade" id="nilaiModal" tabindex="-1" aria-labelledby="nilaiModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="nilaiForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="nilaiModalLabel">Input/Edit Nilai</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="histori_id" name="histori_id">
                        <div class="form-group">
                            <label for="nilai">Nilai:</label>
                            <input type="number" id="nilai" name="nilai" class="form-control" required>
                            <small id="nilaiError" class="text-danger d-none">Nilai tidak boleh negatif!</small>
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

    <!-- Modal Preview Nilai -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">Inputan Nilai dan Tanggal</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody id="previewTableBody">
                                <!-- Data akan dimuat di sini -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            var table = $('.histori-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('histori.fnGetData') }}",
                    data: function(d) {
                        d.search_value = $('input[type="search"]').val();
                    }
                },
                columns: [
                    { data: 'nama', name: 'nama' },
                    { data: 'kelas', name: 'kelas' },
                    { data: 'nama_surat', name: 'nama_surat' },
                    { data: 'ayat', name: 'ayat' },
                    { data: 'persentase', name: 'persentase' },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data) {
                            const statusLabel = {
                                0: 'Belum Mulai',
                                1: 'Proses',
                                2: 'Selesai',
                                3: 'Terlambat'
                            };
                            const statusClass = {
                                0: 'secondary',
                                1: 'warning',
                                2: 'success',
                                3: 'danger'
                            };
                            return `<span class="badge bg-${statusClass[data] ?? 'light'}">${statusLabel[data] ?? 'Tidak Diketahui'}</span>`;
                        }
                    },
                    { data: 'nilai', name: 'nilai' },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-primary btn-sm edit-nilai" data-id="${row.id_target}" data-nilai="${row.nilai}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-info btn-sm preview-nilai" data-id="${row.id_target}">
                                    <i class="fas fa-eye"></i>
                                </button>
                            `;
                        }
                    }
                ],
                columnDefs: [{
                    className: "text-center",
                    targets: "_all"
                }]
            });

            // Modal input/edit nilai
            $(document).on('click', '.edit-nilai', function() {
                $('#histori_id').val($(this).data('id'));
                $('#nilai').val($(this).data('nilai'));
                $('#nilaiError').addClass('d-none');
                $('#nilaiModal').modal('show');
            });

            $('#nilaiForm').submit(function(e) {
                e.preventDefault();
                let id_target = $('#histori_id').val();
                let nilai = $('#nilai').val();

                if (nilai < 0) {
                    $('#nilaiError').removeClass('d-none');
                    return;
                }

                $.ajax({
                    url: `/histori/update-nilai/${id_target}`,
                    method: 'POST',
                    data: {
                        nilai: nilai,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#nilaiModal').modal('hide');
                            table.ajax.reload(null, false);
                        } else {
                            alert('Gagal memperbarui nilai');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat memperbarui nilai');
                    }
                });
            });

            // Modal preview nilai
            $(document).on('click', '.preview-nilai', function() {
                let id = $(this).data('id');

                $.ajax({
                    url: `/histori/get-preview/${id}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let tableBody = $('#previewTableBody').empty();
                            response.data.forEach(function(item) {
                                tableBody.append(`<tr><td>${item.updated_at}</td><td>${item.nilai}</td></tr>`);
                            });
                            $('#previewModal').modal('show');
                        } else {
                            alert('Gagal mengambil data preview');
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mengambil data preview');
                    }
                });
            });
        });
    </script>
@endsection
