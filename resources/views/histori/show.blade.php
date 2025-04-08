@extends('admin_template')

@section('css')
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
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
                            <small id="nilaiError" class="text-danger" style="display: none;">Nilai tidak boleh
                                negatif!</small>
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

    <!-- Modal untuk Preview Nilai -->
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

                        <table class="table table-bordered table-hover histori-list w-100">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody id="previewTableBody">
                                <!-- Data akan dimuat disini -->
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
                searching: true,
                ajax: {
                    url: "{{ route('histori.fnGetData') }}",
                    data: function(d) {
                        // Kirimkan nilai pencarian saat pencarian dilakukan
                        d.search_value = $('input[type="search"]').val();
                    }
                },
                columns: [{
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'kelas',
                        name: 'kelas'
                    },
                    {
                        data: 'nama_surat',
                        name: 'nama_surat'
                    },
                    {
                        data: 'ayat',
                        name: 'ayat'
                    },
                    {
                        data: 'persentase',
                        name: 'persentase'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, row) {
                            // Ganti status angka dengan label teks
                            switch (data) {
                                case 0:
                                    return '<span class="badge bg-secondary">Belum Mulai</span>';
                                case 1:
                                    return '<span class="badge bg-warning">Proses</span>';
                                case 2:
                                    return '<span class="badge bg-success">Selesai</span>';
                                case 3:
                                    return '<span class="badge bg-danger">Terlambat</span>';
                                default:
                                    return data;
                            }
                        }
                    },
                    {
                        data: 'nilai',
                        name: 'nilai'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `<button class="btn btn-primary btn-sm edit-nilai" data-id="${row.id_target}" data-nilai="${row.nilai}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-info btn-sm preview-nilai" data-id="${row.id_target}">
                                    <i class="fas fa-eye"></i>
                                </button>`;
                        }
                    }
                ],
                columnDefs: [{
                    className: "text-center",
                    targets: [0, 1, 2, 3, 4, 5, 6, 7]
                }]
            });

            // Tampilkan modal edit nilai saat tombol edit diklik
            $(document).on('click', '.edit-nilai', function() {
                var id = $(this).data('id');
                var nilai = $(this).data('nilai');
                $('#histori_id').val(id);
                $('#nilai').val(nilai);
                $('#nilaiError').hide(); // Sembunyikan pesan error saat modal dibuka
                $('#nilaiModal').modal('show');
            });

            // Submit form untuk menyimpan nilai
            $('#nilaiForm').submit(function(e) {
                e.preventDefault();

                var id_target = $('#histori_id').val();
                var nilai = $('#nilai').val();
                var nilaiError = $('#nilaiError');

                // Validasi: Cek apakah nilai negatif
                if (nilai < 0) {
                    nilaiError.text('Nilai tidak boleh negatif!').show();
                    return; // Hentikan proses submit
                } else {
                    nilaiError.hide(); // Sembunyikan pesan error jika nilai valid
                }

                $.ajax({
                    url: `/histori/update-nilai/${id_target}`, // Perbaikan URL
                    method: 'POST',
                    data: {
                        nilai: nilai,
                        _token: '{{ csrf_token() }}' // Sertakan token CSRF untuk keamanan
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#nilaiModal').modal('hide');
                            table.ajax.reload(null, false);
                        } else {
                            alert('Gagal memperbarui nilai');
                        }
                    },
                    error: function(error) {
                        alert('Terjadi kesalahan saat memperbarui nilai');
                    }
                });
            });

            // Tampilkan modal preview saat tombol preview diklik
            $(document).on('click', '.preview-nilai', function() {
                var id = $(this).data('id');

                $.ajax({
                    url: `/histori/get-preview/${id}`, // Pastikan URL benar
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var tableBody = $('#previewTableBody');
                            tableBody.empty(); // Bersihkan tabel sebelumnya

                            response.data.forEach(function(item) {
                                tableBody.append(
                                    `<tr>
                            <td>${item.updated_at}</td> <!-- Tanggal dan Waktu -->
                            <td>${item.nilai}</td>       <!-- Nilai -->
                        </tr>`
                                );
                            });

                            $('#previewModal').modal('show');
                        } else {
                            alert('Gagal mengambil data preview');
                        }
                    },
                    error: function(error) {
                        alert('Terjadi kesalahan saat mengambil data preview');
                    }
                });
            });

        });
    </script>
@endsection
