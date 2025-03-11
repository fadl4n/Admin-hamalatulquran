@extends('admin_template')

@section('title page', 'Detail Target')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h5> @isset($targets){{ $targets->first()->first()->santri->nama }}@endisset</h5>
                    <div class="card">
                        <div class="card-body">
                            @if($targets->isEmpty())
                                <p class="text-center text-danger">Data tidak ditemukan.</p>
                            @else
                                <table class="table table-bordered">
                                    <thead class="bg-navy disabled">
                                        <tr>
                                            <th>Nama Surat</th>
                                            <th>Ayat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($targets as $groupKey => $group)
                                            @php
                                                $firstTarget = $group->first();
                                            @endphp

                                            @foreach ($group as $target)
                                                <tr>
                                                    <td>{{ optional($target->surat)->nama_surat ?? 'Tidak Ditemukan' }}</td>
                                                    <td>{{ $target->jumlah_ayat_target_awal ?? '0' }} - {{ $target->jumlah_ayat_target ?? '0' }}</td>
                                                    <td class="text-center">
                                                        <a href="{{ route('target.edit', $target->id_target) }}" class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button class="btn btn-danger btn-sm btnDelete" data-id="{{ $target->id_target }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            <div class="text-right mt-3">
                                <a href="{{ route('target.index') }}" class="btn btn-secondary">Kembali</a>
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
    $('.btnDelete').on('click', function (e) {
        e.preventDefault();

        var id_target = $(this).data('id');

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus target ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/target/delete/" + id_target,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        Swal.fire('Berhasil!', 'Target berhasil dihapus!', 'success');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
