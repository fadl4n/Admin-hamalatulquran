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
Detail User
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
          @csrf
          <div class="card-body">
            <legend>User Detail</legend>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Name</label>
              <div class="col-sm-10">
                <p style="padding-top: 7px; margin-bottom: 0px">{{ $data->name }}</p>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Email</label>
              <div class="col-sm-10">
                <p style="padding-top: 7px; margin-bottom: 0px">{{ $data->email }}</p>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Role</label>
              <div class="col-sm-10">
                <p style="padding-top: 7px; margin-bottom: 0px">{{ isset($data->role)?$data->role->name:'' }}</p>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Created At</label>
              <div class="col-sm-10">
                <p style="padding-top: 7px; margin-bottom: 0px">{{ date('d-m-Y H:i:s', strtotime($data->created_at)) }}</p>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-2 col-form-label">Updated At</label>
              <div class="col-sm-10">
                <p style="padding-top: 7px; margin-bottom: 0px">{{ date('d-m-Y H:i:s', strtotime($data->updated_at)) }}</p>
              </div>
            </div>         
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
            <div class="d-flex justify-content-end">
              <a href="{{ url('/users') }}" class="btn btn-default">Cancel</a>
            </div>

          </div>
        </div>
        <!-- /.card -->

      </div>
      <!--/.col (left) -->
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</section>
@endsection