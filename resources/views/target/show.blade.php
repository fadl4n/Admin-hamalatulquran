@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
@endsection

@section('title page', 'Daftar Target')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end pb-2">
                                <a href="{{ route('target.create') }}" class="btn btn-info">+ Tambah Target</a>
                            </div>
                            <table class="table table-bordered table-hover target-list" id="targetTable">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>NISN</th>
                                        <th>Nama Kelas</th>
                                        <th>Nama Pengajar</th>
                                        <th>Target</th>
                                        <th>Tanggal Awal</th>
                                        <th>Tanggal Target</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $no = 1; @endphp
                                    @foreach($targets as $groupKey => $group)
                                    @php $first = $group->first(); @endphp
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $first->santri->nama }}</td>
                                        <td>{{ $first->santri->nisn }}</td>
                                        <td>{{ $first->kelas->nama_kelas }}</td>
                                        <td>{{ $first->pengajar->nama }}</td>
                                        <td>{{'Target '}} {{$first->id_group}}</td>
                                        <td>{{ $first->tgl_mulai }}</td>
                                        <td>{{ $first->tgl_target }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('target.detail', ['id_group' => $first->id_group]) }}?id_santri={{ $first->id_santri }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button class="btn btn-danger btn-sm btnDelete"
                                                    data-id_santri="{{ $first->santri->id_santri }}"
                                                    data-id_group="{{ $first->id_group }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#targetTable').DataTable({
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });

    $('#searchBox').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('.btnDelete').on('click', function (e) {
        e.preventDefault();

        var id_santri = $(this).data('id_santri');
        var id_group = $(this).data('id_group');

        Swal.fire({
            title: "Confirmation",
            text: "Are you sure you want to delete this target?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/target/delete/" + id_santri + "/" + id_group,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        Swal.fire('Success!', 'Target has been deleted!', 'success');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Failed!', 'There was an error while deleting the data', 'error');
                    }
                });
            }
        });
    });
});
    
</script>
@endsection
