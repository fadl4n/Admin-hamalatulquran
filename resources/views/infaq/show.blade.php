@extends('admin_template')

@section('title page')
    Data Infaq
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <!-- Flex container untuk form filter dan tombol create -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form method="GET" class="form-inline">
                        <label for="tgl_infaq" class="mr-2">Tanggal:</label>
                        <input type="date" name="tgl_infaq" value="{{ request('tgl_infaq', $tanggal) }}"
                            class="form-control mr-2">
                        <button type="submit" class="btn btn-secondary">Filter</button>
                    </form>

                    <a href="{{ route('infaq.create') }}" class="btn btn-info">
                    + Tambah Data Infaq
                    </a>
                </div>

                <table class="table table-bordered table-hover w-100">
                    <thead class="bg-navy disabled">
                        <tr>
                            <th>No</th>
                            <th>Nama Kelas</th>
                            <th>Tanggal Infaq</th>
                            <th>Nominal Infaq</th>
                            <th>Aksi</th> <!-- Kolom Aksi ditambahkan -->
                        </tr>
                    </thead>
                    <tbody>
    @foreach ($kelasList as $i => $kelas)
        @php
            $infaq = $infaqs[$kelas->id_kelas] ?? null;
        @endphp
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $kelas->nama_kelas }}</td>
            <td>
                {{ $infaq ? \Carbon\Carbon::parse($infaq->tgl_infaq)->format('d-m-Y') : \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
            </td>
            <td>
                {{ $infaq ? 'Rp ' . number_format($infaq->nominal_infaq, 0, ',', '.') : 'Belum ada data infaq' }}
            </td>
            <td>
                @if ($infaq)
                    <a href="{{ route('infaq.edit', $infaq->id) }}" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                @else
                    -
                @endif
            </td>
        </tr>
    @endforeach
</tbody>

                </table>

            </div>
        </div>
    </div>
</section>
@endsection
