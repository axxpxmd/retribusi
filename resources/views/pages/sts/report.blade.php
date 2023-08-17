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
            height: 100;
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
            margin-left: 25px
        }
        .m-r-20{
            margin-right: 20px
        }
        .m-t-1{
            margin-top: 1px
        }
        .f-w-n{
            font-weight: normal
        }

        .m-l-10{
            margin-left: 10px;
        }
        .m-r-10{
            margin-right: 10px;
        }
        .m-b-5{
            margin-bottom: 5px
        }
        .m-l-50{
            margin-left: 50px;
        }
        .m-t-15{
            margin-top: 15px
        }
        .fs-12{
            font-size: 12px 
        }
        .f-normal{
            font-weight: normal
        }
        .mt-n40{
            margin-top: -30px !important
        }
        .mt-n15{
            margin-top: -15px !important
        }
        .fs-10{
            font-size: 10px
        }.t-blue{
            color: blue
        }
	</style>
</head>
<body >
    <table class="d">
        <tr class="d">
            <th width="40%" class="d">
                <div style="margin: 0 auto">
                    <p class="m-b-0" style="font-size: 13px">PEMERINTAH KOTA TANGERANG SELATAN</p>
                    <p class="m-t-1" style="font-size: 13px">{{ $data->opd->n_opd }}</p>
                    <p class="m-l-5 f-w-n">{{ $data->opd->alamat }} &nbsp;</p>
                    <p>&nbsp;</p>
                </div>
            </th>
            <th width="40%" class="d">
                <div style="margin: 0 auto">
                    <p class="m-b-0" style="font-size: 13px">SURAT KETETAPAN RETRIBUSI DAERAH</p>
                    <p class="m-t-1" style="font-size: 13px">(SKRD)</p>
                    <p>&nbsp;</p>
                    @if ($data->tgl_skrd_awal != null)
                    <p class="text-left m-l-14 m-t-0 f-w-n">Tanggal SKRD : {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->isoFormat('D MMMM Y') }}</p>
                    @else
                    <p class="text-left m-l-14 m-t-0 f-w-n">Tanggal SKRD : - </p>
                    @endif
                </div>
            </th>
            <th width="20%" class="d">
                <div style="margin: 0 auto">
                    <p class="text-center t-bold m-b-0" style="font-size: 13px">NO SKRD</p>
                    <p class="text-center m-t-1 f-normal">{{ $data->no_skrd }}</p>
                    <p class="text-left f-normal m-l-5 m-b-0">No BKU : {{ $data->no_bku != null ? $data->no_bku : '-' }}</p>
                    @if ($data->tgl_bku != null)
                    <p class="text-left f-normal m-l-5 m-b-0 m-t-1">Tanggal &nbsp;: {{ Carbon\Carbon::createFromFormat('Y-m-d', substr($data->tgl_bku,0,10))->isoFormat('D MMMM Y') }}</p>
                    @else 
                    <p class="text-left f-normal m-l-5 m-b-0 m-t-1">Tanggal &nbsp;: -</p>
                    @endif
                    <p class="text-left f-normal m-l-5 m-t-1">Rek &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: </p>
                </div>
            </th>
        </tr>
    </table>
    
    <div class="m-t-15 m-l-50">
        <table class="c">
            <tr class="c">
                <td><p class="m-b-0 m-t-0">No Bayar </p></td>
                <td><span>:</span></td>
                <td><p class="m-b-0 m-t-0">{{ $data->no_bayar }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">No Pendaftaran </p></td>
                <td><span>:</span></td>
                <td><p class="m-t-0 m-b-0">{{ $data->nmr_daftar }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">Nama/Perusahaan </p></td>
                <td><span>:</span></td>
                <td><p class="m-t-0 m-b-0">{{ $data->nm_wajib_pajak }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">Alamat </p></td>
                <td><span>:</span></td>
                <td><p class="m-t-0 m-b-0">{{ $data->alamat_wp }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">Lokasi </p></td>
                <td><span>:</span></td>
                <td><p class="m-t-0 m-b-0">{{ $data->lokasi }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">&nbsp; </p></td>
                <td>&nbsp;</td>
                <td><p class="m-t-0 m-b-0">Kecamatan &nbsp;&nbsp;: {{ $data->kecamatan->n_kecamatan }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">&nbsp;</p></td>
                <td>&nbsp;</td>
                <td><p class="m-t-0 m-b-0">Kelurahan &nbsp;&nbsp;&nbsp;: {{ $data->kelurahan->n_kelurahan }}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">NPWPRD </p></td>
                <td><span>:</span></td>
                <td><p class="m-t-0 m-b-0">{{ $data->opd->npwd }}</p></td>
                <td>&nbsp;</td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0">Jatuh Tempo </p></td>
                <td><span>:</span></td>
                @if ($data->tgl_skrd_akhir != null)
                <td><p class="m-t-0 m-b-0">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->isoFormat('D MMMM Y') }}</p></td>
                @else
                <td><p class="m-t-0 m-b-0">- </p></td>
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
                <th width="50%" class="a">URAIAN RETRIBUSI</th>
                <th width="25%" class="a">JUMLAH (Rp)</th>
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
                    @if ($data->status_diskon == 1)
                    <p class="m-l-5 text-right m-r-10">(Diskon {{ (int)$data->diskon }}%)&nbsp;&nbsp; @currency(((int)$data->diskon / 100) * (int)$data->jumlah_bayar),-</p>
                    @endif
                </td>
            </tr>
            <tr class="a">
                <td rowspan="2" class="a text-center">2</td>
                <td rowspan="2" class="a"><p class="m-l-5">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening_denda : '-' }}</p></td>
                <td class="a">
                    <p class="m-l-5 m-b-0">Jumlah Ketetapan Pokok Retribusi :</p>
                    <p class="m-l-5 m-t-1 m-b-0">Jumlah Sanksi :</p>
                    <p class="m-l-5 m-t-1 m-b-0">a. Bunga</p>
                    <p class="m-l-5 m-t-1">b. Kenaikan</p>
                </td>
                <td class="a">
                    <p class="m-l-5 m-b-0 m-r-10 text-right">@currency((int)$data->denda),-</p>
                    <p class="m-l-5 m-t-1 m-b-0">&nbsp;</p>
                    <p class="m-l-5 m-t-1 m-b-0">&nbsp;</p>
                    <p class="m-l-5 m-t-1">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td class="a"><p class="m-l-5 t-bold">Jumlah Keseluruhan :</p></td>
                <td class="a"><p class="m-l-5 t-bold text-right m-r-10">@currency($total_bayar_final),-</p></td>
            </tr>
            <tr class="a">
                <td colspan="4" class="a">
                    <p class="m-l-5">Dengan Huruf : <span class="t-bold" style="text-transform: uppercase">{{ $terbilang }}</span></p>
                    <p class="fs-14 t-bold m-l-5"><u>PERHATIAN :</u></p>
                    <ol>
                        <li>Penyetoran dilakukan menggunakan Bank Jabar Banten (BJB) melalui Teller/ATM BJB dengan menggunakan <b>{{ $data->no_bayar }}</b></li>
                        <li>Penyetoran melalaui transfer dapat melalui Virtual Account BJB dengan nomor <b>( {{ $data->nomor_va_bjb ? $data->nomor_va_bjb  : '-' }} )</b>, Berlaku sampai {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->isoFormat('D MMMM Y') }}</li>
                        <li>Apabila SKRD ini tidak atau kurang dibayar lewat waktu paling lama 30 hari setelah SKRD diterima atau (tanggal jatuh tempo) sanksi administrasi bunga sebesar 2% per bulan</li>
                    </ol>
                </td>
            </tr>
            <tr class="a">
                <td colspan="1" class="a" style="border-right: none !important; margin-left: 10px !important">
                   {{--  --}}
                </td>
                <td colspan="3" class="a" style="border-left: none !important">
                    <div style="text-align:center; margin-right: -500px !important">
                        @if ($data->tgl_ttd != null)
                        <p>Serpong, {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->isoFormat('D MMMM Y') }}</p>
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
                            <p class="m-t-0">NIP.{{ $data->nip_ttd }}</p>
                        @else
                            <p class="m-t-0">NIP.{{ $data->opd->nip_ttd }}</p>
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="">
        <table class="c">
            <tr class="c">
                <td><p class="m-b-0 fs-12">NTB</p></td>
                <td><p class="m-b-0 fs-12">: {{ $data->ntb != null ? $data->ntb : ''}}</p></td>
            </tr>
            <tr class="c">
                <td><p class="m-t-0 m-b-0 fs-12">TANGGAL BAYAR</p></td>
                @if ($data->tgl_bayar != null)
                <td><p class="m-t-0 m-b-0 fs-12">: {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data->tgl_bayar)->format('d F Y | H:i:s') }}</p></td>
                @else
                <td><p class="m-t-0 m-b-0 fs-12">: </p></td>
                @endif
            </tr>
        </table>
    </div>
</body>
</html>