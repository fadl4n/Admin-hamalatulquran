@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
@endsection

@section('title page')
    Daftar Keluarga
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end pb-2">
                                <a href="{{ route('keluarga.create') }}" class="btn btn-info">+ Tambah Keluarga</a>
                            </div>
                            <table class="table table-bordered table-hover keluarga-list">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Santri</th>
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
    var oDataList = $('.keluarga-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ url('/keluarga/fn-get-data') }}",
        },
        columns: [
            { data: 'id', name: 'id' },
            { data: 'nama_santri', name: 'nama_santri', render: function(data) {
                return data ? data : '-';
            }},
            { data: 'action', name: 'action', orderable: false, searchable: false, render: function(data, type, row) {
                return `
                    <a href="{{ url('/keluarga/show/') }}/${row.id}" class="btn btn-primary btn-sm" title="Preview">
                        <i class="fa fa-eye"></i>
                    </a>
                    <button class="btn btn-danger btn-sm btnDelete" data-id="${row.id}" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>
                `;
            }}
        ],
    });

    $('.keluarga-list').on('click', '.btnDelete', function () {
        let id = $(this).data('id');
        if (confirm("Apakah Anda yakin ingin menghapus keluarga ini?")) {
            $.ajax({
                url: "{{ url('/keluarga/delete') }}/" + id,
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
