@extends('admin_template')
@section('css')
    <!-- DataTables -->
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('title page')
    Menu
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>Daftar Menu</h5>
                                <a href="{{ url('menu/create') }}" class="btn btn-info">+ Tambah Menu</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover menu-list w-100">
                                    <thead class="bg-navy disabled">
                                        <tr>
                                            <th>Menu Group</th>
                                            <th>Name</th>
                                            <th>URL</th>
                                            <th>Sequence</th>
                                            <th>Action</th>
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
    var oDataList = $('.menu-list').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: false,
        ajax: {
            url: _baseURL + '/menu/fn-get-data',
            data: function(d) {

            }
        },
        "fnDrawCallback": function(oSettings) {

        },
        columns: [
            { data: 'group_menu.name', name: 'group_menu.name', orderable: false, searchable: false},
            { data: 'name', name: 'name'},
            { data: 'url', name: 'url'},
            // { data: 'description', name: 'description'},
            { data: 'sequence', name: 'sequence'},
            // { data: 'status_table', name: 'status_table', orderable: false, searchable: false},
            { data: 'action', searchable: false, orderable: false, width: '25%' },
        ],
        "order": [
            [0, "asc"]
        ]
    });

    $('.menu-list').on('click', '.btnDelete', function () {
        $('#modal-delete .deleteUrl').attr('href', _baseURL + '/menu/delete/'+$(this).attr('data-id'));

        // SHOW MODAL
        $('#modal-delete').modal('show');
    });
</script>

@endsection
