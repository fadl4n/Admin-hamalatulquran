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
          <h3 class="card-title">Statistik Santri per Angkatan</h3>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const lineChartCanvas = $('#lineChart').get(0).getContext('2d');

  const lineChartData = {
    labels: [],
    datasets: [{
      label: 'Jumlah Santri',
      fill: false,
      borderColor: '#3e95cd',
      pointBackgroundColor: '#3e95cd',
      tension: 0.3,
      data: []
    }]
  };

  const lineChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    scales: {
      x: {
        title: {
          display: true,
          text: 'Angkatan'
        }
      },
      y: {
        beginAtZero: true,
        title: {
          display: true,
          text: 'Jumlah Santri'
        },
        ticks: {
          // Hanya tampilkan angka bulat
          callback: function(value) {
            return Number.isInteger(value) ? value : null;
          },
          stepSize: 10 // default, akan diubah dinamis di bawah
        }
      }
    },
    plugins: {
      legend: {
        display: true,
        position: 'top'
      }
    }
  };

  $.ajax({
    url: _baseURL + '/statistics',
    method: 'GET',
    success: function(response) {
      console.log('Response Statistik:', response);

      // Update total
      $('#santriCount').text(response.santri_count || 0);
      $('#kelasCount').text(response.kelas_count || 0);
      $('#pengajarCount').text(response.pengajar_count || 0);

      if (Array.isArray(response.santri_per_angkatan)) {
        const labels = [];
        const data = [];

        response.santri_per_angkatan.forEach(function(item) {
          if (item.count > 0) {
            labels.push(item.angkatan);
            data.push(item.count);
          }
        });

        if (data.length === 0) {
          $('#lineChart').replaceWith('<div class="text-center text-muted p-3">Tidak ada data santri per angkatan.</div>');
          return;
        }

        lineChartData.labels = labels;
        lineChartData.datasets[0].data = data;

        // Hitung stepSize yang sesuai berdasarkan nilai tertinggi
        const maxData = Math.max(...data);
        const stepSize = Math.ceil(maxData / 5); // Misalnya 5 garis vertikal
        lineChartOptions.scales.y.ticks.stepSize = stepSize;

        new Chart(lineChartCanvas, {
          type: 'line',
          data: lineChartData,
          options: lineChartOptions
        });
      } else {
        console.warn('santri_per_angkatan tidak dalam format array');
        $('#lineChart').replaceWith('<div class="text-center text-danger p-3">Format data salah.</div>');
      }
    },
    error: function(xhr, status, error) {
      console.error('Gagal memuat data statistik:', error);
      $('#santriCount').text('0');
      $('#kelasCount').text('0');
      $('#pengajarCount').text('0');
      $('#lineChart').replaceWith('<div class="text-center text-danger p-3">Gagal memuat grafik.</div>');
    }
  });
</script>
@endsection

