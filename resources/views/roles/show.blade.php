@extends('admin_template')
@section('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
@endsection

@section('title page')
Roles
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
              <h5>Daftar Roles</h5>
              <a href="{{ url('/roles/create') }}" class="btn btn-info">+ Add Role</a>
            </div>
            <div class="table-responsive">
              <table id="example2" class="table table-bordered table-hover role-list w-100">
                <thead class="bg-navy disabled">
                  <tr>
                    <th>No</th>
                    <th>Roles Name</th>
                    <th>Description</th>
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
    var oDataList = $('.role-list').DataTable({
        processing: true,
        serverSide: true,
        lengthChange: false,
        ajax: {
            url: _baseURL + '/roles/fn-get-data',
            data: function(d) {

            }
        },
        "fnDrawCallback": function(oSettings) {

        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name'},
            { data: 'description', name: 'description'},
            { data: 'action', searchable: false, orderable: false, width: '25%' },
        ],
        "order": [
            0
        ]
    });

    $('.role-list').on('click', '.btnDelete', function () {
        $('#modal-delete .deleteUrl').attr('href', _baseURL + '/roles/delete/'+$(this).attr('data-id'));

        // SHOW MODAL
        $('#modal-delete').modal('show');
    });


</script>

@endsection
