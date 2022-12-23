<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of welcome
 *
 * @author Asip Hamdi
 * Github : axxpxmd
 */

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;

use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

// Models
use App\Models\TransaksiOPD;

class PrintController extends Controller
{
    //* Create Bunga
    public static function createBunga($tgl_skrd_akhir, $total_bayar)
    {
        //TODO: Create Bunga (kenaikan 2% tiap bulan)
        $toDate = Carbon::parse($tgl_skrd_akhir);
        $fromDate = Carbon::now();
        $monthDiff = $toDate->diffInMonths($fromDate);

        $kenaikan = ((int) $monthDiff + 1) * 2;
        $bunga    = $kenaikan / 100;
        $jumlahBunga = $total_bayar * $bunga;

        return [$jumlahBunga, $kenaikan];
    }

    //* SKRD
    public function printSKRD($id)
    {
        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);
        $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

        //TODO: generate QR Code
        $img = '';
        if ($data->text_qris) {
            $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
            $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
            $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(1000)->errorCorrection('H')->margin(0)->generate($data->text_qris));
            $img = '<img width="150" src="data:image/png;base64, ' . $b . '" alt="qr code" />';
        }

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'portrait');
        $pdf->loadView('pages.print.skrd', compact(
            'data',
            'terbilang',
            'img'
        ));

        return $pdf->stream($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }

    //* STRD
    public function printSTRD($id)
    {
        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        //* Bunga
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);

        //* Total Bayar + Bunga
        $total_bayar = $data->total_bayar + $jumlahBunga;
        $terbilang   = Html_number::terbilang($total_bayar) . 'rupiah';

        //TODO: generate QR Code
        $img = '';
        if ($data->text_qris) {
            $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
            $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
            $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(1000)->errorCorrection('H')->margin(0)->generate($data->text_qris));
            $img = '<img width="150" src="data:image/png;base64, ' . $b . '" alt="qr code" />';
        }

        //* Tanggal Jatuh Tempo STRD
        if ($data->tgl_strd_akhir == null) {
            $tgl_jatuh_tempo = $data->tgl_skrd_akhir;
        } else {
            $tgl_jatuh_tempo = $data->tgl_strd_akhir;
        }

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'portrait');
        $pdf->loadView('pages.print.strd', compact(
            'img',
            'data',
            'terbilang',
            'jumlahBunga',
            'total_bayar',
            'kenaikan',
            'tgl_jatuh_tempo'
        ));

        return $pdf->stream($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }

    //* STS
    public function printSTS($id)
    {
        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);

        $dateNow   = Carbon::now()->format('Y-m-d');
        $statusSTS = 1;

        //* SKRD (sts)
        if ($data->tgl_skrd_akhir >= $dateNow) {
            $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

            //TODO: generate QR Code
            $img = '';
            if ($data->text_qris) {
                $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
                $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
                $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(1000)->errorCorrection('H')->margin(0)->generate($data->text_qris));
                $img = '<img width="150" src="data:image/png;base64, ' . $b . '" alt="qr code" />';
            }

            $pdf = app('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('legal', 'portrait');
            $pdf->loadView('pages.print.skrd', compact(
                'img',
                'data',
                'terbilang',
                'statusSTS'
            ));
        }

        //* STRD (sts)
        if ($data->tgl_skrd_akhir < $dateNow) {
            //* Bunga
            $tgl_skrd_akhir = $data->tgl_skrd_akhir;
            $total_bayar = $data->jumlah_bayar;
            list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);

            //* Total Bayar + Bunga
            if ($data->status_bayar == 1) {
                $total_bayar = $data->total_bayar_bjb;
            } else {
                $total_bayar = $data->total_bayar + $jumlahBunga;
            }
            $terbilang   = Html_number::terbilang($total_bayar) . 'rupiah';

            //TODO: generate QR Code
            $img = '';
            if ($data->text_qris) {
                $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
                $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
                $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(1000)->errorCorrection('H')->margin(0)->generate($data->text_qris));
                $img = '<img width="150" src="data:image/png;base64, ' . $b . '" alt="qr code" />';
            }

            //* Tanggal Jatuh Tempo STRD
            if ($data->tgl_strd_akhir == null) {
                $tgl_jatuh_tempo = $data->tgl_skrd_akhir;
            } else {
                $tgl_jatuh_tempo = $data->tgl_strd_akhir;
            }

            $pdf = app('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('legal', 'portrait');
            $pdf->loadView('pages.print.strd', compact(
                'img',
                'data',
                'terbilang',
                'jumlahBunga',
                'total_bayar',
                'kenaikan',
                'tgl_jatuh_tempo',
                'statusSTS'
            ));
        }

        return $pdf->stream($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }

    public function download($id)
    {
        $data = TransaksiOPD::find($id);

        $path_sftp = 'file_ttd_skrd/';
        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $timeNow   = Carbon::now()->format('Y-m-d H:i:s');

        //TODO: Update Jumlah Cetak
        $jumlah_cetak = $data->jumlah_cetak + 1;
        $data->update([
            'jumlah_cetak'    => $jumlah_cetak,
            'tgl_cetak_trkhr' => $timeNow
        ]);

        return Storage::disk('sftp')->download($path_sftp . $fileName);
    }
}
