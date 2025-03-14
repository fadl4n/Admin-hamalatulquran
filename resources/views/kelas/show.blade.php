    @extends('admin_template')

    @section('css')
        <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @endsection

    @section('title page')
        Daftar Kelas
    @endsection

    @section('content')
        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between pb-2">
                                    <h5>Daftar Kelas</h5>
                                    <button class="btn btn-info" data-toggle="modal" data-target="#modalTambahKelas">+ Tambah Kelas</button>
                                </div>
                                <table class="table table-bordered table-hover kelas-list">
                                    <thead class="bg-navy disabled">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kelas</th>
                                            <th>Jumlah Santri</th>
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

        <!-- Modal Tambah Kelas -->
        <div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-labelledby="modalTambahKelasLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTambahKelasLabel">Tambah Kelas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="formTambahKelas">
                            @csrf
                            <div class="form-group">
                                <label>Nama Kelas</label>
                                <input type="text" name="nama_kelas" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

      <!-- Modal Edit Kelas -->
<div class="modal fade" id="modalEditKelas" tabindex="-1" aria-labelledby="modalEditKelasLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditKelasLabel">Edit Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formEditKelas" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                </form>
            </div>
        </div>
    </div>
</div>

    @endsection

    @section('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        var oDataList = $('.kelas-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ url('/kelas/fn-get-data') }}",
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_kelas', name: 'nama_kelas' },
                { data: 'santri_count', name: 'santri_count', defaultContent: '0' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                { className: "text-center", targets: [0, 1, 2, 3] }
            ]
        });
        $('#formTambahKelas').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
        url: "{{ url('/kelas/store') }}",
        type: "POST",
        data: $(this).serialize(),
        success: function(response) {
            $('#modalTambahKelas').modal('hide');
            Swal.fire("Berhasil!", response.success, "success");
            oDataList.ajax.reload();
            $('#formTambahKelas')[0].reset();
        },
        error: function(xhr) {
            // Cek jika ada error pesan
            if (xhr.responseJSON && xhr.responseJSON.error) {
                Swal.fire("Gagal!", xhr.responseJSON.error, "error");
            } else {
                Swal.fire("Gagal!", "Terjadi kesalahan saat menambahkan kelas!", "error");
            }
        }
    });
});

      // Show Edit Modal dan Isikan Data ke Form
$('.kelas-list').on('click', '.btnEdit', function () {
    var id = $(this).data('id');

    // Ambil data kelas menggunakan AJAX
    $.ajax({
        url: "{{ url('/kelas/edit') }}/" + id, // URL untuk mengambil data kelas berdasarkan id
        type: "GET",
        success: function(response) {
            // Isi form edit dengan data yang diterima
            $('#modalEditKelas').find('input[name="nama_kelas"]').val(response.nama_kelas);

            // Set action untuk form edit
            $('#formEditKelas').attr('action', "{{ url('/kelas/update') }}/" + id);

            // Tampilkan modal edit
            $('#modalEditKelas').modal('show');
        },
        error: function(xhr) {
            Swal.fire("Gagal!", "Terjadi kesalahan saat mengambil data kelas!", "error");
        }
    });
});

$('#formEditKelas').on('submit', function(e) {
    e.preventDefault();

    $.ajax({
        url: $(this).attr('action'),
        type: "POST",
        data: $(this).serialize(),
        success: function(response) {
            $('#modalEditKelas').modal('hide');
            Swal.fire("Berhasil!", response.success, "success");
            oDataList.ajax.reload();
        },
        error: function(xhr) {
            // Cek jika ada error pesan
            if (xhr.responseJSON && xhr.responseJSON.error) {
                Swal.fire("Gagal!", xhr.responseJSON.error, "error");
            } else {
                Swal.fire("Gagal!", "Terjadi kesalahan saat memperbarui kelas!", "error");
            }
        }
    });
});

        // Hapus Kelas
        $('.kelas-list').on('click', '.btnDelete', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus kelas ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ url('/kelas/delete') }}/" + id,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire("Berhasil!", "Kelas berhasil dihapus.", "success");
                            oDataList.ajax.reload();
                        },
                        error: function(xhr) {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus data!", "error");
                        }
                    });
                }
            });
        });
    </script>
    @endsection
