@extends('admin_template')
@section('css')
<!-- DataTables -->
<link rel="stylesheet" href={{
  asset("/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css")}}>
<link rel="stylesheet" href={{
  asset("/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css")}}>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
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
            <div class="table-responsive">
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
                        <a href="{{url('/manage-priviledge/edit/'.$item['id'])}}" class="btn btn-sm btn-light text-navy"><i class="fas fa-edit"></i></a>
                        <button type="button" class="btn btn-sm btn-danger text-white btnDelete" data-id="{{ $item['id'] }}" data-role="{{ $item['roleName'] }}">
                          <i class="fas fa-trash-alt"></i>
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  document.addEventListener("DOMContentLoaded", function() {
      document.querySelectorAll(".btnDelete").forEach(button => {
          button.addEventListener("click", function() {
              let id = this.getAttribute("data-id");
              let roleName = this.getAttribute("data-role");

              Swal.fire({
                  title: "Konfirmasi",
                  text: "Apakah Anda yakin ingin menghapus " + roleName + "?",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonText: "Ya, hapus!",
                  cancelButtonText: "Batal"
              }).then((result) => {
                  if (result.isConfirmed) {
                      window.location.href = "{{ url('/roles/delete/') }}/" + id;
                  }
              });
          });
      });
  });
</script>
@endsection
