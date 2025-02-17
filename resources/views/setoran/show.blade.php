@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

@endsection

@section('title page')
    Daftar Setoran
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end pb-2">
                                <a href="{{ route('setoran.create') }}" class="btn btn-info">+ Tambah Setoran</a>
                            </div>
                            <table class="table table-bordered table-hover setoran-list">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Santri</th>
                                        <th>Kelas</th>
                                        <th>Tanggal Setoran</th>
                                        <th>Surat</th>
                                        <th>Jumlah Ayat</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($setorans as $index => $setoran)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $setoran->santri->nama }} | {{ $setoran->santri->nisn }}</td>
                                            <td>{{ $setoran->kelas->nama_kelas }}</td>
                                            <td>{{ $setoran->tgl_setoran }}</td>
                                            <td>{{ $setoran->surat->nama_surat }}</td>
                                            <td>{{ $setoran->jumlah_ayat_start }} - {{ $setoran->jumlah_ayat_end }}</td>
                                            <td>{{ $setoran->status == 1 ? 'Selesai' : 'Proses' }}</td>
                                            <td>{{ $setoran->keterangan }}</td>
                                            <td class="text-center">
                                                <!-- Tombol Edit dengan Ikon -->
                                                <a href="{{ url('setoran/edit/' . $setoran->id_setoran) }}"class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <!-- Tombol Delete dengan Ikon -->
                                                <button class="btn btn-danger btn-sm btnDelete" data-id="{{ $setoran->id_setoran }}">
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
    <script src="{{ asset('/bower_components/admin-lte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.setoran-list').DataTable({
                responsive: true,
                autoWidth: false,
            });
        });
        $('.setoran-list').on('click', '.btnDelete', function () {
    let id = $(this).data('id');
    if (confirm("Apakah Anda yakin ingin menghapus setoran ini?")) {
        $.ajax({
            url: "{{ url('/setoran/delete') }}/" + id,
            type: "DELETE",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(response) {
                alert(response.success);
                $('.setoran-list').DataTable().ajax.reload(); // Reload DataTables
            },
            error: function(xhr) {
                alert("Terjadi kesalahan! " + xhr.responseText);
            }
        });
    }
});

    </script>
@endsection
