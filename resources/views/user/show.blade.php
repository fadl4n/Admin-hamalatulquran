@extends('admin_template')

@section('css')
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('title page')
    Daftar Pengguna
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @include('component.error_bar')
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>Daftar Pengguna</h5>
                                <a href="{{ url('users/create') }}" class="btn btn-info">+ Tambah Pengguna</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover user-list w-100">
                                    <thead class="bg-navy disabled">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Peran</th>
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


@section('script')
<script>
    var oDataList = $('.user-list').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: false,
        ajax: {
            url: _baseURL + '/users/fn-get-data',
            data: function(d) {

            }
        },
        "fnDrawCallback": function(oSettings) {

        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name'},
            { data: 'email', name: 'email'},
            { data: 'role_table', name: 'role_table', orderable: false, searchable: false},
            { data: 'action', searchable: false, orderable: false, width: '25%' },
        ],
        "order": [
            0
        ]
    });

    $('.user-list').on('click', '.btnDelete', function () {
        $('#modal-delete .deleteUrl').attr('href', _baseURL + '/users/delete/'+$(this).attr('data-id'));

        // SHOW MODAL
        $('#modal-delete').modal('show');
    });


</script>

@endsection
