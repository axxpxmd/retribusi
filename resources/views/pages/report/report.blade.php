<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laporan {{ $title }}</title>

    <style type="text/css">
     body {
            padding-top: 0px !important;
            color: black !important;
        }
		table.d {
            border-collapse: collapse;
            width: 100%
        } 

        table.d tr.d,th.d,td.d{
            table-layout: fixed;
            border: 1px solid black;
            font-size: 10px;
        }

        .text-center{
            text-align: center
        }

        .p-l-5{
            padding-left: 5px;
        }
        .fs-14{
            font-size: 14px
        }
    </style>
</head>
<body>
    <div>
        <p class="text-center">Laporan {{ $title }}</p>
    </div>
    <table class="fs-14" style="margin-bottom: 10px">
        <tr>
            <td>Jenis</td>
            <td>: 
                @if ($jenis == 0)
                    -
                @endif
                @if ($jenis == 1)
                    SKRD
                @endif
                @if ($jenis == 2)
                    STS
                @endif
            </td>
        </tr>
        <tr>
            <td>OPD</td>
            <td>: {{ $opd ? $opd->n_opd : 'Semua' }}</td>
        </tr>
        <tr>
            <td>Jenis Pendapatan</td>
            <td>: {{ $jenis_pendapatan ? $jenis_pendapatan->jenis_pendapatan : 'Semua' }}</td>
        </tr>
        <tr>
            <td>Rincian Pendapatan</td>
            <td>: {{ $rincian_pendapatan ? $rincian_pendapatan->rincian_pendapatan : 'Semua' }}</td>
        </tr>
        @if($jenis == 1) {
            <tr>
                <td>Status Bayar</td>
                @if ($status_bayar)
                <td>: {{ $status_bayar == 1 ? 'Sudah Bayar' : 'Belum Bayar' }}</td>
                @else
                <td>: Semua</td>
                @endif
            </tr>
        @endif
        @if($jenis == 2) {
            <tr>
                <td>Metode Bayar</td>
                <td>: {{ $channel_bayar ? $metode_bayar : 'Semua' }}</td>
            </tr>
        @endif
        <tr>
            <td>Periode {{ $jenis == 2 ? 'STS' : 'SKRD' }}</td>
            <td>: {{ Carbon\Carbon::createFromFormat('Y-m-d', $from)->isoFormat('D MMMM Y') }} - {{ Carbon\Carbon::createFromFormat('Y-m-d', $to)->isoFormat('D MMMM Y') }}</td>
        </tr>
    </table>
    <p style="text-align: right">{{ $datas->count() }} Data</p>
    <table class="d">
        <thead>
            <tr class="d">
                <th class="d">No</th>
                <th class="d">No Bayar</th>
                <th class="d">No SKRD</th>
                <th class="d">Nama</th>
                <th class="d">Rincian Pendapatan</th>
                <th class="d">Tanggal SKRD</th>
                <th class="d">Tanggal Bayar</th>
                <th class="d">NTB</th>
                <th class="d">Ketetapan</th>
                <th class="d">Diskon</th>
                <th class="d">Denda</th>
                <th class="d">Total Bayar</th>
                <th class="d">Status Bayar</th>
            </tr>
        </thead>
        <tbody>
            @forelse  ($data as $index => $i)
                <tr class="d">
                    <td width="3%" class="d text-center">{{ $index+1 }}</td>
                    <td width="8%" class="d p-l-5">{{ $i['no_bayar'] }}</td>
                    <td width="8%" class="d p-l-5">{{ $i['no_skrd'] }}</td>
                    <td width="17%" class="d p-l-5">{{ $i['nm_wajib_pajak'] }}</td>
                    <td width="30%" class="d p-l-5">{{ $i['rincian_pendapatan'] }}</td>
                    <td width="10%" class="d p-l-5"> {{ Carbon\Carbon::createFromFormat('Y-m-d', $i['tgl_skrd_awal'])->isoFormat('D MMMM Y') }}</td>
                    <td width="14%" class="d p-l-5"> {{ $i['tgl_bayar'] != null ? Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $i['tgl_bayar'])->isoFormat('D MMMM Y | hh:mm:ss') : '' }}</td>
                    <td width="20%" class="d p-l-5">
                        {{ $i['ntb'] }}
                        @if (strpos($i['chanel_bayar'], 'QRIS') !== false)
                            | QRIS
                        @endif
                        @if (strpos($i['chanel_bayar'], 'Virtual Account') !== false)
                            | VA
                        @endif
                        @if (strpos($i['chanel_bayar'], 'ATM BJB') !== false)
                            | ATM
                        @endif
                    </td>
                    <td width="10%" class="d p-l-5">@currency($i['jumlah_bayar'])</td>
                    <td width="10%" class="d p-l-5">@currency(((int) $i['diskon'] / 100) * $i['jumlah_bayar'])</td>
                    <td width="10%" class="d p-l-5">@currency((int)$i['denda'])</td>
                    <td width="11%" class="d p-l-5" >@currency((int)$i['jumlah_bayar'] + $i['denda'])</td>
                    <td width="5%" class="d p-l-5 text-center">{{ $i['status_bayar'] == 1 ? 'Sudah' : 'Belum' }}</td>
                </tr>
            @empty
            <tr class="d">
                <td class="d text-center" colspan="13">Tidak ada disini.</td>
            </tr>
            @endforelse
        </tbody>
        <tr class="d">
            <th class="d" colspan="11">Total</th>
            <th class="d" colspan="2">@currency($totalBayar)</th>
        </tr>
    </table>
    <script type="text/php">
        if (isset($pdf)) {
            $text = "page {PAGE_NUM} / {PAGE_COUNT}";
            $size = 10;
            $font = $fontMetrics->getFont("Verdana");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>