@extends('admin_template')

@section('title page', 'Daftar Nilai Santri')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered" id="santriTable">
                    <thead class="bg-navy disabled">
                        <tr>
                            <th>Nama</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        var table = $('#santriTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('nilai.fn-get-data') }}", // URL untuk mendapatkan data
                data: function(d) {
                    d.search = $('input[type="search"]').val();  // Mengirimkan input search dari DataTable ke server
                }
            },
            columns: [
                { data: 'nama', name: 'nama' },
                { data: 'nisn', name: 'nisn' },
                { data: 'kelas', name: 'kelas' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                { className: "text-center", targets: [3] } // Menyenter aksi
            ],

        });
    });
</script>
@endsection
