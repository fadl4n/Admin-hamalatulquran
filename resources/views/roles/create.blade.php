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
Add Roles
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
                    <form action="{{url('/roles/store')}}" method="post" class="form-horizontal">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label for="inputEmail3" class="col-sm-2 col-form-label">Role Name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" placeholder="Role Name"
                                        name="name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="inputPassword3" class="col-sm-2 col-form-label">Description</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="description" placeholder="Description"
                                        name="description">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Priviledge</label>
                                <table class="table table-striped">
                                    <tbody>
                                        <tr>
                                            <th>Menu Name</th>
                                            <th>View</th>
                                            <th>Add</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                            <th>Other</th>
                                        </tr>
                                        @foreach ($menus as $key => $menu)                           
                                            <tr>
                                                <td>{{ $menu->groupMenu->name . ' > ' . $menu->name }}</td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id={{ 'view_' . $menu->id }}
                                                            name={{ 'view_' . $menu->id }}>
                                                        <label class="custom-control-label" for={{ 'view_' . $menu->id }}></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id={{ 'add_' . $menu->id }}
                                                            name={{ 'add_' . $menu->id }}>
                                                        <label class="custom-control-label" for={{ 'add_' . $menu->id }}></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id={{ 'edit_' . $menu->id }}
                                                        name={{ 'edit_' . $menu->id }}>
                                                    <label class="custom-control-label" for={{ 'edit_' . $menu->id }}></label>
                                                </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id={{ 'delete_' . $menu->id }}
                                                            name={{ 'delete_' . $menu->id }}>
                                                        <label class="custom-control-label" for={{ 'delete_' . $menu->id }}></label>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" id={{ 'other_' . $menu->id }}
                                                            name={{ 'other_' . $menu->id }}>
                                                        <label class="custom-control-label" for={{ 'other_' . $menu->id }}></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-info mr-2">Add</button>
                                <a href="{{ url('/roles') }}" class="btn btn-default">Cancel</a>
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