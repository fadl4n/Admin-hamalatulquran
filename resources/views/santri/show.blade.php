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
                            <div class="d-flex justify-content-end pb-2">
                                <a href="{{ url('santri/create') }}" class="btn btn-info">+ Tambah Santri</a>
                            </div>
                            <table class="table table-bordered table-hover santri-list">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>Nama</th>
                                        <th>NISN</th>
                                        <th>Tanggal Lahir</th>
                                        <th>Alamat</th>
                                        <th>Angkatan</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Status</th>
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
            { data: 'tgl_lahir', name: 'tgl_lahir' },
            { data: 'alamat', name: 'alamat' },
            { data: 'angkatan', name: 'angkatan' },
            { data: 'jenis_kelamin', name: 'jenis_kelamin', render: function(data) {
                return data == 1 ? 'Laki-laki' : 'Perempuan';
            }},
            { data: 'status', name: 'status', render: function(data) {
                return data == 1 ? 'Aktif' : 'Nonaktif';
            }},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    });

    $('.santri-list').on('click', '.btnDelete', function () {
    let id = $(this).data('id');
    if (confirm("Apakah Anda yakin ingin menghapus santri ini?")) {
        $.ajax({
            url: "{{ url('/santri/delete') }}/" + id,
            type: "DELETE",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(response) {
                alert(response.success);
                oDataList.ajax.reload(); // Reload DataTables
            },
            error: function(xhr) {
                alert("Terjadi kesalahan! " + xhr.responseText);
            }
        });
    }
});

</script>
@endsection
