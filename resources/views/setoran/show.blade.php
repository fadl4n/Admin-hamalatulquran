@extends('admin_template')

@section('title page', 'Daftar Setoran')

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
                            <thead class="bg-navy text-white">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Santri</th>
                                    <th>Kelas</th>
                                    <th>Tanggal Setoran</th>
                                    <th>Nama Pengajar</th>
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
                                        <td>{{ $setoran->pengajar ? $setoran->pengajar->nama : ' ' }}</td>
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

@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $('.setoran-list').on('click', '.btnDelete', function () {
        let id = $(this).data('id');

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus setoran ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/setoran/delete') }}/" + id,
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire("Berhasil!", response.success, "success");
                        location.reload(); // Reload halaman setelah sukses
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
