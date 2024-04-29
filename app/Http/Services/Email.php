<?php

namespace App\Http\Services;

use Carbon\Carbon;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Email
{
    public static function sendSKRD($data)
    {

        $email    = $data->email;
        $mailFrom = config('app.mail_from');
        $mailName = config('app.mail_name');

        $dataEmail = array(
            'nama'     => $data->nm_wajib_pajak,
            'no_bayar' => $data->no_bayar,
            'jumlah_bayar'    => 'Rp. ' . number_format($data->jumlah_bayar),
            'tgl_jatuh_tempo' => Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->isoFormat('D MMMM Y')
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
}
