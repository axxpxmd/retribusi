<style>
    .highcharts-background {
        background: transparent !important;
    }
</style>
<figure class="highcharts-figure" style="margin-top: -75px !important">
    <div id="pieChartChanelBayar"></div>
</figure>
@push('scriptDashboard')
<script type="text/javascript">
    var data = <?php echo $dataPieChartChanelBayar?>;

    Highcharts.chart('pieChartChanelBayar', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            backgroundColor: 'transparent',
            type: 'pie'
        },
        credits: {
            enabled: false
        },
        exporting: {
            enabled: false
        },
        title: false,
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        tooltip: {
            style: {
                fontSize: '75%'
            },
            pointFormat: 'Total Data: <b>{point.y}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                size: '35%',
                cursor: 'pointer',
                dataLabels: {
                    crop: false,
                    distance: 25,
                    overflow: "none",
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        fontSize: '60%'
                    }
                },
                center: ["50%", "50%"]
            }
        },
        series: [{
            colorByPoint: true,
            data: data
        }]
    });
</script>
@endpush
