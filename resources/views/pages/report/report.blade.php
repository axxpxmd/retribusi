<!DOCTYPE html>
<html lang="en">
<head>
    <title>Laporan {{ $title }}</title>

    <style type="text/css">
		table.d {
            border-collapse: collapse;
            width: 100%
        } 

        table.d tr.d,th.d,td.d{
            table-layout: fixed;
            border: 1px solid black;
            font-size: 13px;
        }

        .text-center{
            text-align: center
        }

        .p-l-5{
            padding-left: 5px;
        }
    </style>
</head>
<body>
    <div>
        <p class="text-center">Laporan {{ $title }}</p>
    </div>

    <table class="d">
        <thead>
            <tr class="d">
                <th class="d">No</th>
                <th class="d">No Bayar</th>
                <th class="d">No SKRD</th>
                <th class="d">Nama</th>
                <th class="d">Jenis Retribusi</th>
                <th class="d">Tanggal SKRD</th>
                <th class="d">Tanggal Bayar</th>
                <th class="d">NTB</th>
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
                    <td width="10%" class="d p-l-5">{{ $i->no_bayar }}</td>
                    <td width="10%" class="d p-l-5">{{ $i->no_skrd }}</td>
                    <td width="10%" class="d p-l-5">{{ $i->nm_wajib_pajak }}</td>
                    <td width="30%" class="d p-l-5">{{ $i->jenis_pendapatan->jenis_pendapatan }}</td>
                    <td width="10%" class="d p-l-5"> {{ Carbon\Carbon::createFromFormat('Y-m-d', $i->tgl_skrd_awal)->format('d M Y') }}</td>
                    <td width="15%" class="d p-l-5"> {{ $i->tgl_bayar != null ? Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $i->tgl_bayar)->format('d M Y | H:i:s') : '' }}</td>
                    <td width="13%" class="d p-l-5">{{ $i->ntb }}</td>
                    <td width="10%" class="d p-l-5">@currency(((int) $i->diskon / 100) * $i->total_bayar)</td>
                    <td width="10%" class="d p-l-5">@currency($i->denda)</td>
                    <td width="12%" class="d p-l-5" >@currency($i->total_bayar)</td>
                    <td width="10%" class="d p-l-5">{{ $i->status_bayar == 1 ? 'Sudah' : 'Belum' }}</td>
                </tr>
            @empty
            <tr class="d">
                <td class="d text-center" colspan="7">Tidak ada disini.</td>
            </tr>
            @endforelse
        </tbody>
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