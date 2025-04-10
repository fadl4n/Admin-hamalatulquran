@extends('admin_template')

@section('title page', "Detail Nilai ")

@section('content')
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="font-size: 1.8rem;">{{ $santri->nama }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Hafalan</h4>
                        <table class="table table-bordered">
                            <thead class="bg-navy disabled">
                                <tr>
                                    <th>Surat</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hafalan as $item)
                                    <tr>
                                        <td>{{ $item['surat'] }}</td>
                                        <td>{{ $item['nilai'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h4>Muroja'ah</h4>
                        <table class="table table-bordered">
                            <thead class="bg-navy disabled">
                                <tr>
                                    <th>Surat</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($murojaah as $item)
                                    <tr>
                                        <td>{{ $item['surat'] }}</td>
                                        <td>{{ $item['nilai'] }}</td>
                                    </tr>
                                @endforeach
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
