@extends('admin_template')

@section('content')
<div class="container-fluid">
  <div class="row">
    <!-- Jumlah Santri -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header" style="background-color: #6c757d; color: white;">
          <h3 class="card-title">Jumlah Santri</h3>
        </div>
        <div class="card-body">
          <h3 class="text-center">
            <span class="badge" style="background-color: #ffffff; color: #6c757d; font-size: 2rem;" id="santriCount">0</span>
          </h3>
        </div>
      </div>
    </div>

    <!-- Jumlah Kelas -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header" style="background-color: #6c757d; color: white;">
          <h3 class="card-title">Jumlah Kelas</h3>
        </div>
        <div class="card-body">
          <h3 class="text-center">
            <span class="badge" style="background-color: #ffffff; color: #6c757d; font-size: 2rem;" id="kelasCount">0</span>
          </h3>
        </div>
      </div>
    </div>

    <!-- Jumlah Pengajar -->
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header" style="background-color: #6c757d; color: white;">
          <h3 class="card-title">Jumlah Pengajar</h3>
        </div>
        <div class="card-body">
          <h3 class="text-center">
            <span class="badge" style="background-color: #ffffff; color: #6c757d; font-size: 2rem;" id="pengajarCount">0</span>
          </h3>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik Santri per Angkatan -->
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Grafik Santri per Angkatan (Naik Turun)</h3>
        </div>
        <div class="card-body">
          <div class="chart-responsive">
            <canvas id="lineChart" height="150"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('/bower_components/admin-lte/plugins/chart.js/Chart.min.js') }}"></script>
<script>
  // Data untuk grafik naik turun (line chart)
  var lineChartCanvas = $('#lineChart').get(0).getContext('2d');
  var lineChartData = {
    labels: [], // Angkatan
    datasets: [
      {
        label: 'Jumlah Santri',
        fill: false, // Tidak mengisi area
        borderColor: '#3e95cd', // Warna garis
        pointBackgroundColor: '#3e95cd', // Warna titik
        data: [] // Jumlah santri per angkatan
      }
    ]
  };
  var lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  };

  $.ajax({
    url: _baseURL + '/statistics',
    method: 'GET',
    success: function(response) {
        console.log(response); // Untuk debug

        if (response.santri_count && response.kelas_count && response.pengajar_count) {
            // Tampilkan jumlah santri, kelas, dan pengajar
            $('#santriCount').text(response.santri_count);
            $('#kelasCount').text(response.kelas_count);
            $('#pengajarCount').text(response.pengajar_count);
        } else {
            console.error('Data tidak lengkap');
            $('#santriCount').text('0');
            $('#kelasCount').text('0');
            $('#pengajarCount').text('0');
        }

        // Isi data untuk grafik naik turun
        var labels = [];
        var data = [];

        response.santri_per_angkatan.forEach(function(item) {
            labels.push(item.angkatan);  // Angkatan
            data.push(item.count); // Jumlah santri
        });

        lineChartData.labels = labels;
        lineChartData.datasets[0].data = data;

        var lineChart = new Chart(lineChartCanvas, {
            type: 'line',
            data: lineChartData,
            options: lineChartOptions
        });
    },
    error: function() {
        console.error('Error fetching data for dashboard');
        // Menangani kesalahan
        $('#santriCount').text('0');
        $('#kelasCount').text('0');
        $('#pengajarCount').text('0');
    }
});
</script>
@endsection
