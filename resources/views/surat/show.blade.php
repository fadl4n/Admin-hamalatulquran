@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection

@section('title page')
    Daftar Surat
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end pb-2">
                                <a href="{{ route('surat.create') }}" class="btn btn-info">+ Tambah Surat</a>
                            </div>
                            <table class="table table-bordered table-hover surat-list">
                                <thead class="bg-navy disabled">
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Surat</th>
                                        <th>Jumlah Ayat</th>
                                        <th>Juz</th>
                                        <th>Deskripsi</th>
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
    var oDataList = $('.surat-list').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('surat.index') }}",
        },
        columns: [
            { data: 'id_surat', name: 'id_surat' },
            { data: 'nama_surat', name: 'nama_surat' },
            { data: 'jumlah_ayat', name: 'jumlah_ayat' },
            { data: 'juz', name: 'juz' },
            { data: 'deskripsi', name: 'deskripsi', render: function(data) {
                return data ? data : '-';
            }},
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
    });

    $('.surat-list').on('click', '.btnDelete', function () {
        let id = $(this).data('id');
        if (confirm("Apakah Anda yakin ingin menghapus surat ini?")) {
            $.ajax({
                url: "{{ url('/surat') }}/" + id,
                type: "DELETE",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
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
