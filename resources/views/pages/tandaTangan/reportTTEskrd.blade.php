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
        .m-r-10{
            margin-right: 10px;
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

        .f-w-n{
            font-weight: normal
        }
        .m-t-1{
            margin-top: 1px
        }

        .m-l-50{
            margin-left: 50px;
        }
        .m-t-15{
            margin-top: 15px
        }
        .m-b-5{
            margin-bottom: 5px
        }
        .f-normal{
            font-weight: normal
        }
        .mt-n40{
            margin-top: -30px !important
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
                    @if (isset($statusSTS))
                    <p class="m-b-0" style="font-size: 13px">SURAT TANDA SETORAN</p>
                    <p class="m-t-1" style="font-size: 13px">(STS)</p>
                    @else
                    <p class="m-b-0" style="font-size: 13px">SURAT KETETAPAN RETRIBUSI DAERAH</p>
                    <p class="m-t-1" style="font-size: 13px">(SKRD)</p>
                    @endif
                    <p>&nbsp;</p>
                    @if ($data->tgl_skrd_awal != null)
                    <p class="text-left m-l-14 m-t-0 f-w-n">Tanggal SKRD : {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_awal)->format('d F Y') }}</p>
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
                    <p class="text-left f-normal m-l-5 m-b-0 m-t-1">Tanggal &nbsp;: {{ Carbon\Carbon::createFromFormat('Y-m-d', substr($data->tgl_bku,0,10))->format('d F Y') }}</p>
                    @else 
                    <p class="text-left f-normal m-l-5 m-b-0 m-t-1">Tanggal &nbsp;: -</p>
                    @endif
                    <p class="text-left f-normal m-l-5 m-t-1">Rek &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: -</p>
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
                <td><p class="m-t-0 m-b-0">{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d F Y') }}</p></td>
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
                <th width="5%" class="a"><span>NO</span></th>
                <th width="20%" class="a"><span>NOMOR REKENING</span></th>
                <th width="50%" class="a"><span>URAIAN RETRIBUSI</span></th>
                <th width="25%" class="a"><span>JUMLAH (Rp)</span></th>
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
                    <p class="m-l-5 text-right m-r-10">(Diskon {{(int) $data->diskon }}%)&nbsp;&nbsp; @currency(($data->diskon / 100) * $data->jumlah_bayar),-</p>
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
                    <p class="m-l-5 m-b-0 m-r-10 text-right">&nbsp;</p>
                    <p class="m-l-5 m-t-1 m-b-0 m-r-10 text-right">&nbsp;</p>
                    <p class="m-l-5 m-t-1 m-b-0 m-r-10 text-right">-</p>
                    <p class="m-l-5 m-t-1 m-r-10 text-right">-</p>
                </td>
            </tr>
            <tr>
                <td class="a"><p class="m-l-5 t-bold">Jumlah Keseluruhan :</p></td>
                <td class="a"><p class="m-l-5 t-bold text-right m-r-10">@currency($total_bayar),-</p></td>
            </tr>
            <tr class="a">
                <td colspan="4" class="a">
                    <p class="m-l-5">Dengan Huruf : <span class="t-bold" style="text-transform: uppercase">{{ $data->jumlah_bayar == 0 ? '0 Rupiah' : $terbilang }}</span></p>
                    <p class="fs-12 t-bold m-l-5"><u>PERHATIAN :</u></p>
                    <ol style="text-transform: lowercase">
                        <li>
                            PEMBAYARAN DILAKUKAN DI BANK JABAR BANTEN (BJB) MELALUI :
                            <ul style="margin-left: -28px !important; text-transform: lowercase; list-style-type: armenian">
                                <li>TELLER DENGAN MENGGUNAKAN KODE BAYAR <b>{{ $data->no_bayar }}</b></li>
                                <li>ATM/APLIKASI BJB DIGI (DIGINET& DIGIMOBILE) KHUSUS NASABAH BANK BJB DENGAN MENGIKUTI KETENTUAN LIMIT TRANSAKSI YANG BERLAKU MENGGUNAKAN KODE BAYAR <b>{{ $data->no_bayar }}</b></li>
                            </ul>
                        </li>
                        <li>PEMBAYARAN DILAKUKAN MELALUI TRANSFER VA (VIRTUAL ACCOUNT) BANK BJB ATAU TRANSFER ANTAR BANK ONLINE MENGGUNAKAN NOMOR VIRTUAL ACCOUNT BANK BJB <b>{{ $data->nomor_va_bjb }}</b>. (MENGIKUTI KETENTUAN LIMIT TRANSAKSI TRANSFER YANG BERLAKU, DAN TIDAK BERLAKU UNTUK TRANSAKSI SKN & RTGS) ,BERLAKU SAMPAI <b>{{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d F Y') }}</b>.</li>
                        <li>UNTUK PEMBAYARAN MELALUI SKN DAN RTGS ATAU YANG MELEBIHI LIMIT TRANSAKSI TRANSFER ONLINE DAPAT MENGHUBUNGI PERANGKAT DAERAH PENERBIT SKRD.</li>
                        <li>APABILA SKRD INI TIDAK ATAU KURANG DIBAYAR LEWAT WAKTU PALING LAMA 30 HARI SETELAH SKRD DITERIMA ATAU (TANGGAL JATUH TEMPO) SANKSI ADMINISTRASI BUNGA SEBESAR 2% PER BULAN</li>
                    </ol>
                </td>
            </tr>
            <tr class="a">
                <td colspan="1" class="a" style="border-right: none !important; margin-left: 10px !important">
                    @if ($data->text_qris)
                    <div style="margin-top: 10px !important; margin-bottom: 5px !important">
                        <img width="80" class="m-b-5" style="margin-left: 37px !important" src="{{ public_path('images/qr-logo.png') }}" alt="qris"><br>
                        {!! $imgQRIS !!}
                        <br style="margin-top: -30px !important">
                        <span class="m-l-5" style="font-weight: 400; font-size: 12px; font-family: 'Open Sans'">NMID:IDXXXXXXXXXX</span>
                    </div>
                    @endif
                </td>
                <td colspan="3" class="a" style="border-left: none !important">
                    <div style="text-align:center; margin-right: -400px !important">
                        @if ($data->tgl_ttd != null)
                        <p>Tangerang Selatan, {{ Carbon\Carbon::createFromFormat('Y-m-d', $data->tgl_ttd)->format('d F Y') }}</p>
                        @else
                        <p>Tangerang Selatan, </p>
                        @endif
                        <table style="margin-left: 410px !important; margin-top: -8px; margin-bottom: -8px">
                            <tr class="a">
                                <td style="padding: 1px" width="8%" class="a"> {!! $img !!}</td>
                                <td style="padding: 3px" width="92%" class="a">
                                    <p class="m-b-0 m-t-0 fs-10" style="font-style: italic">Telah ditandatangani secara elektronik oleh :</p>
                                    <p class="m-t-0 m-b-0 fs-10 t-blue">{{ $data->nm_ttd }}</p>
                                    <p class="m-t-0 m-b-0 fs-10">Menggunakan Sertifikat Elektronik.</p>
                                    <p class="m-t-0 m-b-0 fs-10">Verifikasi dokumen bisa dilakukan melalui :</p>
                                    <p class="m-t-0 m-b-0 fs-10 t-blue" style="font-style: italic">https://tte.kominfo.go.id/verifyPDF</p>
                                </td>
                            </tr>
                        </table>
                        @if ($data->nm_ttd != null)
                            <p class="m-b-5 m-b-0"><u>{{ $data->nm_ttd }}</u></p>
                        @else 
                            <p class="m-b-5 m-b-0"><u>{{ $data->opd->nm_ttd }}</u></p>
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

    @if (isset($statusSTS))
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
    @endif
</body>
</html>