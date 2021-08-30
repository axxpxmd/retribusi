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