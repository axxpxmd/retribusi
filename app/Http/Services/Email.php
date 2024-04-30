<?php

namespace App\Http\Services;

use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Email
{
    public static function sendSKRD($data, $tgl_jatuh_tempo, $email = null)
    {
        $email    = $email == null ? $data->email : $email;
        $mailFrom = config('app.mail_from');
        $mailName = config('app.mail_name');

        $dataEmail = array(
            'nominal' => 'Rp. ' . number_format($data->total_bayar),
            'jatuh_tempo' => Carbon::parse($tgl_jatuh_tempo)->isoFormat('D MMMM Y'),
            'nomor_bayar' => $data->no_bayar,
            'nomor_va' => $data->nomor_va_bjb,
            'nama' => $data->nm_wajib_pajak,
            'no_skrd' => $data->no_skrd,
            'no_pendaftaran' => $data->nmr_daftar,
            'rincian_retribusi' => $data->rincian_jenis->rincian_pendapatan
        );

        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';
        $file      = Storage::disk('sftp')->get($path_sftp . $fileName);

        Mail::send('layouts.mail.skrd', $dataEmail, function ($message) use ($email, $mailFrom, $mailName, $fileName, $file) {
            $message->to($email)->subject('SKRD');
            $message->attachData($file, $fileName);
            $message->from($mailFrom, $mailName);
        });
    }

    public static function sendSTS($data, $email = null)
    {
        $email    = $email == null ? $data->email : $email;
        $mailFrom = config('app.mail_from');
        $mailName = config('app.mail_name');

        $dataEmail = array(
            'nama' => $data->nm_wajib_pajak,
            'tanggal_bayar' => $data->tgl_bayar,
            'nomor_transaksi' => $data->ntb,
            'metode_pembayaran' => $data->chanel_bayar,
            'nominal' => 'Rp. ' . number_format($data->total_bayar_bjb),
            'rincian_retribusi' => $data->rincian_jenis->rincian_pendapatan,
            'link' => route('sendSTS', base64_encode($data->id)),
            'no_skrd' => $data->no_skrd,
            'no_pendaftaran' => $data->nmr_daftar
        );

        Mail::send('layouts.mail.sts', $dataEmail, function ($message) use ($email, $mailFrom, $mailName) {
            $message->to($email)->subject('STS');
            $message->from($mailFrom, $mailName);
        });
    }
}
