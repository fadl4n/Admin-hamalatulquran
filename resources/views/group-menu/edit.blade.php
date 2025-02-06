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
Edit Group Menu
@endsection

@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <!-- left column -->
      <div class="col-md-12">
        @if(Session::has('error'))
        <div class="alert alert-danger">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          {{ Session::get('error') }}
        </div>
        {{ session()->forget('error') }}
        @endif
        <div class="card card-info">
          <!-- form start -->
          {{-- @foreach($role->data as $p) --}}
          <form action="{{url('/group-menu/update')}}" method="post" class="form-horizontal">
            @csrf
            <div class="card-body">
              <input type="hidden" name="id" value="{{ $data->id }}">
              <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="name" placeholder="Name" name="name" value="{{$data->name}}" required>
                </div>
              </div>
              <div class="form-group row">
                  <label for="sequence" class="col-sm-2 col-form-label">Sequence</label>
                  <div class="col-sm-10">
                      <input type="number" class="form-control" id="sequence" placeholder="Sequence" name="sequence" value="{{$data->sequence}}" required>
                  </div>
              </div>
              <div class="form-group row">
                  <label for="icon" class="col-sm-2 col-form-label">Icon</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" id="icon" placeholder="Sequence" name="icon" value="{{$data->icon}}" required>
                  </div>
              </div>
              {{-- <div class="form-group row">
                <label for="icon" class="col-sm-2 col-form-label">Status</label>
                <div class="col-sm-10">
                  <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="active" class="custom-control-input" id="active" {{ $data->active ? 'checked' : ''}}>
                    <label class="custom-control-label" for="active">Active</label>
                  </div>
                </div>
              </div> --}}
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-info mr-2">Update</button>
                <a href="{{ url('/group-menu') }}" class="btn btn-default">Cancel</a>
              </div>

            </div>
            <!-- /.card-footer -->
          </form>
        </div>
        <!-- /.card -->

      </div>
      <!--/.col (left) -->
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</section>
@endsection