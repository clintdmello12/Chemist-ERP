@extends('layouts.app')

@section('htmlheader_title')
	Dashboard
@endsection

@section('contentheader_title')
	Dashboard
@endsection

@section('contentheader_description')
    Control panel
@endsection

@section('main-content')
	<div class="row">
        <a href="{{ route('users.index') }}">
	        <div class="col-md-3 col-sm-6 col-xs-12">
	            <div class="info-box">
	              <span class="info-box-icon bg-aqua">
	                <i class="fa fa-user"></i>
	              </span>
	              <div class="info-box-content">
	                <span class="info-box-text">Users</span>
                    @if ($current_user->hasRole('admin'))
	                <span class="info-box-number">{{ $users->count() }}</span>
                    @else
                    <span class="info-box-number">{{ $current_user->vendor->users->count() }}</span>
                    @endif
	              </div>
	            </div>
	        </div>
        </a>
        @if ($current_user->hasRole('admin'))
        <a href="{{ route('vendors.index') }}">
	        <div class="col-md-3 col-sm-6 col-xs-12">
	            <div class="info-box">
		            <span class="info-box-icon bg-yellow">
		              <i class="fa fa-institution"></i>
		            </span>
		            <div class="info-box-content">
		              <span class="info-box-text">Vendor</span>
		              <span class="info-box-number">{{ $vendors->count() }}</span>
		            </div>
	            </div>
	        </div>
        </a>
        @endif
        <a href="{{ route('expired_inventories') }}">
          <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-red">
                  <i class="fa fa-warning"></i>
                </span>
                <div class="info-box-content">
                  <span class="info-box-text">Expired Inventories</span>
                  <span class="info-box-number">{{ $inventories->count() }}</span>
                </div>
              </div>
          </div>
        </a>
  </div>
  <div class="row">
      <section class="col-md-6">
          <div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title">Total Sales and Purchases</h3>
            </div>
            <div class="box-body">
              <canvas id="sale-purchase" style="height:300px"></canvas>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
      </section>
      <section class="col-md-6">
          <div class="box box-solid">
            <div class="box-header">
              <h3 class="box-title">Monthly Sales</h3>
            </div>
            <div class="box-body">
              <canvas id="sale-bar" style="height:300px"></canvas>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
      </section>
  </div>
@endsection

@section('internal-script')
    <script type="text/javascript">
        $(function () {

            var ctx = document.getElementById("sale-purchase");
            var salesPurchaseChart = new Chart(ctx,{
                type: 'pie',
                data: {
                    labels: [
                        "Sales",
                        "Purchases"
                    ],
                    datasets: [
                        {
                            data: [
                                {{ $sales->count() }},
                                {{ $purchases->count() }}
                            ],
                            backgroundColor: [
                                "#FF6384",
                                "#36A2EB"
                            ],
                            hoverBackgroundColor: [
                                "#FF6384",
                                "#36A2EB"
                            ]
                        }]
                },
                options: {
                    animation:{
                        animateScale:true
                    }
                }
            });

            var ctx1 = document.getElementById("sale-bar");
            var salesBar = new Chart(ctx1,{
                type: 'bar',
                data: {
                    labels: {!! json_encode($months) !!},
                    datasets: [{
                        type: 'bar',
                        yAxisID: 'price',
                        label: 'Monthly Sales in Rs.',
                        data: {!! json_encode($monthlySales) !!},
                        backgroundColor: 'rgba(153, 102, 255, 0.2)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    },{
                        type: 'line',
                        yAxisID: 'count',
                        label: 'Sales count',
                        data: {!! json_encode($monthlySaleCount) !!},
                        backgroundColor: 'rgba(153, 102, 255, 0)',
                        borderColor: 'rgba(153, 102, 255, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            id: 'price',
                            position: 'left',
                            ticks: {
                                beginAtZero:true
                            }
                        },{
                            id: 'count',
                            position: 'right',
                            ticks: {
                                stepSize: 1,
                                beginAtZero:true
                            }
                        }]
                    }
                }
            });
        });
    </script>
@endsection
