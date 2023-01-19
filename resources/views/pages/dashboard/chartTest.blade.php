<style>
    .highcharts-figure,
    .highcharts-data-table table {
        min-width: 360px;
        max-width: 800px;
        margin: 1em auto;
    }

    .highcharts-data-table table {
        font-family: Verdana, sans-serif;
        border-collapse: collapse;
        border: 1px solid #ebebeb;
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

    #container {
        height: 90%;
        width: 95%;
        position: absolute;
    }
</style>
<figure class="highcharts-figure">
    <div id="container"></div>
</figure>
@push('scriptDashboard')
    <script type="text/javascript">
        var data = <?php echo $parentJsonRetribusiPerTahun; ?>;
        var tahunMulai = <?php echo $tahunMulai; ?>;

        Highcharts.chart('container', {
            title: {
                style: {
                    fontSize: '16px'
                },
                text: 'Grafik Pendapatan per Tahun'
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: true
            },
            yAxis: {
                title: {
                    text: 'Pendapatan per Tahun'
                }
            },
            legend: {
                enabled: false
            },
            plotOptions: {
                series: {
                    label: {
                        connectorAllowed: false
                    },
                    pointStart: tahunMulai
                }
            },

            series: [{
                name: 'Pendapatan per Tahun',
                data: data
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    }
                }]
            }

        });
    </script>
@endpush
