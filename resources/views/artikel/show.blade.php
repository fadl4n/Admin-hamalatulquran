@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
<script>
    var oDataList = $('.artikel-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('/artikel/fn-get-data') }}",
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'judul', name: 'judul' },
            { data: 'deskripsi', name: 'deskripsi', render: function(data) {
                return data ? data.substring(0, 100 )  : '-';
            }},
            { data: 'gambar', name: 'gambar', orderable: false, searchable: false, render: function(data) {
                return data ? '<img src="' + data + '" width="100" alt="Gambar Artikel">' : '-';
            }},
            { data: 'expired_at', name: 'expired_at', render: function(data) {
                return data ? moment(data).format('YYYY-MM-DD') : '-';
            }},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    });

    $('.artikel-list').on('click', '.btnDelete', function () {
        let id = $(this).data('id');
        if (confirm("Apakah Anda yakin ingin menghapus artikel ini?")) {
            $.ajax({
                url: "{{ url('/artikel/delete') }}/" + id,
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    alert(response.success);
                    oDataList.ajax.reload();
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan! " + xhr.responseText);
                }
            });
        }
    });
</script>
@endsection
