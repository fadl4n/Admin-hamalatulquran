@extends('admin_template')
@section('css')
<!-- DataTables -->
<link rel="stylesheet" href={{
  asset("/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css")}}>
<link rel="stylesheet" href={{
  asset("/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}>
<link rel="stylesheet" href={{
  asset("/bower_components/admin-lte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css")}}>
@endsection

@section('title page')
Priviledge
@endsection

@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        @include('component.error_bar')
        <div class="card">
          <!-- /.card-header -->
          <div class="card-body">
            <div class="d-flex justify-content-end pb-2">
              {{-- <a href="{{url('/role/create')}}" class="btn btn-info mr-2 bg-navy color-palette"><i class="fas fa-user-plus"></i></a> --}}
              {{-- <a href="{{url('/roles/create')}}" class="btn btn-info mr-2 bg-info color-palette"><p style="font-size: 13px; margin-bottom: 0px;">Add Priviledge +</p></a> --}}
            </div>
            <table id="example2" class="table table-bordered table-hover">
              <thead class="bg-navy disabled">
                <tr>
                  <th>No</th>
                  <th>Roles Name</th>
                  <th>Description</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($roles as $index => $item)
                <tr>
                  <td>{{ $index + 1 }}</td>
                  <td>{{ $item['roleName'] }}</td>
                  <td>{{ $item['description'] }}</td>
                  <td>
                    {{-- <a href="{{url('/role/edit/{{ $item->id }}')}}" class="text-black"><i class="fas fa-edit"></i></a> --}}
                    <a href="{{url('/manage-priviledge/edit/'.$item['id'])}}" class="btn btn-sm btn-light text-navy"><i class="fas fa-edit"></i></a>
                    <a href="#" data-toggle="modal" data-target="#modal-default" data-target-id="{{ $item['id'] }}" class="btn btn-sm btn-danger text-white">
                      <i class="fas fa-trash-alt"></i>
                    </a>
                  </td>
                </tr>
                @endforeach

              </tbody>

            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <p>Are you sure you want to delete {{ $item['roleName'] }} ?</p>
        </div>
        <div class="modal-footer justify-content-end">
          <a href="{{url('/roles/delete/'.$item['id'])}}" class="btn btn-danger">Delete</a>
          <button type="button" class="btn btn-defaut" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection
