<style>
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 310px;
        max-width: 800px;
        margin: 1em auto;
    }

    #chartDiagramPendapatanOPD {
        width: auto;
        height: auto;
    }

    .highcharts-root {
        height: auto !important;
        margin-top: auto !important;
    }

    .highcharts-container {
        height: auto !important;
        margin-top: auto !important;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #EBEBEB;
        margin: 10px auto;
        text-align: center;
        width: 100%;
        max-width: 500px;
    }

    .highcharts-data-table caption {
        padding: 1em 0;
        font-size: 1.2em;
        color: #555;
    }

    .highcharts-data-table th {
        font-weight: 600;
        padding: 0.5em;
    }

    .highcharts-data-table td,
    .highcharts-data-table th,
    .highcharts-data-table caption {
        padding: 0.5em;
    }

    .highcharts-data-table thead tr,
    .highcharts-data-table tr:nth-child(even) {
        background: #f8f8f8;
    }

    .highcharts-data-table tr:hover {
        background: #f1f7ff;
    }
</style>
<figure class="highcharts-figure">
    <div id="chartDiagramPendapatanOPD"></div>
</figure>
@push('scriptDashboard')
<script type="text/javascript">
    var parents = <?php echo $parentJson; ?>;
    var childs = <?php echo $childJson; ?>;

    Highcharts.chart('chartDiagramPendapatanOPD', {
        chart: {
            type: 'column'
        },
        credits: {
            enabled: false
        },
        title: {
            style: {
                fontSize: '16px'
            },
            text: 'Total Pendapatan Tiap Dinas'
        },
        accessibility: {
            announceNewData: {
                enabled: true
            }
        },
        xAxis: {
            type: 'category'
        },
        yAxis: {
            title: {
                text: 'Total Pendapatan'
            }
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            series: {
                borderWidth: 0,
                dataLabels: {
                    enabled: true
                }
            }
        },
        tooltip: {
            headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
            pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>Rp.{point.y}</b> of total<br/>'
        },

        series: [{
            name: "Nama Dinas",
            data: parents,
            dataLabels: {
                enabled: true,
                formatter: function() {
                    return 'Rp.' + Highcharts.numberFormat(this.y, 0);
                }
            }
        }],
        drilldown: {
            breadcrumbs: {
                position: {
                    align: 'right'
                }
            },
            series: childs
        }
    });
</script>
@endpush
