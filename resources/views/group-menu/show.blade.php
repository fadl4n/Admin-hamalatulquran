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
    Group Menu
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-2">
                                <h5>Group Menu</h5>
                                <a href="{{ url('group-menu/create') }}" class="btn btn-info">+ Add Group Menu</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover group-menu-list w-100">
                                    <thead class="bg-navy disabled">
                                        <tr>
                                            <th>Name</th>
                                            <th>Sequence</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
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
    var oDataList = $('.group-menu-list').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: false,
        ajax: {
            url: _baseURL + '/group-menu/fn-get-data',
            data: function(d) {

            }
        },
        "fnDrawCallback": function(oSettings) {

        },
        columns: [
            { data: 'name', name: 'name'},
            { data: 'sequence', name: 'sequence'},
            // { data: 'status_table', name: 'status_table', orderable: false, searchable: false},
            { data: 'action', searchable: false, orderable: false, width: '25%' },
        ],
        "order": [
            [0, "asc"]
        ]
    });

    $('.group-menu-list').on('click', '.btnDelete', function () {
        $('#modal-delete .deleteUrl').attr('href', _baseURL + '/group-menu/delete/'+$(this).attr('data-id'));

        // SHOW MODAL
        $('#modal-delete').modal('show');
    });
</script>

@endsection
