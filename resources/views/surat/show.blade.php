@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('title page')
    Daftar Surat
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end pb-2">
                                <a href="{{ route('surat.create') }}" class="btn btn-info">+ Tambah Surat</a>
                            </div>
                            <table class="table table-bordered table-hover surat-list">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Surat</th>
                                        <th>Jumlah Ayat</th>
                                        <th>Deskripsi</th>
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

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        var oDataList = $('.surat-list').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('surat.index') }}",
                type: 'GET',
                dataSrc: function (json) {
                    // Menambahkan pengecekan agar DataTables menerima data dengan benar
                    console.log("Data diterima:", json);
                    return json.data;
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                    console.error("Response:", xhr.responseText);
                    Swal.fire("Terjadi Kesalahan!", "Gagal memuat data!", "error");
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nama_surat', name: 'nama_surat' },
                { data: 'jumlah_ayat', name: 'jumlah_ayat' },
                { data: 'deskripsi', name: 'deskripsi', render: function(data) { return data ? data : '-'; }},
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                { className: "text-center", targets: [0, 2, 3, 4] }
            ]
        });

        // Konfirmasi Hapus Surat
        $('.surat-list').on('click', '.btnDelete', function () {
            let id = $(this).data('id');
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus surat ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "/surat/delete/" + id, // Sesuai dengan perubahan di web.php
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire("Berhasil!", response.success, "success");
                            oDataList.ajax.reload(); // Reload DataTable setelah hapus
                        },
                        error: function(xhr) {
                            Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus data!", "error");
                        }
                    });
                }
            });
        });
    });
</script>
@endsection
