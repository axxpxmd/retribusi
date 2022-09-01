<?php

namespace App\Http\Controllers;

use Mail;
use DateTime;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

// Models
use App\Models\TransaksiOPD;

class EmailController extends Controller
{
    public function getDiffDays($tgl_skrd_akhir)
    {
        $timeNow = Carbon::now();

        $dateTimeNow = new DateTime($timeNow);
        $expired     = new DateTime($tgl_skrd_akhir . ' 23:59:59');
        $interval    = $dateTimeNow->diff($expired);
        $daysDiff    = $interval->format('%r%a');

        return $daysDiff;
    }

    public function sendEmail($id)
    {
        try {
            $data = TransaksiOPD::find($id);

            //* Data
            $email = $data->email;
            $mailFrom = config('app.mail_from');
            $mailName = config('app.mail_name');

            if ($data->status_bayar == 1) {
                //* File STS
                $tgl_skrd_akhir = $data->tgl_skrd_akhir;
                $total_bayar    = $data->jumlah_bayar;
                $daysDiff = $this->getDiffDays($tgl_skrd_akhir);

                //TODO: Check bunga (STRD)
                if ($daysDiff > 0) {
                    $jumlahBunga = 0;
                    $kenaikan = 0;
                } else {
                    //* Bunga
                    list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);
                }

                //* Total Bayar + Bunga
                if ($data->status_bayar == 1) {
                    $total_bayar = $total_bayar + $data->denda;
                } else {
                    $total_bayar = $total_bayar + $jumlahBunga;
                }

                $terbilang   = Html_number::terbilang($total_bayar) . 'rupiah';

                //TODO: generate QR Code
                $imgQRIS = '';
                if ($data->text_qris) {
                    $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
                    $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
                    $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(1000)->errorCorrection('H')->margin(0)->generate($data->text_qris));
                    $imgQRIS = '<img width="150" src="data:image/png;base64, ' . $b . '" alt="qr code" />';
                }

                //* Tanggal Jatuh Tempo STRD
                if ($data->tgl_strd_akhir == null) {
                    $tgl_jatuh_tempo = $data->tgl_skrd_akhir;
                } else {
                    $tgl_jatuh_tempo = $data->tgl_strd_akhir;
                }

                //TODO: generate QR Code
                $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
                $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
                $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->merge(public_path('images/logo-png.png'), 0.2, true)->size(900)->errorCorrection('H')->margin(0)->generate($file_url));
                $img = '<img width="60" height="61" src="data:image/png;base64, ' . $b . '" alt="qr code" />';

                $pdf = app('dompdf.wrapper');
                $pdf->getDomPDF()->set_option("enable_php", true);
                $pdf->setPaper('legal', 'portrait');

                //TODO: Check status TTD
                if ($data->status_ttd == 1) {
                    $file = 'pages.tandaTangan.reportTTEskrd';
                } elseif ($data->status_ttd == 3) {
                    $file = 'pages.tandaTangan.reportTTEstrd';
                }

                $statusSTS = 1;
                $pdf->loadView($file, compact(
                    'data',
                    'terbilang',
                    'jumlahBunga',
                    'total_bayar',
                    'kenaikan',
                    'tgl_jatuh_tempo',
                    'img',
                    'statusSTS',
                    'imgQRIS'
                ));

                $file = $pdf->output();
            } else {
                //* File SKRD
                $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
                $path_sftp = 'file_ttd_skrd/';
                $file = Storage::disk('sftp')->get($path_sftp . $fileName);
            }

            $dataEmail = array(
                'nama' => $data->nm_wajib_pajak,
                'jumlah_bayar' => 'Rp. ' . number_format($data->jumlah_bayar),
                'tgl_jatuh_tempo' => Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d M Y'),
                'no_bayar' => $data->no_bayar
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
