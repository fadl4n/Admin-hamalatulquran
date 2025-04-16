<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CMS</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('/admin-lte/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('/admin-lte/dist/css/adminlte.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/admin-lte/plugins/select2/css/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/admin-lte/plugins/daterangepicker/daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('/admin-lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/app.css') }}">

  @yield('css')
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  {{-- Header --}}
  @include('layout.header')

  {{-- Sidebar --}}
  @include('layout.sidebar')

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">@yield('title page')</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
              <li class="breadcrumb-item active">@yield('title page')</li>
            </ol>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="content">
      <div class="container-fluid">
        @yield('content')
      </div>
    </div>
  </div>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <div class="p-3">
      <a href="{{ url('/logout') }}" class="d-block text-white">Logout</a>
    </div>
  </aside>
{{--
  {{-- Footer (HANYA SATU) --}}
  {{-- @include('layout.footer') --}} 

</div>

{{-- Delete Confirmation Modal --}}
@include('component.delete_modal')

<!-- Scripts -->
<script src="{{ asset('/admin-lte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('/admin-lte/plugins/popper/popper.min.js') }}"></script>
<script src="{{ asset('/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('/admin-lte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('/admin-lte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('/admin-lte/plugins/select2/js/select2.full.min.js') }}"></script>
<script src="{{ asset('/admin-lte/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('/admin-lte/plugins/daterangepicker/daterangepicker.js') }}"></script>
<script src="{{ asset('/admin-lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>

<script>
  var _baseURL = "{{ url('') }}";
  var _assetURL = "{{ asset('') }}";

  $(function () {
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>

@yield('script')

</body>
</html>
