<style>
    /* #pieChart {
        height: 300px !important
    } */
</style>
<div class="card no-b mr-n15">
    <h6 class="card-header bg-white font-weight-bold text-black">Total Transaksi</h6>
    <div class="card-body">
        <figure class="highcharts-figure">
            <div id="pieChart"></div>
        </figure>
    </div>
</div>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
    var year =  new Date().getFullYear();

    Highcharts.chart('pieChart', {
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
            text: 'Tahun ' + year
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.y}</b>'
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
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'Jumlah',
            colorByPoint: true,
            data: [{
                name: 'SKRD',
                y: {{ $totalSKRD }},
                color: '#2196f3',
                // sliced: true
            }, {
                name: 'STS',
                y: {{ $totalSTS }},
                color: '#ffeb3b',
                sliced: true
            }, {
                name: 'STRD',
                y: {{ $totalSTRD }},
                color: '#4caf50',
                // sliced: true
            }]
        }]
    });

</script>
