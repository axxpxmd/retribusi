<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $data->nm_wajib_pajak }} - {{ $data->no_skrd }}</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/util.css') }}">

    <!-- Font -->
    <link href='https://fonts.googleapis.com/css?family=Open Sans' rel='stylesheet'>

    <style>
        html {
            margin: 30px
        }

        table.d {
            border-collapse: collapse;
            width: 100%
        }

        table.d tr.d,
        th.d,
        td.d {
            table-layout: fixed;
            border: 1px solid black;
            font-size: 12px;
            height: 100;
        }

        table.a tr.a,
        th.a,
        td.a {
            table-layout: fixed;
            border: 1px solid black;
            font-size: 12px;
        }

        table.c {
            font-size: 15px
        }

        .t-bold {
            font-weight: bold
        }

        .m-l-5 {
            margin-left: 5px;
        }

        .m-l-15 {
            margin-left: 15px;
        }

        .m-l-25 {
            margin-left: 25px;
        }

        .m-b-0 {
            margin-bottom: 0px;
        }

        .m-r-10 {
            margin-right: 10px;
        }

        .m-t-0 {
            margin-top: 0px;
        }

        .m-l-5 {
            margin-left: 5px;
        }

        .text-right {
            text-align: right
        }

        .text-center {
            text-align: center
        }

        .m-t-100 {
            margin-top: 100px
        }

        .text-left {
            text-align: left
        }

        .m-l-14 {
            margin-left: 25px
        }

        .m-r-20 {
            margin-right: 20px
        }

        .f-w-n {
            font-weight: normal
        }

        .m-t-1 {
            margin-top: 1px
        }

        .m-l-50 {
            margin-left: 50px;
        }

        .m-t-15 {
            margin-top: 15px
        }

        .m-b-5 {
            margin-bottom: 5px
        }

        .f-normal {
            font-weight: normal
        }

        .mt-n40 {
            margin-top: -30px !important
        }

        .mt-n40 {
            margin-top: -30px !important
        }

        .mt-n15 {
            margin-top: -15px !important
        }

        .fs-10 {
            font-size: 10px
        }

        .t-blue {
            color: blue
        }
    </style>

</head>

