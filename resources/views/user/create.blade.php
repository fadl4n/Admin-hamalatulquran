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
Add User
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
                    <form action="{{url('/users/store')}}" method="post" class="form-horizontal">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="role_id" class="col-sm-2 col-form-label">Role</label>
                                <div class="col-sm-10">
                                    <select name="role_id" id="role_id" class="form-control select-role" style="width: 100%;" required>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="name" class="col-sm-2 col-form-label">Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" placeholder="Name" name="name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" id="email" placeholder="Email" name="email" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-sm-2 col-form-label">Password</label>
                                <div class="col-sm-10">
                                    <input type="password" minlength="6" class="form-control" id="password" name="password" required/>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="passwordConfirmation" class="col-sm-2 col-form-label">Confirm Password</label>
                                <div class="col-sm-10">
                                    <input type="password" minlength="6" class="form-control" id="passwordConfirmation" name="passwordConfirmation" required/>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-info mr-2">Add</button>
                                <a href="{{ url('/users') }}" class="btn btn-default">Cancel</a>
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

@section('script')
<script>
    $(".select-role").select2({
        placeholder: 'Select Role',
        ajax: {
            url: _baseURL + '/master/role',
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    term: params.term || '',
                    page: params.page || 1
                }
            },
            processResults: function (data, params) {
                var page = params.page || 1;
                return {
                    results: $.map(data.results, function (item) { return {id: item.id, text: item.name}}),
                    pagination: {
                    // THE `10` SHOULD BE SAME AS `$resultCount FROM PHP, it is the number of records to fetch from table` 
                        more: false
                    }
                };
            },
            cache: true
        },
        search: true,
        theme: 'bootstrap4'
    });
</script>
@endsection