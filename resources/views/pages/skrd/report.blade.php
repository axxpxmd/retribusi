<!DOCTYPE html>
<html lang="en">
<head>
    <title>{{ $data->nm_wajib_pajak }} - {{ $data->no_skrd }}</title>
    <link rel="stylesheet" href="{{ asset('css/util.css') }}">

    <style type="text/css">

        html{
            margin: 30px
        }

		table.d {
            border-collapse: collapse;
            width: 100%
        } 

        table.d tr.d,th.d,td.d{
            table-layout: fixed;
            border: 1px solid black;
            font-size: 12px;
        }

        table.a tr.a,th.a,td.a{
            table-layout: fixed;
            border: 1px solid black;
            font-size: 12px;
        }

        table.c{
            font-size: 15px 
        }

        .t-bold {
            font-weight: bold
        }

        .m-b-0{
            margin-bottom: 0px;
        }

        .m-t-0{
            margin-top: 0px;
        }

        .m-l-5{
            margin-left: 5px;
        }

        .text-right{
            text-align: right
        }
        .text-left{
            text-align: left
        }
        .text-center{
            text-align: center
        }
        .m-t-100{
            margin-top: 100px
        }

        .text-left{
            text-align: left
        }

        .m-l-14{
            margin-left: 14px
        }

        .m-r-20{
            margin-right: 20px
        }

        .f-w-n{
            font-weight: normal
        }

        .m-l-50{
            margin-left: 50px;
        }

        .m-t-15{
            margin-top: 15px
        }
	</style>
