<?php

namespace App\Http\Controllers;

use PDF;
use Mail;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

// Models
use App\Models\TransaksiOPD;

class EmailController extends Controller
{
    public function sendEmail($id)
    {
        try {
            $data = TransaksiOPD::find($id);

            //* Data
            $email = $data->email;
            $mailFrom = config('app.mail_from');
            $mailName = config('app.mail_name');

            //* File
            $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
            $path_sftp = 'file_ttd_skrd/';
            $file      = config('app.sftp_src') . $path_sftp . $fileName;
            $file      = file_get_contents($file);

            $dataEmail = array(
                'nama' => $data->nm_wajib_pajak,
                'file' => $file
            );

            //* Send email
            Mail::send('layouts.mail.skrd', $dataEmail, function ($message) use ($email, $mailFrom, $mailName, $fileName, $file) {
                $message->to($email)->subject('SKRD');
                $message->attachData($file, $fileName);
                $message->from($mailFrom, $mailName);
            });
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => "Terjadi kesalahan saat mengirim email"
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => "Selamat! Email berhasil terkirim kepada ' . $data->nm_wajib_pajak"
        ], 200);
    }
}
