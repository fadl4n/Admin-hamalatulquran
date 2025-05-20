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
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover target-list" id="targetTable">
                                    <thead class="bg-navy disabled">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Kelas</th>
                                            <th>Pengajar</th>
                                            <th>Target</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Tanggal Target</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $no = 1; @endphp
                                        @foreach($target as $groupKey => $group)
                                        @php $first = $group->first(); @endphp
                                        <tr>
                                            <td>{{ $no++ }}</td>
                                            <td>{{ $first->santri->nama }}</td>
                                            <td>{{ $first->kelas->nama_kelas }}</td>
                                              <td>{{ $first->pengajar->nama }}</td>
    <td>
        @php
            // Ambil semua target milik santri yang terkait, urutkan berdasarkan id_target dan reset index
            $targets = $first->santri->targets->sortBy('id_target')->values();

            // Cari index target saat ini
            $targetIndex = $targets->search(function ($target) use ($first) {
                return $target->id_target === $first->id_target;
            });

            // Index mulai dari 0, jadi tambah 1 supaya tampil "Target 1"
            $targetNumber = $targetIndex !== false ? $targetIndex + 1 : '-';
        @endphp
        Target {{ $targetNumber }}
    </td>
                                            <td>{{ $first->tgl_mulai }}</td>
                                            <td>{{ $first->tgl_target }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('target.detail', ['id_target' => $first->id_target]) }}?id_santri={{ $first->id_santri }}" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                               <button class="btn btn-danger btn-sm btnDelete" data-id="{{ $first->id_target }}">
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
 var id_target = $(this).data('id');

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus target ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/target/delete/" + id_target,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Target berhasil dihapus!', 'success');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            }
        });
    });
});

</script>
@endsection
