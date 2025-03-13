@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endsection

@section('title page')
    Daftar Artikel
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end pb-2">
                                <a href="{{ route('artikel.create') }}" class="btn btn-info">+ Tambah Artikel</a>
                                <!-- Kolom Pencarian -->

                            </div>
                            <table class="table table-bordered table-hover artikel-list">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>No</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Gambar</th>
                                        <th>Tanggal Expired</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($articles as $key => $article)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $article->judul }}</td>
                                            <td>{{ Str::limit(strip_tags($article->deskripsi), 100) }}</td>
                                            <td>
                                                @if ($article->gambar)
                                                    <img src="{{ $article->gambar }}" width="100" alt="Gambar Artikel">
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $article->expired_at ? \Carbon\Carbon::parse($article->expired_at)->format('Y-m-d') : '-' }}</td>
                                            <td>
                                                <a href="{{ route('artikel.edit', $article->id) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                <button type="button" class="btn btn-danger btn-sm btnDelete" data-id="{{ $article->id }}">
                                                    <i class="fas fa-trash"></i>
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
    var oDataList = $('.artikel-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('/artikel/fn-get-data') }}",
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false }, // ✅ Nomor urut otomatis
            { data: 'judul', name: 'judul' },
            { data: 'deskripsi', name: 'deskripsi' },
            { data: 'gambar', name: 'gambar' },
            { data: 'expired_at', name: 'expired_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        columnDefs: [
            { className: "text-center", targets: [0,1, 2, 3, 4, 5] }, // ✅ Pusatkan teks di kolom tertentu
        ],
        searching: true // ✅ Enable search
    });

    // Menambahkan event untuk pencarian berdasarkan input
    $('#searchInput').on('keyup', function() {
        oDataList.search(this.value).draw();
    });

    $('.artikel-list').on('click', '.btnDelete', function () {
        let id = $(this).data('id');

        // SweetAlert2 Konfirmasi Hapus
        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus artikel ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('/artikel/destroy') }}/" + id,
                    type: "DELETE",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire("Berhasil!", response.success, "success");
                        // Reload DataTables
                        oDataList.ajax.reload();
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
