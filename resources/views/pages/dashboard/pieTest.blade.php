<style>

    .highcharts-background {
        background: transparent !important;
    }
</style>
<figure class="highcharts-figure" style="margin-top: -70px !important">
    <div id="pieChartTotalRetribusi"></div>
</figure>
@push('scriptDashboard')
<script type="text/javascript">
    Highcharts.chart('pieChartTotalRetribusi', {
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
                fontSize: '60%'
            },
            pointFormat: '<b>{point.name}</b>: <b>{point.y}</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                size: '45%',
                cursor: 'pointer',
                dataLabels: {
                    crop: false,
                    distance: 25,
                    overflow: "none",
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        fontSize: '80%'
                    }
                },
                center: ["50%", "50%"]
            }
        },
        series: [{
            colorByPoint: true,
            data: [{
                name: 'SKRD',
                y: {{ $totalSKRD->total_skrd }},
                color: '#FFCE3C',
            }, {
                name: 'STS',
                y: {{ $totalSTS->total_skrd }},
                color: '#4385F4',
                // sliced: true
            }, {
                name: 'STRD',
                y: {{ $totalSTRD->total_skrd }},
                color: '#ED5665',
            }]
        }]
    });
</script>
@endpush
