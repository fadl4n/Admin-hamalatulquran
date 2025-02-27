@extends('admin_template')

@section('title page', 'Daftar Santri')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                {{-- Search Bar diposisikan ke kiri dengan ukuran lebih besar --}}
                <form action="{{ route('nilai.index') }}" method="GET" class="mb-3">
                    <div class="d-flex">
                        <div class="input-group" style="width: 350px;">
                            <input type="text" name="search" class="form-control py-2 fs-5" placeholder="search" value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary py-2 fs-5" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Tabel Data Santri --}}
                <table class="table table-bordered">
                    <thead class="bg-navy text-white">
                        <tr>
                            <th>Nama</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>Target </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($santris as $santri)
                            @foreach ($santri->targets->groupBy('id_group') as $idGroup => $targets)
                                <tr>
                                    <td>{{ $santri->nama }}</td>
                                    <td>{{ $santri->nisn }}</td>
                                    <td>{{ $santri->kelas->nama_kelas ?? '-' }}</td>
                                    <td>Target {{ $idGroup }}</td>
                                    <td>
                                        <a href="{{ route('nilai.show', ['idSantri' => $santri->id_santri, 'idGroup' => $idGroup]) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection
