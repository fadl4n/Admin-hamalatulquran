@extends('admin_template')

@section('title page')
    Absensi - {{ $kelas->nama_kelas }}
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form method="GET" class="form-inline">
                            <label for="tgl_absen" class="mr-2">Tanggal:</label>
                            <input type="date" name="tgl_absen" value="{{ request('tgl_absen', $tanggal) }}"
                                class="form-control mr-2">
                            <button type="submit" class="btn btn-secondary">Filter</button>
                        </form>

                        <a href="{{ route('absen.create', ['id_kelas' => $kelas->id_kelas]) }}" class="btn btn-info">
                            + Tambah Absensi
                        </a>
                    </div>

                    <table class="table table-bordered table-hover w-100">
                        <thead class="bg-navy disabled">
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NISN</th>
                                <th>Tanggal Absen</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($santris as $i => $santri)
                                @php
                                    $absen = $santri->absens->first();
                                    $statusText = match ($absen->status ?? null) {
                                        1 => 'Hadir',
                                        2 => 'Sakit',
                                        3 => 'Izin',
                                        4 => 'Alfa',
                                        default => 'Belum Absen',
                                    };
                                @endphp

                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $santri->nama }}</td>
                                    <td>{{ $santri->nisn }}</td>
                                    <td>{{ $absen?->tgl_absen ? \Carbon\Carbon::parse($absen->tgl_absen)->format('d-m-Y') : '-' }}</td>
                                    <td>{{ $statusText }}</td>
                                    <td>
                                        <a href="{{ route('absen.edit', ['id' => $absen->id ?? 0]) }}"
                                            class="btn btn-sm btn-warning" {{ !$absen ? 'disabled' : '' }}
                                            title="Edit Absensi">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <a href="{{ route('absen.index') }}" class="btn btn-secondary mt-3">Kembali</a>
                </div>
            </div>
        </div>
    </section>
@endsection
