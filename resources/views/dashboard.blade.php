@extends('admin_template')

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">User Age Statistic</h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-8">
              <div class="chart-responsive">
                <canvas id="pieChart" height="150"></canvas>
              </div>
            </div>
            <div class="col-md-4" style="padding-top: 5%;">
              <ul class="chart-legend clearfix" style="font-size: x-large">
              </ul>
            </div>
          </div>
        </div>
        <div class="card-footer p-0">
          <ul class="nav nav-pills flex-column">
            <li class="nav-item">
              <a href="#" class="nav-link">
              Total User:
              <span class="text-success total-user"></span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
<script src="{{ asset('/bower_components/admin-lte/plugins/chart.js/Chart.min.js') }}"></script>
<script>
  var backgroundColor = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de', '#FF8A8A', '#F4DEB3', '#D5ED9F', '#E0E5B6']
  var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
  var pieData = {
    labels: [
      
    ],
    datasets: [
      {
        data: [],
        backgroundColor: []
      }
    ]
  }
  var pieOptions = {
    legend: {
      display: false
    }
  }

  $.ajax({
    url: _baseURL + '/dashboard/fn-get-total-user/',
    method: 'GET',
    success: function(response) {
      if (response.community != undefined) {
        var iter = 0;
        
        $.each(response.community, function (key, val) {
          pieData.labels[iter] = val.age_range;
          pieData.datasets[0].data[iter] = val.total_users;
          pieData.datasets[0].backgroundColor[iter] = backgroundColor[iter];
          $('.chart-legend').append('<li><i class="far fa-circle" style="color:'+backgroundColor[iter]+'"></i> '+val.age_range+' : '+val.total_users+' user</li>')
          iter++;
        });
        $('.total-user').html('<b>'+response.total_users+'</b>')
        var pieChart = new Chart(pieChartCanvas, {
          type: 'doughnut',
          data: pieData,
          options: pieOptions
        })
      }
    },
    error: function() {
        
    }
  });
</script>
@endsection