@extends('admin_template')

@section('title page', 'Daftar Pengajar')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-end pb-2">
                            <a href="{{ url('pengajar/create') }}" class="btn btn-info">+ Tambah Pengajar</a>
                        </div>
                        <table class="table table-bordered table-hover pengajar-list">
                            <thead class="bg-navy disabled">
                                <tr>
                                    <th>No</th>  <!-- ✅ Nomor urut otomatis -->
                                    <th>Nama</th>
                                    <th>NIP</th>
                                    <th>Email</th>
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
@endsection

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var oDataList = $('.pengajar-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('/pengajar/fn-get-data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // ✅ Menambahkan nomor urut
            { data: 'nama', name: 'nama' },
            { data: 'nip', name: 'nip' },
            { data: 'email', name: 'email' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']],
        columnDefs: [
            { className: "text-center", targets: [0, 2, 3, 4] } // ✅ Pusatkan teks di kolom tertentu
        ]
    });

    $('.pengajar-list').on('click', '.btnDelete', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus pengajar ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/pengajar/delete') }}/" + id,
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire("Berhasil!", response.success, "success");
                        oDataList.ajax.reload(); // Reload DataTables
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
