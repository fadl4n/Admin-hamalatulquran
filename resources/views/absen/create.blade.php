@extends('admin_template')

@section('title page')
    Tambah Absensi - {{ $kelas->nama_kelas }}
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @elseif(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('absen.store') }}">
                    @csrf

                    <!-- Hidden input kelas -->
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id_kelas }}">

                    <!-- Input tanggal di paling atas -->
                    <div class="form-group">
                        <label for="tgl_absen">Tanggal Absensi</label>
                        <input type="date" id="tgl_absen" name="tgl_absen" class="form-control" value="{{ old('tgl_absen', date('Y-m-d')) }}" required>
                    </div>

                    <!-- Daftar santri -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Nama Santri</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($santris as $santri)
                                <tr>
                                    <td>
                                        {{ $santri->nama }}
                                        <input type="hidden" name="santri_id[]" value="{{ $santri->id_santri }}">
                                    </td>
                                    <td>
                                        <select name="status[]" class="form-control" required>
                                            <option value="">-- Pilih Keterangan --</option>
                                            <option value="1">Hadir</option>
                                            <option value="2">Sakit</option>
                                            <option value="3">Izin</option>
                                            <option value="4">Alfa</option>
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2">Tidak ada santri dalam kelas ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <button type="submit" name="action" value="save" class="btn btn-primary">Simpan</button>
                    <a href="{{ route('absen.index') }}" class="btn btn-secondary">Batal</a>
                </form>

            </div>
        </div>
    </div>
</section>
@endsection
