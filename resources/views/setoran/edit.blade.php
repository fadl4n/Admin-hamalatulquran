@extends('admin_template')

@section('content')
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Setoran</h3>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form action="{{ route('setoran.update', $setoran->id_setoran) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nama Santri</label>
                                    <select name="id_santri" class="form-control @error('id_santri') is-invalid @enderror" required>
                                        @foreach ($santris as $santri)
                                            <option value="{{ $santri->id_santri }}" {{ old('id_santri', $setoran->id_santri) == $santri->id_santri ? 'selected' : '' }}>
                                                {{ $santri->nama }} | {{ $santri->nisn }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_santri')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Kelas</label>
                                    <select name="id_kelas" class="form-control @error('id_kelas') is-invalid @enderror" required>
                                        @foreach ($kelas as $kelass)
                                            <option value="{{ $kelass->id_kelas }}" {{ old('id_kelas', $setoran->id_kelas) == $kelass->id_kelas ? 'selected' : '' }}>
                                                {{ $kelass->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_kelas')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Setoran</label>
                                    <input type="date" name="tgl_setoran" class="form-control @error('tgl_setoran') is-invalid @enderror" value="{{ old('tgl_setoran', $setoran->tgl_setoran) }}" required>
                                    @error('tgl_setoran')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pengajar</label>
                                    <select name="id_pengajar" class="form-control @error('id_pengajar') is-invalid @enderror">
                                        <option value="">Pilih Pengajar</option>
                                        @foreach($pengajars as $pengajar)
                                            <option value="{{ $pengajar->id_pengajar }}" {{ old('id_pengajar', $setoran->id_pengajar) == $pengajar->id_pengajar ? 'selected' : '' }}>
                                                {{ $pengajar->nama }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_pengajar')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Surat</label>
                                    <select name="id_surat" class="form-control @error('id_surat') is-invalid @enderror" required>
                                        @foreach ($surats as $surat)
                                            <option value="{{ $surat->id_surat }}" {{ old('id_surat', $setoran->id_surat) == $surat->id_surat ? 'selected' : '' }}>
                                                {{ $surat->nama_surat }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('id_surat')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ayat Mulai</label>
                                    <input type="number" name="jumlah_ayat_start" class="form-control @error('jumlah_ayat_start') is-invalid @enderror" value="{{ old('jumlah_ayat_start', $setoran->jumlah_ayat_start) }}" required>
                                    @error('jumlah_ayat_start')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ayat Akhir</label>
                                    <input type="number" name="jumlah_ayat_end" class="form-control @error('jumlah_ayat_end') is-invalid @enderror" value="{{ old('jumlah_ayat_end', $setoran->jumlah_ayat_end) }}" required>
                                    @error('jumlah_ayat_end')<span class="text-danger">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="1" {{ old('status', $setoran->status) == '1' ? 'selected' : '' }}>Selesai</option>
                                <option value="0" {{ old('status', $setoran->status) == '0' ? 'selected' : '' }}>Proses</option>
                            </select>
                            @error('status')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="3">{{ old('keterangan', $setoran->keterangan) }}</textarea>
                            @error('keterangan')<span class="text-danger">{{ $message }}</span>@enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('setoran.index') }}" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
