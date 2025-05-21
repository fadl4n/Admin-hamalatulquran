@extends('admin_template')

@section('title page', 'Detail Setoran')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <style>
        table.table {
            background-color: white;
            color: black; /* Warna teks tabel hitam */
        }
        table.table, table.table th, table.table td {
            border: 1px solid black;
        }
        thead.bg-navy {
            background-color: white !important;
            color: black !important; /* Warna teks pada thead hitam */
        }
        /* Membatasi lebar kolom keterangan dan mengizinkan wrap text */
        td.keterangan {
            max-width: 250px; /* Bisa disesuaikan */
            white-space: normal !important;
            word-break: break-word;
            vertical-align: top;
        }
    </style>
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Menampilkan informasi santri -->
                        <h4>Santri: {{ optional($setorans->first()->santri)->nama }} | NISN: {{ optional($setorans->first()->santri)->nisn }}</h4>

                        @php
                            // Mengelompokkan setoran berdasarkan id_surat
                            $setoransBySurat = $setorans->groupBy('id_surat');
                        @endphp

                        @foreach ($setoransBySurat as $idSurat => $setoranGroup)
                            @php
                                $surat = $setoranGroup->first()->surat;
                            @endphp

                            <h5 class="mt-4">Surah: {{ optional($surat)->nama_surat ?? 'Tidak Diketahui' }}</h5>

                            <!-- Tabel Setoran Detail berdasarkan id_surat -->
                            <table class="table table-bordered">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>Ayat</th>
                                        <th>Nilai</th>
                                        <th>Pengajar</th>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($setoranGroup as $setoran)
                                        <tr>
                                            <td>
                                                {{ $setoran->jumlah_ayat_start == $setoran->jumlah_ayat_end ? $setoran->jumlah_ayat_start : $setoran->jumlah_ayat_start . ' - ' . $setoran->jumlah_ayat_end }}
                                            </td>
                                            <td>{{ number_format($setoran->nilai) }}</td>
                                            <td>{{ $setoran->pengajar->nama }}</td>
                                            <td>{{ $setoran->tgl_setoran }}</td>
                                            <td class="keterangan" title="{{ $setoran->keterangan }}">
                                                {{ $setoran->keterangan }}
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ url('setoran/edit/' . $setoran->id_setoran) }}" class="btn btn-warning btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <!-- Tombol Hapus -->
                                                <button class="btn btn-danger btn-sm btnDelete" data-id_setoran="{{ $setoran->id_setoran }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endforeach

                        <!-- Tombol Kembali -->
                        <div class="mt-4">
                            <a href="{{ route('setoran.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
   $(document).on('click', '.btnDelete', function () {
    let id_setoran = $(this).data('id_setoran'); // Ambil id_setoran dari tombol delete
    let row = $(this).closest('tr'); // Ambil baris yang terkait dengan tombol delete

    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menghapus setoran ?",
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
                    row.remove(); // Hapus baris dari tabel setelah berhasil dihapus
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

@endsection
