@extends('admin_template')

@section('title page')
    Edit Data Infaq
@endsection

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('infaq.update', $infaq->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="id_kelas">Nama Kelas</label>
                        <select name="id_kelas" id="id_kelas" class="form-control @error('id_kelas') is-invalid @enderror" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id_kelas }}" {{ old('id_kelas', $infaq->id_kelas) == $kelas->id_kelas ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="tgl_infaq">Tanggal Infaq</label>
                        <input type="date" name="tgl_infaq" id="tgl_infaq" class="form-control @error('tgl_infaq') is-invalid @enderror" value="{{ old('tgl_infaq', $infaq->tgl_infaq->format('Y-m-d')) }}" required>
                        @error('tgl_infaq')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nominal_infaq">Nominal Infaq</label>
                        <input type="number" name="nominal_infaq" id="nominal_infaq" class="form-control @error('nominal_infaq') is-invalid @enderror" value="{{ old('nominal_infaq', $infaq->nominal_infaq) }}" required>
                        @error('nominal_infaq')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('infaq.index') }}" class="btn btn-secondary">Batal</a>
                </form>

            </div>
        </div>
    </div>
</section>
@endsection
