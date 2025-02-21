@extends('admin_template')

@section('title page', 'Daftar Setoran')

@section('css')
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- 🔍 SEARCH & TAMBAH -->
                            <div class="d-flex justify-content-end pb-3">
                                <a href="{{ route('setoran.create') }}" class="btn btn-info">+ Tambah Setoran</a>
                            </div>

                            <!-- 📊 TABEL SETORAN -->
                            <table class="table table-bordered table-hover setoran-list">
                                <thead class="bg-navy text-white">
                                    <tr>
                                        <th>No</th>
                                        <th>Santri</th>
                                        <th>Kelas</th>
                                        <th>Pengajar</th>
                                        <th>Target</th>
                                        <th>Status</th>
                                        <th>Persentase</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($setoransGrouped as $groupKey => $setoranGroup)
                                        @php
                                            // Memisahkan groupKey menjadi id_santri dan id_group
                                            [$idSantri, $idGroup] = explode('-', $groupKey);
                                            $santri = \App\Models\Santri::find($idSantri);
                                            $target = \App\Models\Target::where('id_group', $idGroup)->first();

                                            // Mengambil nilai unik untuk kelas, pengajar, status
                                            $kelasList = $setoranGroup->pluck('kelas.nama_kelas')->unique()->implode(', ');
                                            $pengajarList = $setoranGroup->pluck('pengajar.nama')->unique()->implode(', ');
                                            $statusList = $setoranGroup->pluck('status')->unique()->map(function ($status) {
                                                return $status == 1 ? 'Selesai' : 'Proses';
                                            })->implode(', ');

                                            // Menghitung rata-rata persentase
                                            $averagePersentase = $setoranGroup->pluck('persentase')->avg();
                                        @endphp

                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $santri->nama }} | {{ $santri->nisn }}</td>
                                            <td class="text-center">{{ $kelasList }}</td>
                                            <td>{{ $pengajarList ?: '-' }}</td>
                                            <td class="text-center">{{ 'target '.$idGroup }}</td>
                                            <td class="text-center">{{ $statusList }}</td>
                                            <td class="text-center">{{ round($averagePersentase, 2) }} %</td>
                                            <td class="text-center">
                                                <a href="{{ route('setoran.show', $groupKey) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <!-- Tombol Delete -->
                                                <button class="btn btn-sm btn-danger btnDelete" data-id_group="{{ $idGroup }}">
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
<script>
    var oDataList = $('.setoran-list').DataTable({
        processing: true,
        serverSide: false,
        order: [[0, 'asc']],
        columnDefs: [
            { className: "text-center", targets: [0, 2, 3, 5, 6, 7] }
        ]
    });

    $('#searchBox').on('keyup', function () {
        oDataList.search(this.value).draw();
    });

    $('.setoran-list').on('click', '.btnDelete', function () {
        let id_group = $(this).data('id_group'); // Ambil id_group dari tombol delete
        let row = $(this).closest('tr'); // Ambil baris yang terkait dengan tombol delete

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus semua setoran ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('setoran.destroyByTarget', ':id_group') }}".replace(':id_group', id_group), // Ganti dengan id_group yang benar
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire("Berhasil!", response.success, "success");
                        oDataList.row(row).remove().draw(); // Hapus baris dari DataTable
                    },
                    error: function(xhr) {
                        Swal.fire("Gagal!", "Terjadi kesalahan saat menghapus data!", "error");
                    }
                });
            }
        });
    });
</script>
@endsection
