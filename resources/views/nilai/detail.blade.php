@extends('admin_template')

@section('title page', "Detail Nilai ")

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card col-md-10 offset-md-1">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 1.8rem;">{{ $santri->nama }}</h3>
            </div>
            <div class="card-body">

                {{-- Form Search Surat --}}
                <form method="GET" action="{{ route('nilai.show', [$santri->id_santri, $idtarget]) }}" class="mb-4">
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" name="search_surat" class="form-control" placeholder="Cari surat..." value="{{ request('search_surat') }}">
                        <button class="btn btn-primary" type="submit">Cari</button>
                    </div>
                </form>

                <div class="row">
                    {{-- Tabel Hafalan --}}
                    <div class="col-md-6">
                        <h4>Hafalan</h4>
                        <table class="table table-bordered">
                            <thead class="bg-navy disabled">
                                <tr>
                                    <th>Surat</th>
                                    <th>Ayat</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($hafalan as $item)
                                    <tr>
                                        <td>{{ $item['surat'] }}</td>
                                        <td>{{ $item['ayat'] }}</td>
                                        <td>{{ $item['nilai'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data hafalan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Tabel Muroja'ah --}}
                    <div class="col-md-6">
                        <h4>Muroja'ah</h4>
                        <table class="table table-bordered">
                            <thead class="bg-navy disabled">
                                <tr>
                                    <th>Surat</th>
                                    <th>Ayat</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($murojaah as $item)
                                    <tr>
                                        <td>{{ $item['surat'] }}</td>
                                        <td>{{ $item['ayat'] ?? '-' }}</td>
                                        <td>{{ $item['nilai'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data muroja'ah.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="text-start mt-3">
                    <a href="{{ route('nilai.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
