<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Santri</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #333;
            margin: 20px;
        }

        .kop-surat {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }

        .kop-surat img {
            position: absolute;
            top: 0;
            left: 0;
            width: 80px;
            height: 80px;
        }

        .kop-surat h2 {
            margin: 0;
            font-size: 16pt;
        }

        .kop-surat p {
            margin: 0;
            font-size: 11pt;
        }

        .garis {
            border-top: 3px double #000;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        h4 {
            margin-top: 30px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
            color: #2c3e50;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #aaa;
            padding: 8px 10px;
            vertical-align: top;
            text-align: left;
        }

        th {
            background-color: #f4f6f7;
            font-weight: 600;
            width: 30%;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    {{-- Kop Surat --}}
    <div class="kop-surat">
        <div>
            <h2>PONDOK PESANTREN HAMALATUL QUR'AN</h2>
            <p>Jl. Pendidikan No. 123, Padang, Sumatera Barat</p>
            <p>Telepon: (xxxx) xxxxxx | Email: hamalatul@quran.sch.id</p>
        </div>
    </div>
    <div class="garis"></div>

    {{-- Data Santri --}}
    <h4>Data Santri</h4>
    <table>
        <tr><th>Nama</th><td>{{ $santri->nama }}</td></tr>
        <tr><th>NISN</th><td>{{ $santri->nisn }}</td></tr>
        <tr><th>Tempat, Tanggal Lahir</th><td>{{ $santri->tempat_lahir }}, {{ $santri->tgl_lahir }}</td></tr>
        <tr><th>Email</th><td>{{ $santri->email }}</td></tr>
        <tr><th>Alamat</th><td>{{ $santri->alamat }}</td></tr>
        <tr><th>Angkatan</th><td>{{ $santri->angkatan }}</td></tr>
        <tr><th>Jenis Kelamin</th><td>{{ match ($santri->jenis_kelamin) {1 => 'Laki-laki', 2 => 'Perempuan', default => 'Tidak diketahui'} }}</td></tr>
        <tr><th>Kelas</th><td>{{ $santri->kelas->nama_kelas ?? 'Tidak Ada Kelas' }}</td></tr>
        <tr><th>Status</th><td>{{ $santri->status == 1 ? 'Aktif' : 'Tidak Aktif' }}</td></tr>
    </table>

    {{-- Data Ayah --}}
    <h4>Data Ayah</h4>
    <table>
        <tr><th>Nama</th><td>{{ $ayah->nama ?? '-' }}</td></tr>
<tr>
    <th>Status</th>
    <td>{{ [1 => 'Hidup', 2 => 'Meninggal'][$ayah?->status] ?? '-' }}</td>
</tr>

        <tr><th>Pekerjaan</th><td>{{ $ayah->pekerjaan ?? '-' }}</td></tr>
        <tr><th>Pendidikan</th><td>{{ $ayah->pendidikan ?? '-' }}</td></tr>
        <tr><th>No. Telepon</th><td>{{ $ayah->no_telp ?? '-' }}</td></tr>
        <tr><th>Alamat</th><td>{{ $ayah->alamat ?? '-' }}</td></tr>
        <tr><th>Email</th><td>{{ $ayah->email ?? '-' }}</td></tr>
        <tr><th>Tempat, Tanggal Lahir</th><td>{{ $ayah->tempat_lahir ?? '-' }}, {{ $ayah->tgl_lahir ?? '-' }}</td></tr>
    </table>

    {{-- Data Ibu --}}
    <h4>Data Ibu</h4>
    <table>
        <tr><th>Nama</th><td>{{ $ibu->nama ?? '-' }}</td></tr>
<tr>
    <th>Status</th>
    <td>{{ [1 => 'Hidup', 2 => 'Meninggal'][$ibu?->status] ?? '-' }}</td>
</tr>

        <tr><th>Pekerjaan</th><td>{{ $ibu->pekerjaan ?? '-' }}</td></tr>
        <tr><th>Pendidikan</th><td>{{ $ibu->pendidikan ?? '-' }}</td></tr>
        <tr><th>No. Telepon</th><td>{{ $ibu->no_telp ?? '-' }}</td></tr>
        <tr><th>Alamat</th><td>{{ $ibu->alamat ?? '-' }}</td></tr>
        <tr><th>Email</th><td>{{ $ibu->email ?? '-' }}</td></tr>
        <tr><th>Tempat, Tanggal Lahir</th><td>{{ $ibu->tempat_lahir ?? '-' }}, {{ $ibu->tgl_lahir ?? '-' }}</td></tr>
    </table>

    {{-- Data Wali --}}
    <h4>Data Wali</h4>
    <table>
        <tr><th>Nama</th><td>{{ $wali->nama ?? '-' }}</td></tr>
<tr>
    <th>Status</th>
    <td>{{ [1 => 'Hidup', 2 => 'Meninggal'][$wali
    ?->status] ?? '-' }}</td>
</tr>

        <tr><th>Pekerjaan</th><td>{{ $wali->pekerjaan ?? '-' }}</td></tr>
        <tr><th>Pendidikan</th><td>{{ $wali->pendidikan ?? '-' }}</td></tr>
        <tr><th>No. Telepon</th><td>{{ $wali->no_telp ?? '-' }}</td></tr>
        <tr><th>Alamat</th><td>{{ $wali->alamat ?? '-' }}</td></tr>
        <tr><th>Email</th><td>{{ $wali->email ?? '-' }}</td></tr>
        <tr><th>Tempat, Tanggal Lahir</th><td>{{ $wali->tempat_lahir ?? '-' }}, {{ $wali->tgl_lahir ?? '-' }}</td></tr>
    </table>

    {{-- Halaman baru --}}
    <div class="page-break"></div>
    {{-- Penilaian --}}
    <div class="kop-surat">
        <div>
            <h2>Nilai Hafalan</h2>
        </div>
    </div>
    <div class="garis"></div>

    {{-- Nilai Hafalan --}}
    <!-- <h4>Nilai Hafalan</h4> -->
    <table>
        <thead>
            <tr><th>Surat</th><th>Ayat</th><th>Nilai</th></tr>
        </thead>
        <tbody>
            @forelse ($hafalan as $item)
                <tr>
                    <td>{{ $item['surat'] }}</td>
                    <td>{{ $item['ayat'] }}</td>
                    <td>{{ $item['nilai'] }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align: center;">Tidak ada data hafalan.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Nilai Murojaah --}}
    <h4>Nilai Muroja'ah</h4>
    <table>
        <thead>
            <tr><th>Surat</th><th>Ayat</th><th>Nilai</th></tr>
        </thead>
        <tbody>
            @forelse ($murojaah as $item)
                <tr>
                    <td>{{ $item['surat'] }}</td>
                    <td>{{ $item['ayat'] }}</td>
                    <td>{{ $item['nilai'] }}</td>
                </tr>
            @empty
                <tr><td colspan="3" style="text-align: center;">Tidak ada data muroja'ah.</td></tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
