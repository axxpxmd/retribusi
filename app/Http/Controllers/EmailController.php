<?php

namespace App\Http\Controllers;

use PDF;
use Mail;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

// Models
use App\Models\TransaksiOPD;
use Illuminate\Support\Facades\Storage;

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
            $fileName  = "AsipHamdi-17.14.22.00001.pdf";
            $path_sftp = 'file_ttd_skrd/';
            $file = Storage::disk('sftp')->get($path_sftp . $fileName);

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
            dd($th);
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
