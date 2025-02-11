@extends('admin_template')

@section('title page')
    Edit Santri
@endsection

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-end pb-2">
                                <a href="{{ url('santri') }}" class="btn btn-secondary">Kembali</a>
                            </div>
                            <form action="{{ route('santri.update', $santri->id_santri) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="form-group">
                                    <label>ID Santri</label>
                                    <input type="text" class="form-control" value="{{ $santri->id_santri }}" disabled>
                                </div>
                                <div class="form-group">
                                    <label>Nama</label>
                                    <input type="text" name="nama" class="form-control" value="{{ $santri->nama }}" required>
                                </div>
                                <div class="form-group">
                                    <label>NISN</label>
                                    <input type="number" name="nisn" class="form-control" value="{{ $santri->nisn }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="tgl_lahir" class="form-control" value="{{ $santri->tgl_lahir }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control" value="{{ $santri->alamat }}">
                                </div>
                                <div class="form-group">
                                    <label>Angkatan</label>
                                    <input type="text" name="angkatan" class="form-control" value="{{ $santri->angkatan }}">
                                </div>
                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="id_kelas" class="form-control">
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelas as $k)
                                            <option value="{{ $k->id_kelas }}" {{ $santri->id_kelas == $k->id_kelas ? 'selected' : '' }}>
                                                {{ $k->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="1" {{ $santri->jenis_kelamin == 1 ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="2" {{ $santri->jenis_kelamin == 2 ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="1" {{ $santri->status == 1 ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ $santri->status == 0 ? 'selected' : '' }}>Nonaktif</option>
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-success">Simpan</button>
                                <a href="{{ url('santri') }}" class="btn btn-secondary">Batal</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
