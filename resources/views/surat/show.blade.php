@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .table-description {
            max-width: 100%;
            white-space: normal;
            word-wrap: break-word;
            word-break: break-word;
        }

        .table td {
            vertical-align: top;
        }

        th.no, td.no {
            width: 50px;
            text-align: center;
        }

        th.nama-surat, td.nama-surat {
            width: 120px;
        }

        th.jumlah-ayat, td.jumlah-ayat {
            width: 100px;
            text-align: center;
        }

        th.deskripsi, td.deskripsi {
            width: auto;
        }

        th.aksi, td.aksi {
            width: 100px;
            text-align: center;
        }
    </style>
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
                        <div class="d-flex justify-content-between pb-2">
                            <h5>Daftar Surat</h5>
                            <a href="{{ route('surat.create') }}" class="btn btn-info">+ Tambah Surat</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover surat-list w-100">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th class="no">No</th>
                                        <th class="nama-surat">Nama Surat</th>
                                        <th class="jumlah-ayat">Jumlah Ayat</th>
                                        <th class="deskripsi">Deskripsi</th>
                                        <th class="aksi">Aksi</th>
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
                    return json.data;
                },
                error: function(xhr, status, error) {
                    Swal.fire("Terjadi Kesalahan!", "Gagal memuat data!", "error");
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'no' },
                { data: 'nama_surat', name: 'nama_surat', className: 'nama-surat' },
                { data: 'jumlah_ayat', name: 'jumlah_ayat', className: 'jumlah-ayat' },
                {
                    data: 'deskripsi',
                    name: 'deskripsi',
                    className: 'deskripsi',
                    render: function(data) {
                        return `<div class="table-description">` + (data ? data : '-') + `</div>`;
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'aksi' }
            ],
            columnDefs: [
                { className: "text-center", targets: [0, 2, 4] }
            ]
        });

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
                        url: "/surat/delete/" + id,
                        type: "DELETE",
                        headers: {
                            'X-CSRF-TOKEN': "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            Swal.fire("Berhasil!", response.success, "success");
                            oDataList.ajax.reload();
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
