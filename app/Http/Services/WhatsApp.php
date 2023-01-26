<?php

namespace App\Http\Services;

use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class WhatsApp
{
    public static function sendSTS($tgl_bayar, $ntb, $chanel_bayar, $total_bayar_bjb, $data)
    {
        //* Send message to WA
        $text = "*TRANSAKSI BERHASIL* 

*Tanggal Bayar* : " . Carbon::parse($tgl_bayar)->format('d F Y | H:i:s') . "
*Nomor Referensi* : " . $ntb . "
*Metode Pembayaran* : " . $chanel_bayar . "
*Nominal* : Rp. " . number_format($total_bayar_bjb) . "
------------------------------------------------------
*Nama Pelanggan* : " . $data->nm_wajib_pajak . "
*No Bayar* : " . $data->no_bayar . "
*No Pendaftaran* : " . $data->nmr_daftar . "
*No SKRD* : " . $data->no_skrd . "

*Untuk data selengkapnya bisa dilihat pada link dibawah ini*
" . route('sendSTS', Crypt::encrypt($data->id)) . "
";
        Http::post('https://api.visimediatech.com/wa/' . 'send-text', [
            'number'  => '083897229273',
            'api_key' => '1dcb074cfd3dcaaab323082a4ad30e537e07e9de',
            'message' => $text
        ]);
    }
}
