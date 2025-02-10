@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection

@section('title page')
    Daftar Pengajar
@endsection

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
                                        <th>Nama</th>
                                        <th>NIP</th>
                                        <th>Email</th>
                                        <th>No. Telepon</th>
                                        <th>Jenis Kelamin</th>
                                        <th>Alamat</th>
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
    var oDataList = $('.pengajar-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('/pengajar/fn-get-data') }}",
        },
        columns: [
            { data: 'nama', name: 'nama' },
            { data: 'nip', name: 'nip' },
            { data: 'email', name: 'email' },
            { data: 'no_telp', name: 'no_telp' },
            { data: 'jenis_kelamin', name: 'jenis_kelamin', render: function(data) {
                return data == "Laki-laki" ? 'Laki-laki' : 'Perempuan';
            }},
            { data: 'alamat', name: 'alamat' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    });

    $('.pengajar-list').on('click', '.btnDelete', function () {
        let id = $(this).data('id');
        if (confirm("Apakah Anda yakin ingin menghapus pengajar ini?")) {
            $.ajax({
                url: "{{ url('/pengajar/delete') }}/" + id,
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
