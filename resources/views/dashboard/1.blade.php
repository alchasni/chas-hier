@extends('layouts.master')

@section('title')
    Dashboard
@endsection

@section('breadcrumb')
    @parent
    <li class="active">Dashboard</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <button id="prevMonth" class="btn btn-custom" aria-label="Previous Month">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <span id="dateRange"></span>
                        <button id="nextMonth" class="btn btn-custom" aria-label="Next Month">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="chart">
                                <canvas id="salesChart" style="height: 180px;"></canvas>
                            </div>
                        </div>
                    </div>
                    <div id="loading-spinner" class="loading-spinner" style="display: none;">
                        <i class="fa fa-spinner fa-spin"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $category }}</h3>
                    <p><b>Categories</b></p>
                </div>
                <div class="icon"><i class="fa fa-tags"></i></div>
                <a href="{{ route('category.index') }}" class="small-box-footer">View Details <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $product }}</h3>
                    <p><b>Products</b></p>
                </div>
                <div class="icon"><i class="fa fa-dropbox"></i></div>
                <a href="{{ route('product.index') }}" class="small-box-footer">View Details <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $guest }}</h3>
                    <p><b>Guest</b></p>
                </div>
                <div class="icon"><i class="fa fa-id-card"></i></div>
                <a href="{{ route('guest.index') }}" class="small-box-footer">View Details <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $transaction }}</h3>
                    <p><b>Today's Transaction</b></p>
                </div>
                <div class="icon"><i class="fa fa-money"></i></div>
                <a href="{{ route('transaction.index') }}" class="small-box-footer">View Details <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('AdminLTE-2/bower_components/chart.js/Chart.js') }}"></script>
    <script>
        $(function () {
            let salesChart = null;
            let salesChartCanvas = $('#salesChart').get(0).getContext('2d');
            let today = new Date().toISOString().split('T')[0];

            var currentDate = new Date();

            function updateChart(monthOffset) {
                var newDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + monthOffset, 1);
                currentDate = newDate;

                let nextMonthDate = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
                let nextMonthStartDate = nextMonthDate.toISOString().split('T')[0];

                if (nextMonthStartDate > today) {
                    $('#nextMonth').prop('disabled', true);
                } else {
                    $('#nextMonth').prop('disabled', false);
                }

                var month = newDate.getMonth() + 1;
                var year = newDate.getFullYear();

                $.ajax({
                    url: 'api/transaction',
                    method: 'GET',
                    data: {
                        year: year,
                        month: month
                    },
                    success: function(response) {
                        if (salesChart) {
                            salesChartCanvas.clearRect(0, 0, salesChartCanvas.canvas.width, salesChartCanvas.canvas.height);
                            salesChart.clear();
                            salesChart.destroy();
                        }
                        var salesChartData = {
                            labels: response.dates,
                            datasets: [
                                {
                                    label: 'Sales',
                                    fillColor: 'rgba(60,141,188,0.9)',
                                    strokeColor: 'rgba(60,141,188,0.8)',
                                    pointColor: '#3b8bba',
                                    pointStrokeColor: 'rgba(60,141,188,1)',
                                    pointHighlightFill: '#fff',
                                    pointHighlightStroke: 'rgba(60,141,188,1)',
                                    data: response.incomes
                                }
                            ]
                        };

                        var salesChartOptions = {
                            pointDot: false,
                            responsive: true
                        };
                        $('#dateRange').text(`Sales ${response.startDate} - ${response.endDate}`);
                        salesChart = new Chart(salesChartCanvas).Line(salesChartData, salesChartOptions);
                    },
                    complete: function() {
                        $('#loading-spinner').hide();
                    },
                    error: function() {
                        alert('Failed to load data');
                        $('#loading-spinner').hide();
                    }
                });
            }

            $('#prevMonth').click(function() {
                updateChart(-1);
            });

            $('#nextMonth').click(function() {
                updateChart(1);
            });

            updateChart(0);
        });
    </script>
@endpush

