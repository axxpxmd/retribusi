<?php

namespace App\Http\Services;

use Carbon\Carbon;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;

class WhatsApp
{
    public static function sendSTS($tgl_bayar, $ntb, $chanel_bayar, $total_bayar_bjb, $data, $no_telp)
    {
        //* Send message to WA
        $text = "*TRANSAKSI BERHASIL* 

*Tanggal Bayar* : " . Carbon::parse($tgl_bayar)->format('d F Y | H:i:s') . "
*Nomor Transaksi* : " . $ntb . "
*Metode Pembayaran* : " . $chanel_bayar . "
*Nominal* : Rp. " . number_format($total_bayar_bjb) . "
------------------------------------------------------
*Nama Pelanggan* : " . $data->nm_wajib_pajak . "
*No Pendaftaran* : " . $data->nmr_daftar . "
*No SKRD* : " . $data->no_skrd . "

*Untuk data selengkapnya bisa dilihat pada link dibawah ini*
" . route('sendSTS', Crypt::encrypt($data->id)) . "
";
        Http::post('http://192.168.150.153/api_cfa961a9c00c8d795ab9b9d262fcbb01682185be/public/wa/' . 'send-text', [
            'number'  => $no_telp,
            'api_key' => '1dcb074cfd3dcaaab323082a4ad30e537e07e9de',
            'message' => $text
        ]);
    }
}
