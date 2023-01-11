<style>
    .highcharts-root {
        height: 320px !important;
        margin-top: 20px !important;
    }

    .highcharts-container {
        height: 320px !important;
        margin-top: 20px !important;
    }

    .highcharts-background {
        background: transparent !important;
    }
</style>
<figure class="highcharts-figure">
    <div id="pieChartChanelBayar"></div>
</figure>
@push('script')
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script type="text/javascript">
    var parents = <?php echo $dataPieChartChanelBayar?>;

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
                fontSize: '100%'
            },
            pointFormat: '<b>{point.name}</b>: <b>{point.y}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                size: '50%',
                cursor: 'pointer',
                dataLabels: {
                    crop: false,
                    distance: 25,
                    overflow: "none",
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        fontSize: '100%'
                    }
                },
                center: ["50%", "50%"]
            }
        },
        series: [{
            colorByPoint: true,
            data: parents
        }]
    });
</script>
@endpush
