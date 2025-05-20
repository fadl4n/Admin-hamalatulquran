@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection

@section('title page')
    Daftar Santri
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>Daftar Santri</h5>
                                <a href="{{ url('santri/create') }}" class="btn btn-info">+ Tambah Santri</a>
                            </div>
                            <div class="table-responsive">
                            <table class="table table-bordered table-hover santri-list w-100 text-center">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>Nama</th>
                                        <th>NISN</th>
                                        <th>Nama Kelas</th>
                                        <th>Angkatan</th>
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
@endsection

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
  var oDataList = $('.santri-list').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: "{{ url('/santri/fn-get-data') }}",
    },
    columns: [
        { data: 'nama', name: 'nama' },
        { data: 'nisn', name: 'nisn' },
        { data: 'nama_kelas', name: 'nama_kelas' },
        { data: 'angkatan', name: 'angkatan' },
        { data: 'action', name: 'action', orderable: false, searchable: false }
    ],
    order: [[4, 'asc'], [0, 'asc']] // Mengurutkan berdasarkan id_kelas (indeks 4) dan nama (indeks 0)
});


    $('.santri-list').on('click', '.btnDelete', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda ingin menghapus santri ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/santri/delete') }}/" + id,
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire("Berhasil!", response.success, "success");
                        oDataList.ajax.reload();
                    },
                    error: function(xhr) {
                        Swal.fire("Gagal!", "Terjadi kesalahan!", "error");
                    }
                });
            }
        });
    });
</script>
@endsection
