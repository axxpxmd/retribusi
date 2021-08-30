<style>
    #pieChart2 {
        height: auto;
        width: 500px
    }
</style>
<figure class="highcharts-figure">
    <div id="pieChart2"></div>
</figure>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
    var year =  new Date().getFullYear();

    Highcharts.chart('pieChart2', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        credits: {
            enabled: false
        },
        title: {
            text: 'Total Transaksi Retribusi, ' + year
        },
        tooltip: {
            formatter: function() {
                let indonesian = Intl.NumberFormat('id-ID');
                return '<b>'+ this.series.name +'</b>: '+ 'Rp.' + indonesian.format(this.point.bayar) ;
            }
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<h1>{point.name}</h1>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'Jumlah',
            data: [{
                name: 'Belum Bayar',
                y: {{ $belumBayarTotalData }},
                color: '#2196f3',
                bayar: {{ $belumBayarTotalBayar }}
            }, {
                name: 'Sudah Bayar',
                y: {{ $sudahBayarTotalData }},
                color: '#ffee58',
                sliced: true, 
                bayar: {{ $sudahBayarTotalBayar }}
            }],
        }]
    });

</script>















{{-- <div class="p-0">
    <div class="chart-pie">
        <canvas id="myChart"></canvas>
    </div>
    <div class="mt-4 text-center small">
        <span class="mr-2">
            <i class="icon icon-circle text-success"></i> Belum Bayar
        </span>
        <span class="mr-2">
            <i class="icon icon-circle text-primary"></i> Sudah Bayar
        </span>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var chart = new Chart(ctx, {
    type: 'doughnut',

    // The data for our dataset
    data: {
        labels: ["Sudah Bayar", "Belum Bayar"],
        datasets: [{
            backgroundColor: ['#2979FF', '#1cc88a', '#36b9cc', '#f6c23e'],
            data: [{{$sudahBayar}}, {{$belumBayar}}],
        }]
    },

    // Configuration options go here
    options: {
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#000000",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: false
        },
        cutoutPercentage: 60,
    }
});
</script> --}}
