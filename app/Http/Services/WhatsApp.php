<?php

namespace App\Http\Services;

use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class WhatsApp
{
    public static function sendSTS($tgl_bayar, $ntb, $chanel_bayar, $total_bayar_bjb, $data)
    {
        $endpoint = config('app.wagateway_ipserver');
        $api_key  = config('app.wagateway_apikey');
        $link = route('sendSTS', base64_encode($data->id));

        //* Send message to WA
        $text = "*PEMBAYARAN RETRIBUSI BERHASIL* 

Untuk *" . $data->rincian_jenis->rincian_pendapatan . "*

*Tanggal Bayar* : " . Carbon::parse($tgl_bayar)->format('d F Y | H:i:s') . "
*Nomor Transaksi* : " . $ntb . "
*Metode Pembayaran* : " . $chanel_bayar . "
*Nominal* : Rp. " . number_format($total_bayar_bjb) . "
------------------------------------------------------
*Nama Pelanggan* : " . $data->nm_wajib_pajak . "
*No Pendaftaran* : " . $data->nmr_daftar . "
*No SKRD* : " . $data->no_skrd . "

*Untuk data selengkapnya bisa dilihat pada link dibawah ini*.
" . $link . "

Retribusi, Tangerang Selatan.
";
        Http::post($endpoint . 'send-text', [
            'number'  => $data->no_telp,
            'api_key' => $api_key,
            'message' => $text
        ]);
    }

    public static function sendSKRD($data, $tgl_jatuh_tempo)
    {
        $endpoint = config('app.wagateway_ipserver');
        $api_key  = config('app.wagateway_apikey');
        $link = route('sendSKRD', base64_encode($data->id));

        //* Send message to WA
        $text = "*TAGIHAN PEMBAYARAN RETRIBUSI* 

Untuk *" . $data->rincian_jenis->rincian_pendapatan . "*

*Nominal* : Rp. " . number_format($data->total_bayar) . "
*Jatuh Tempo* : " . Carbon::parse($tgl_jatuh_tempo)->isoFormat('D MMMM Y') . "
*Nomor Bayar* : " . $data->no_bayar . "
*Nomor VA* : " . $data->nomor_va_bjb . "
------------------------------------------------------
*Nama Pelanggan* : " . $data->nm_wajib_pajak . "
*No Pendaftaran* : " . $data->nmr_daftar . "
*No SKRD* : " . $data->no_skrd . "

*PERHATIAN!*
*Lakukan pembayaran sebelum tanggal jatuh tempo untuk menghindari denda.*

*Untuk data selengkapnya bisa dilihat pada link dibawah ini*.
" . $link . "

Retribusi, Tangerang Selatan.
";
        Http::post($endpoint . 'send-text', [
            'number'  => $data->no_telp,
            'api_key' => $api_key,
            'message' => $text
        ]);
    }
}
