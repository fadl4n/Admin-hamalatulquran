@extends('admin_template')

@section('title page', 'Detail Setoran')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        table.table {
            background-color: white;
            color: black;
        }
        table.table, table.table th, table.table td {
            border: 1px solid #ccc;
        }
        thead.bg-navy {
            background-color: #f4f6f9 !important;
            color: black !important;
        }
        td.keterangan {
            max-width: 250px;
            white-space: normal !important;
            word-break: break-word;
            vertical-align: top;
        }
    </style>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <!-- Informasi Santri -->
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Detail Setoran Santri</h4>
                            <a href="{{ route('setoran.index') }}" class="btn btn-outline-secondary">Kembali</a>
                        </div>

                        <!-- Data Santri -->
                        <table class="table table-striped">
                            <tr>
                                <th width="30%">Nama</th>
                                <td>{{ optional($setorans->first()->santri)->nama ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>NISN</th>
                                <td>{{ optional($setorans->first()->santri)->nisn ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Kelas</th>
                                <td>{{ optional($setorans->first()->santri?->kelas)->nama_kelas ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Setoran -->
        @php
            $setoransBySurat = $setorans->groupBy('id_surat');
        @endphp

        @foreach ($setoransBySurat as $idSurat => $setoranGroup)
        <div class="row mt-4">
            <div class="col-md-10 offset-md-1">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mb-3">Surah: {{ optional($setoranGroup->first()->surat)->nama_surat ?? 'Tidak Diketahui' }}</h5>

                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>Ayat</th>
                                        <th>Nilai</th>
                                        <th>Pengajar</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($setoranGroup as $setoran)
                                        <tr>
                                            <td>
                                                {{ $setoran->jumlah_ayat_start == $setoran->jumlah_ayat_end
                                                    ? $setoran->jumlah_ayat_start
                                                    : $setoran->jumlah_ayat_start . ' - ' . $setoran->jumlah_ayat_end }}
                                            </td>
                                            <td>{{ number_format($setoran->nilai) }}</td>
                                            <td>{{ $setoran->pengajar->nama }}</td>
                                            <td>{{ $setoran->tgl_setoran }}</td>
                                            <td class="keterangan" title="{{ $setoran->keterangan }}">{{ $setoran->keterangan }}</td>
                                            <td class="text-center">
                                                <a href="{{ url('setoran/edit/' . $setoran->id_setoran) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-danger btn-sm btnDelete" data-id_setoran="{{ $setoran->id_setoran }}">
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
        @endforeach

    </div>
</section>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).on('click', '.btnDelete', function () {
        let id_setoran = $(this).data('id_setoran');
        let row = $(this).closest('tr');

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus setoran?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('setoran.destroy', ':idSetoran') }}".replace(':idSetoran', id_setoran),
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire("Berhasil!", response.success, "success");
                        row.remove();
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