<body>
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
                    <p class="text-left m-l-14 m-t-0 f-w-n">Tanggal SKRD : {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->isoFormat('D MMMM Y') }}</p>
                </div>
            </th>
            <th width="20%" class="d">
                <div style="margin: 0 auto">
                    <p class="text-center t-bold m-b-0" style="font-size: 13px">NO SKRD</p>
                    <p class="text-center m-t-1 f-normal">{{ $data->no_skrd }}</p>
                    <table style="font-weight: normal">
                        <tr>
                            <td>No BKU</td>
                            <td>:</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td>-</td>
                        </tr>
                        <tr>
                            <td>Rek</td>
                            <td>:</td>
                            <td>-</td>
                        </tr>
                    </table>
                </div>
            </th>
        </tr>
    </table>

    <div class="m-t-15 m-l-50">
        <table class="c">
            <tr class="c">
                <td>
                    <p class="m-b-0 m-t-0">No Bayar </p>
                </td>
                <td><span>:</span></td>
                <td>
                    <p class="m-b-0 m-t-0">{{ $data->status_ttd == 1 ? $data->no_bayar : '-' }}</p>
                </td>
            </tr>
            <tr class="c">
                <td>
                    <p class="m-t-0 m-b-0">No Pendaftaran </p>
                </td>
                <td><span>:</span></td>
                <td>
                    <p class="m-t-0 m-b-0">{{ $data->nmr_daftar }}</p>
                </td>
            </tr>
            <tr class="c">
                <td>
                    <p class="m-t-0 m-b-0">Nama/Perusahaan </p>
                </td>
                <td><span>:</span></td>
                <td>
                    <p class="m-t-0 m-b-0">{{ $data->nm_wajib_pajak }}</p>
                </td>
            </tr>
            <tr class="c">
                <td>
                    <p class="m-t-0 m-b-0">Alamat </p>
                </td>
                <td><span>:</span></td>
                <td>
                    <p class="m-t-0 m-b-0">{{ $data->alamat_wp }}</p>
                </td>
            </tr>
            <tr class="c">
                <td>
                    <p class="m-t-0 m-b-0">Lokasi </p>
                </td>
                <td><span>:</span></td>
                <td>
                    <p class="m-t-0 m-b-0">{{ $data->lokasi }}</p>
                </td>
            </tr>
            <tr class="c">
                <td>
                    <p class="m-t-0 m-b-0">&nbsp; </p>
                </td>
                <td>&nbsp;</td>
                <td>
                    <table style="font-weight: normal; margin-left: -3px !important">
                        <tr>
                            <td>Kecamatan</td>
                            <td>:</td>
                            <td>{{ $data->kecamatan->n_kecamatan }}</td>
                        </tr>
                        <tr>
                            <td>Kelurahan</td>
                            <td>:</td>
                            <td>{{ $data->kelurahan->n_kelurahan }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="c">
                <td>
                    <p class="m-t-0 m-b-0">NPWPRD </p>
                </td>
                <td><span>:</span></td>
                <td>
                    <p class="m-t-0 m-b-0">{{ $data->opd->npwd }}</p>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr class="c">
                <td>
                    <p class="m-t-0 m-b-0">Jatuh Tempo </p>
                </td>
                <td><span>:</span></td>
                <td>
                    <p class="m-t-0 m-b-0">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->isoFormat('D MMMM Y') }}</p>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </div>

    <div class="m-t-15">
        <table class="d">
            <tr class="a">
                <th width="5%" class="a"><span>NO</span></th>
                <th width="20%" class="a"><span>NOMOR REKENING</span></th>
                <th width="50%" class="a"><span>URAIAN RETRIBUSI</span></th>
                <th width="25%" class="a"><span>JUMLAH (Rp)</span></th>
            </tr>
            <tr class="a">
                <td class="a text-center">1</td>
                <td class="a">
                    <p class="m-l-5">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening : '-' }}</p>
                </td>
                <td class="a">
                    <p class="m-l-5 m-b-0">{{ $data->jenis_pendapatan->jenis_pendapatan }}</p>
                    <p class="m-l-15 m-b-0 m-t-0 m-l-10">{{ $data->rincian_jenis != null ? $data->rincian_jenis->rincian_pendapatan : '-' }}</p>
                    <p class="m-l-25 m-t-0 m-l-15">{{ $data->uraian_retribusi }}</p>
                </td>
                <td class="a">
                    <p class="m-l-5 text-right m-r-10">@currency($data->jumlah_bayar),-</p>
                    @if ($data->status_diskon == 1)
                        <p class="m-l-5 text-right m-r-10">(Diskon {{ (int) $data->diskon }}%)&nbsp;&nbsp;@currency(($data->diskon / 100) * $data->jumlah_bayar),-</p>
                    @endif
                </td>
            </tr>
            <tr class="a">
                <td rowspan="2" class="a text-center">2</td>
                <td rowspan="2" class="a">
                    <p class="m-l-5">{{ $data->rincian_jenis != null ? $data->rincian_jenis->nmr_rekening_denda : '-' }}</p>
                </td>
                <td class="a">
                    <p class="m-l-5 m-b-0">Jumlah Ketetapan Pokok Retribusi :</p>
                    <p class="m-l-5 m-t-1 m-b-0">Jumlah Sanksi :</p>
                    <p class="m-l-5 m-t-1 m-b-0">a. Bunga</p>
                    <p class="m-l-5 m-t-1">b. Kenaikan</p>
                </td>
                <td class="a">
                    <p class="m-l-5 m-b-0 m-r-10 text-right">&nbsp;</p>
                    <p class="m-l-5 m-t-1 m-b-0 m-r-10 text-right">&nbsp;</p>
                    <p class="m-l-5 m-t-1 m-b-0 m-r-10 text-right">-</p>
                    <p class="m-l-5 m-t-1 m-r-10 text-right">-</p>
                </td>
            </tr>
            <tr>
                <td class="a">
                    <p class="m-l-5 t-bold">Jumlah Keseluruhan :</p>
                </td>
                <td class="a">
                    <p class="m-l-5 t-bold text-right m-r-10">@currency($data->total_bayar),-</p>
                </td>
            </tr>
            <tr class="a">
                <td colspan="4" class="a">
                    <p class="m-l-5">Dengan Huruf : <span class="t-bold" style="text-transform: uppercase">{{ $data->jumlah_bayar == 0 ? '0 Rupiah' : $terbilang }}</span>
                    </p>
                    <p class="fs-12 t-bold m-l-5"><u>PERHATIAN :</u></p>
                    <ol>
                        <li>
                            Pembayaran dilakukan di Bank Jabar Banten (BJB) melalui :
                            <ul style="margin-left: -28px !important; list-style-type: armenian">
                                <li>Teller dengan menggunakan kode bayar <b>{{ $data->status_ttd == 1 ? $data->no_bayar : '-' }}</b></li>
                                <li>ATM/Aplikasi BJB DIGI (diginet & digimobile) khusus nasabah bank BJB dengan
                                    mengikuti ketentuan limit transaksi yang berlaku menggunakan kode bayar
                                    <b>{{ $data->status_ttd == 1 ? $data->no_bayar : '-' }}</b></li>
                            </ul>
                        </li>
                        <li>Pembayaran dilakukan melalui transfer VA (virtual account) bank BJB atau transfer antar bank
                            online menggunakan nomor virtual account bank BJB
                            <b>{{ $data->status_ttd == 1 ? $data->nomor_va_bjb : '-' }}</b>. (mengikuti ketentuan limit
                            transaksi transfer yang berlaku, dan tidak berlaku untuk transaksi SKN & RTGS) ,berlaku
                            sampai
                            <b>{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->isoFormat('D MMMM Y') }}</b>.
                        </li>
                        <li>Untuk pembayaran melalui SKN dan RTGS atau yang melebihi limit transaksi transfer online
                            dapat menghubungi perangkat daerah penerbit SKRD.</li>
                        <li>Apabila SKRD ini tidak atau kurang dibayar lewat waktu paling lama 30 hari setelah SKRD
                            diterima atau (tanggal jatuh tempo) sanksi administrasi bunga sebesar 1% per bulan</li>
                    </ol>
                </td>
            </tr>
            <tr class="a">
                <td colspan="1" class="a" style="border-right: none !important; margin-left: 10px !important">
                    @if ($data->status_ttd == 1)
                        @if ($data->text_qris)
                            <div style="margin-top: 10px !important; margin-bottom: 5px !important">
                                <img width="80" class="m-b-5" style="margin-left: 37px !important" src="{{ public_path('images/qr-logo.png') }}" alt="qris"><br>
                                {!! $imgQRIS !!}
                                <br style="margin-top: -30px !important">
                                <span class="m-l-5" style="font-weight: 400; font-size: 12px; font-family: 'Open Sans'">NIMD:{{ $data->rincian_jenis->nmid }}</span>
                            </div>
                        @endif
                    @endif
                </td>
                <td colspan="3" class="a" style="border-left: none !important">
                    <div style="text-align:center; margin-right: -500px !important">
                        <p>Tangerang Selatan, {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->isoFormat('D MMMM Y') }}</p>
                        <br>
                        <br>
                        <br>
                        <br>
                        <p class="m-b-5"><u>{{ $data->nm_ttd }}</u></p>
                        <p class="m-t-0">NIP.{{ $data->nip_ttd }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
