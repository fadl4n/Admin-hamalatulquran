@extends('admin_template')

@section('css')
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

    <style>
        .status-proses {
            background-color: #f7d24c;
            color: white;
        }

        .status-selesai {
            background-color: #28a745;
            color: white;
        }
    </style>
@endsection

@section('title page', 'Daftar Setoran')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between pb-3">
                                <h5>Daftar Setoran</h5>
                                <a href="{{ route('setoran.create') }}" class="btn btn-info">+ Tambah Setoran</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover setoran-list w-100">
                                    <thead class="bg-navy disabled">
                                        <tr>
                                            <th>No</th>
                                            <th>Santri</th>
                                            <th>Kelas</th>
                                            <th>Target</th>
                                            <th>Status</th>
                                            <th>Persentase</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($setoransGrouped as $groupKey => $setoranGroup)
                                            @php
                                                [$idSantri, $idtarget] = explode('-', $groupKey);
                                                $santri = \App\Models\Santri::find($idSantri);
                                                $targetsSantri = \App\Models\Target::where('id_santri', $idSantri)
                                                    ->orderBy('id_target')
                                                    ->pluck('id_target') // ambil hanya id_target saja
                                                    ->values(); // reset index ke 0,1,2...

                                                // cari posisi idtarget di list target milik santri
                                                $targetIndex = $targetsSantri->search($idtarget);

                                                // nomor target mulai dari 1 jika ditemukan, kalau tidak tampilkan '-'
                                                $targetNumber = $targetIndex !== false ? $targetIndex + 1 : '-';
                                                $kelasList = $setoranGroup
                                                    ->pluck('kelas.nama_kelas')
                                                    ->unique()
                                                    ->implode(', ');
                                                $statusList = $setoranGroup
                                                    ->pluck('status')
                                                    ->unique()
                                                    ->map(fn($status) => $status == 1 ? 'Selesai' : 'Proses')
                                                    ->values()
                                                    ->all();
                                                $averagePersentase = $setoranGroup->pluck('persentase')->avg();
                                                $status = $averagePersentase >= 100 ? 'Selesai' : 'Proses';
                                            @endphp
                                            <tr>
                                                <td class="text-center">{{ $loop->iteration }}</td>
                                                <td>{{ $santri->nama }} | {{ $santri->nisn }}</td>
                                                <td class="text-center">{{ $kelasList }}</td>
                                                <td class="text-center">Target {{ $targetNumber }}</td>
                                                <td class="text-center">
                                                    <span
                                                        class="badge {{ $status == 'Proses' ? 'bg-warning' : 'bg-success' }}">{{ $status }}</span>
                                                </td>
                                                <td class="text-center">{{ round($averagePersentase, 2) }} %</td>
                                                <td class="text-center">
                                                    <a href="{{ route('setoran.show', $groupKey) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm btn-danger btnDelete"
                                                        data-id_target="{{ $idtarget }}"
                                                        data-id_santri="{{ $idSantri }}">
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
    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            var oDataList = $('.setoran-list').DataTable({
                processing: true,
                serverSide: false,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                    className: "text-center",
                    target: [0, 2, 3, 5, 6]
                }]
            });

            // Fitur pencarian DataTable
            $('#searchBox').on('keyup', function() {
                oDataList.search(this.value).draw();
            });

            // Event handler untuk tombol hapus
            $('.setoran-list').on('click', '.btnDelete', function() {
                let id_target = $(this).data('id_target'); // Ambil id_target dari tombol delete
                let id_santri = $(this).data('id_santri'); // Ambil id_santri dari tombol delete
                let row = $(this).closest('tr'); // Ambil baris terkait

                if (!id_target || !id_santri) {
                    Swal.fire("Error", "ID Group atau ID Santri tidak ditemukan!", "error");
                    return;
                }

                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menghapus semua setoran?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Ya, hapus!",
                    cancelButtonText: "Batal"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('setoran.destroyByTarget', ['id_santri' => ':id_santri', 'idtarget' => ':id_target']) }}"
                                .replace(':id_santri', id_santri).replace(':id_target',
                                    id_target),
                            type: "DELETE",
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                Swal.fire("Berhasil!", response.success, "success")
                                    .then(() => {
                                        oDataList.row(row).remove()
                                    .draw(); // Hapus baris dari DataTable
                                    });
                            },
                            error: function(xhr) {
                                Swal.fire("Gagal!",
                                    "Terjadi kesalahan saat menghapus data!",
                                    "error");
                            }
                        });
                    }
                });
            });
        });
    </script>

@endsection
