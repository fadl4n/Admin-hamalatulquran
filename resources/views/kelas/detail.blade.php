@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('title page')
    Santri Kelas {{ $kelas->nama_kelas }}
@endsection


@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="text-center">Daftar Santri {{ $kelas->nama_kelas }}</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover santri-detail-list w-100 text-center">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>Nama</th>
                                        <th>NISN</th>
                                        <th>Aksi</th>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    var kelasId = "{{ $kelas->id_kelas }}"; // ambil id kelas dari controller

    var oDataDetailList = $('.santri-detail-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('/kelas/fn-get-santri') }}",
            data: function(d) {
                d.kelas_id = kelasId; // pastikan dikirim sebagai parameter ajax
            }
        },
        columns: [
            { data: 'nama', name: 'nama' },
            { data: 'nisn', name: 'nisn' },
            {
                data: 'action',  // harus sama dengan nama kolom di controller
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],
        order: [[0, 'asc']]
    });
</script>

@endsection
