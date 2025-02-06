@extends('admin_template')
@section('css')
    <!-- DataTables -->
    <link rel="stylesheet"
        href={{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}>
    <link rel="stylesheet"
        href={{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}>
    <link rel="stylesheet"
        href={{ asset('/bower_components/admin-lte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}>
@endsection

@section('title page')
    Edit Priviledge
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <!-- /.card-body -->
        </div>

        <div class="card">

            <div class="card-header">
                <div class="row pt-2">
                    <div class="col-sm-2">
                        Role Name
                    </div>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="name" placeholder="Name" name="name"
                            value={{ $role->roleName }}>
                    </div>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
              <form action="{{url('/manage-priviledge/update/'.  $role->id)}}" method="post" class="form-horizontal">
                @csrf
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th colspan="5">Menu Priviledge</th>
                            <th>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="all" name="all">
                                    <label class="custom-control-label" for="all"></label>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>Menu Name</th>
                            <th>View</th>
                            <th>Add</th>
                            <th>Edit</th>
                            <th>Delete</th>
                            <th>Other</th>
                        </tr>
                        @foreach ($rolePriviledges as $index => $item)
                        {{-- @if(count($item->menuItem) > 0) --}}
                            @foreach ($item->menuItem as $index => $menuitem)                           
                                <tr>
                                    <td>{{ $item->name . ' > ' . $menuitem->name }}</td>
                                    <td>
                                      @if($rolePriviledges[$loop->parent->index]->menuItem[$loop->index]->view)
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id={{ 'view_' . $menuitem->id }}
                                                name={{ 'view_' . $menuitem->id }} checked>
                                            <label class="custom-control-label" for={{ 'view_' . $menuitem->id }}></label>
                                        </div>
                                        @else 
                                        <div class="custom-control custom-switch">
                                          <input type="checkbox" class="custom-control-input" id={{ 'view_' . $menuitem->id }}
                                              name={{ 'view_' . $menuitem->id }}>
                                          <label class="custom-control-label" for={{ 'view_' . $menuitem->id }}></label>
                                      </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id={{ 'add_' . $menuitem->id }}
                                                name={{ 'add_' . $menuitem->id }}
                                                {{ $rolePriviledges[$loop->parent->index]->menuItem[$loop->index]->add ? 'checked' : '' }}>
                                            <label class="custom-control-label" for={{ 'add_' . $menuitem->id }}></label>
                                        </div>
                                    </td>
                                    <td>
                                      <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id={{ 'edit_' . $menuitem->id }}
                                            name={{ 'edit_' . $menuitem->id }}
                                            {{ $rolePriviledges[$loop->parent->index]->menuItem[$loop->index]->edit ? 'checked' : '' }}>
                                        <label class="custom-control-label" for={{ 'edit_' . $menuitem->id }}></label>
                                    </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id={{ 'delete_' . $menuitem->id }}
                                                name={{ 'delete_' . $menuitem->id }}
                                                {{ $rolePriviledges[$loop->parent->index]->menuItem[$loop->index]->delete ? 'checked' : '' }}>
                                            <label class="custom-control-label" for={{ 'delete_' . $menuitem->id }}></label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="custom-control-input" id={{ 'other_' . $menuitem->id }}
                                                name={{ 'other_' . $menuitem->id }}
                                                {{ $rolePriviledges[$loop->parent->index]->menuItem[$loop->index]->other ? 'checked' : '' }}>
                                            <label class="custom-control-label" for={{ 'other_' . $menuitem->id }}></label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            {{-- @endif --}}

                        @endforeach

                    </tbody>
                </table>

                <div class="card-footer">
                  <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-info mr-2">Update</button>
                    <a href="{{url('/manage-priviledge')}}" class="btn btn-default">Cancel</a>
                  </div>
    
                </div>
              </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        </div>
        <!-- /.col -->
        </div>
        <!-- /.row -->
        </div>
        {{-- <div class="modal fade" id="modal-default">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <p>Are you sure you want to delete {{ $item['name'] }} ?</p>
        </div>
        <div class="modal-footer justify-content-end">
          <a href="{{url('/role/delete/'.$item['id'])}}" class="btn btn-danger">Delete</a>
          <button type="button" class="btn btn-defaut" data-dismiss="modal">Close</button>
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
  </div> --}}
        <!-- /.container-fluid -->
    </section>
@endsection
