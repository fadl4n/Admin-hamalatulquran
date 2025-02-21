@extends('admin_template')

@section('css')
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/bower_components/admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
@endsection

@section('title page')
    Histori Santri
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="filterSantri">Pilih Nama Santri:</label>
                                    <select id="filterSantri" class="form-control">
                                        <option value="">Semua Santri</option>
                                        @foreach ($santris as $santri)
                                            <option value="{{ $santri->id_santri }}">{{ $santri->nama }} | {{$santri->nisn}} </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <table class="table table-bordered table-hover histori-list">
                                <thead class="bg-navy text-white">
                                    <tr>
                                        <th>Nama Santri</th>
                                        <th>Kelas</th>
                                        <th>Nama Surat</th>
                                        <th>Jumlah Ayat</th>
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
    var oDataList = $('.histori-list').DataTable({
        processing: true,
        serverSide: true,
        searching: false,  // Nonaktifkan fitur pencarian
        ajax: {
            url: "{{ route('histori.fnGetData') }}",
            data: function (d) {
                d.id_santri = $('#filterSantri').val();
            }
        },
        columns: [
            { data: 'nama', name: 'nama' },
            { data: 'kelas', name: 'kelas' },

            { data: 'nama_surat', name: 'nama_surat' },
            { data: 'jumlah_ayat', name: 'jumlah_ayat' }
        ]
    });

    // Event ketika filter santri diubah
    $('#filterSantri').change(function () {
        oDataList.ajax.reload();
    });
</script>
@endsection
