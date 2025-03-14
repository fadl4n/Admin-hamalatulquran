@extends('admin_template')

@section('content')
<div class="container-fluid">
  <div class="row">
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

  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">Statistik User :</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              <div class="chart-responsive">
                <canvas id="pieChart" height="150"></canvas>
              </div>
            </div>
            <div class="col-md-4" style="padding-top: 5%;">
              <ul class="chart-legend clearfix" style="font-size: x-large"></ul>
            </div>
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
  // Data untuk chart pie
  var backgroundColor = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#FF8A8A', '#F4DEB3', '#D5ED9F', '#E0E5B6'];
  var pieChartCanvas = $('#pieChart').get(0).getContext('2d');
  var pieData = {
    labels: [],
    datasets: [
      {
        data: [],
        backgroundColor: []
      }
    ]
  };
  var pieOptions = {
    legend: {
      display: false
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


        var pieChart = new Chart(pieChartCanvas, {
            type: 'doughnut',
            data: pieData,
            options: pieOptions
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
