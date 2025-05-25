@extends('admin_template')

@section('title page')
    Edit Absensi - {{ $absen->santri->nama }}
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

                {{-- Tampilkan error validation --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('absen.update', $absen->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Input tersembunyi untuk kelas --}}
                    <input type="hidden" name="kelas_id" id="kelas" value="{{ $absen->santri->id_kelas }}">

                    <div class="form-group">
                        <label for="santri">Pilih Nama Santri</label>
                        <select id="santri" name="santri_id" class="form-control" required>
                            <option value="">-- Pilih Santri --</option>
                            {{-- Opsi akan diisi lewat AJAX --}}
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="tgl_absen">Tanggal Absensi</label>
                        <input type="date" id="tgl_absen" name="tgl_absen" class="form-control"
                            value="{{ old('tgl_absen', $absen->tgl_absen->format('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group">
                        <label for="status">Keterangan</label>
                        <select id="status" name="status" class="form-control" required>
                            <option value="">-- Pilih Keterangan --</option>
                            <option value="1" {{ old('status', $absen->status) == 1 ? 'selected' : '' }}>Hadir</option>
                            <option value="2" {{ old('status', $absen->status) == 2 ? 'selected' : '' }}>Sakit</option>
                            <option value="3" {{ old('status', $absen->status) == 3 ? 'selected' : '' }}>Izin</option>
                            <option value="4" {{ old('status', $absen->status) == 4 ? 'selected' : '' }}>Alfa</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="{{ route('absen.detail', $absen->santri->id_kelas) }}" class="btn btn-secondary">Batal</a>
                </form>

            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        const santriSelect = $('#santri');
        const kelasId = $('#kelas').val();

        function loadSantris(kelasId, selectedSantriId = null) {
            if (!kelasId) {
                santriSelect.prop('disabled', true).html('<option value="">-- Pilih Santri --</option>');
                return;
            }

            $.ajax({
                url: '{{ route("absen.getSantriByKelas") }}',
                method: 'GET',
                data: { kelas_id: kelasId },
                success: function(response) {
                    let options = '<option value="">-- Pilih Santri --</option>';
                    response.forEach(function(santri) {
                        const selected = santri.id_santri == selectedSantriId ? 'selected' : '';
                        options += `<option value="${santri.id_santri}" ${selected}>${santri.nama}</option>`;
                    });
                    santriSelect.prop('disabled', false).html(options);
                },
                error: function() {
                    alert('Gagal memuat data santri');
                    santriSelect.prop('disabled', true).html('<option value="">-- Pilih Santri --</option>');
                }
            });
        }

        // Load santri saat halaman siap
        loadSantris(kelasId, '{{ old("santri_id", $absen->id_santri) }}');
    });
</script>
@endsection