</head>
<body >
    <table class="d">
        <tr class="d">
            <th width="40%" class="d">
                <p>PEMERINTAH KOTA TANGERANG SELATAN</p>
                <p>{{ $data->opd->n_opd }}</p>
                <p class="m-l-5 f-w-n">{{ $data->opd->alamat }} &nbsp;</p>
                <p>&nbsp;</p>
            </th>
            <th width="40%" class="d">
                <p class="m-t-0">SURAT KETETAPAN RETRIBUSI DAERAH</p>
                <p>(SKRD)</p>
                <p class="text-left m-l-14">&nbsp;</p>
                @if ($data->tgl_skrd_awal != null)
                <p class="text-left m-l-14 m-t-0 f-w-n">Tanggal SKRD : {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->format('d M Y') }}</p>
                @else
                <p class="text-left m-l-14 m-t-0 f-w-n">Tanggal SKRD : - </p>
                @endif
            </th>
            <th width="20%" class="d">
                <div>
                    <p class="text-left m-l-5 m-t-0">No. SKRD : {{ $data->no_skrd }}</p>
                    <p class="text-left m-l-5">No. BKU : {{ $data->no_bku }}</p>
                    @if ($data->tgl_bku != null)
                    <p class="text-left m-l-5">Tanggal : {{ Carbon\Carbon::createFromFormat('Y-m-d', substr($data->tgl_bku,0,10))->format('d M Y') }}</p>
                    @else
                    <p class="text-left m-l-5">Tanggal : - </p>
                    @endif
                    <p class="text-left m-l-5">Rek: </p>
                </div>
            </th>
        </tr>
    </table>
    
    <div class="m-t-15 m-l-50">
        <table class="c">
            <tr class="c">
                <td><p class="m-b-0">No Bayar </p></td>
                <td><p class="m-b-0">: {{ $data->no_bayar }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">No Pendaftaran </p></td>
                <td><p class="m-t-0 m-b-0">: {{ $data->nmr_daftar }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">Nama </p></td>
                <td><p class="m-t-0 m-b-0">: {{ $data->nm_wajib_pajak }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">Alamat </p></td>
                <td><p class="m-t-0 m-b-0">: {{ $data->alamat_wp }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">Lokasi </p></td>
                <td><p class="m-t-0 m-b-0">: {{ $data->lokasi }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">&nbsp; </p></td>
                <td>
                    <p class="m-t-0 m-b-0">&nbsp; Kecamatan</p>
                </td>
                <td>: {{ $data->kecamatan->n_kecamatan }}</td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">&nbsp; </p></td>
                <td>
                    <p class="m-t-0 m-b-0">&nbsp; Kelurahan</p>
                </td>
                <td>: {{ $data->kelurahan->n_kelurahan }}</td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">NPWPRD </p></td>
                <td><p class="m-t-0 m-b-0">: - </p></td>
                <td>&nbsp;</td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">Jatuh Tempo </p></td>
                @if ($data->tgl_skrd_akhir != null)
                <td><p class="m-t-0 m-b-0">: {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d M Y') }}</p></td>
                @else
                <td><p class="m-t-0 m-b-0">: - </p></td>
                @endif
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>

    <div class="m-t-15">
        <table class="d">
            <tr class="a">
                <th width="5%" class="a">NO</th>
                <th width="20%" class="a">NOMOR REKENING</th>
                <th width="55%" class="a">URAIAN RETRIBUSI</th>
                <th width="20%" class="a">JUMLAH (Rp)</th>
            </tr>
            <tr class="a">
                <td class="a text-center">1</td>
                <td class="a"><p class="m-l-5">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '-' }}</p></td>
                <td class="a">
                    <p class="m-l-5 m-b-0">{{ $data->jenis_pendapatan->jenis_pendapatan }}</p>
                    <p class="m-l-5 m-b-0 m-t-0">{{ $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '-' }}</p>
                    <p class="m-l-5 m-t-0">{{ $data->uraian_retribusi }}</p>
                </td>
                <td class="a">
                    <p class="m-l-5 text-right m-r-10">@currency($data->jumlah_bayar),-</p>
                    <p class="m-l-5 text-right m-r-10">(Diskon {{ $data->diskon }}%)&nbsp;&nbsp; @currency(((int) $data->diskon / 100) * $data->total_bayar),-</p>
                </td>
            </tr>
            <tr class="a">
                <td rowspan="2" class="a text-center">2</td>
                <td rowspan="2" class="a"><p class="m-l-5">-</p></td>
                <td class="a">
                    <p class="m-l-5 m-b-0">Jumlah Ketetapan Pokok Retribusi :</p>
                    <p class="m-l-5 m-t-1 m-b-0">Jumlah Sanksi :</p>
                    <p class="m-l-5 m-t-1 m-b-0">a. Bunga</p>
                    <p class="m-l-5 m-t-1">b. Kenaikan</p>
                </td>
                <td class="a">
                    <p class="m-l-5 m-b-0 m-r-10 text-right">@currency($data->denda),-</p>
                    <p class="m-l-5 m-t-1 m-b-0">&nbsp;</p>
                    <p class="m-l-5 m-t-1 m-b-0">&nbsp;</p>
                    <p class="m-l-5 m-t-1">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td class="a"><p class="m-l-5 t-bold">Jumlah Keseluruhan :</p></td>
                <td class="a"><p class="m-l-5 t-bold text-right">@currency($data->total_bayar),-</p></td>
            </tr>
            <tr class="a">
                <td colspan="4" class="a">
                    <p class="m-l-5">Dengan Huruf : <span class="t-bold">{{ $terbilang }}</span></p>
                    <p class="fs-14 t-bold m-l-5"><u>PERHATIAN :</u></p>
                    <ol>
                        <li>Penyetoran dilakukan menggunakan Bank Jabar Banten (BJB) melalui Teller/ATM BJB dengan mencantumkan <b>260721001692</b></li>
                        <li>Penyetoran dengan bank lain, melalui Transfer/BG/M-Banking/RTGS/ ditujukan ke Rekening Bank Jabar Banten (BJB) An. Rek Pen Pjk Kota Tangerang Selatan <br> No Rekening <b>{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '-' }}</b> dengan memberikan keterangan menuliskan <b>260721001692</b></li>
                        <li>Apabila SKRD ini tidak atau kurang dibayar lewat waktu paling lama 30 hari setelah SKRD diterima atau (tanggal jatuh tempo) sanksi administrasi bunga sebesar 2% per bulan</li>
                    </ol>
                </td>
            </tr>
            <tr class="a">
                <td colspan="4" class="a">
                    <div style="text-align:center; margin-right: -500px !important">
                        @if ($data->tgl_ttd != null)
                        <p>Serpong, {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->format('d M Y') }}</p>
                        @else
                        <p>Serpong, </p>
                        @endif
                        <br>
                        <br>
                        <br>
                        @if ($data->nm_ttd != null)
                            <p class="m-b-5"><u>{{ $data->nm_ttd }}</u></p>
                        @else 
                            <p class="m-b-5"><u>{{ $data->opd->nm_ttd }}</u></p>
                        @endif
                        @if ($data->nip_ttd != null)
                            <p class="m-t-0">{{ $data->nip_ttd }}</p>
                        @else
                            <p class="m-t-0">{{ $data->opd->nip_ttd }}</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>