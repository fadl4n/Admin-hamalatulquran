@extends('admin_template')

@section('title page', 'Edit Keluarga')

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('keluarga.update', $keluarga->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label>Santri</label>
                                <select name="id_santri" class="form-control">
                                    <option value="">Pilih Santri</option>
                                    @foreach($santris as $santri)
                                        <option value="{{ $santri->id_santri }}" {{ $keluarga->id_santri == $santri->id_santri ? 'selected' : '' }}>
                                            {{ $santri->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nama</label>
                                <input type="text" name="nama" class="form-control" value="{{ old('nama', $keluarga->nama) }}" required>
                            </div>

                            <div class="form-group">
                                <label>Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control" value="{{ old('pekerjaan', $keluarga->pekerjaan) }}" required>
                            </div>

                            <div class="form-group">
                                <label>Pendidikan</label>
                                <input type="text" name="pendidikan" class="form-control" value="{{ old('pendidikan', $keluarga->pendidikan) }}" required>
                            </div>

                            <div class="form-group">
                                <label>No. Telepon</label>
                                <input type="text" name="no_telp" class="form-control" value="{{ old('no_telp', $keluarga->no_telp) }}" required>
                            </div>

                            <div class="form-group">
                                <label>Alamat</label>
                                <textarea name="alamat" class="form-control" required>{{ old('alamat', $keluarga->alamat) }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $keluarga->email) }}" required>
                            </div>

                            <div class="form-group">
                                <label>Password (Opsional, isi jika ingin mengganti)</label>
                                <input type="password" name="password" class="form-control" value="{{ old('password', $keluarga->password) }}" required>
                            </div>



                            <button type="submit" class="btn btn-primary">Update</button>
                            <a href="{{ route('keluarga.index') }}" class="btn btn-secondary">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
