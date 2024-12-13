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

use Carbon\Carbon;

use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

// Models
use App\Models\Utility;
use App\Models\TransaksiOPD;

class PrintController extends Controller
{
    //* Create Bunga
    public static function createBunga($tgl_skrd_akhir, $total_bayar)
    {
        //TODO: Create Bunga (kenaikan 1% tiap bulan)
        $toDate = Carbon::parse($tgl_skrd_akhir);
        $fromDate = Carbon::now();
        $monthDiff = $toDate->diffInMonths($fromDate);

        $kenaikan = ((int) $monthDiff + 1) * 1;
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

        $text_qris = $data->text_qris;

        //TODO: generate QR Code (QRIS)
        $imgQRIS = '';
        if ($data->text_qris) {
            $imgQRIS = Utility::createQrQris($text_qris);
        }

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'portrait');
        $pdf->loadView('pages.print.skrd', compact(
            'data',
            'terbilang',
            'imgQRIS'
        ));

        return $pdf->stream($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }

    //* STRD
    public function printSTRD($id)
    {
        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        $status_ttd  = $data->status_ttd;
        $text_qris   = $data->text_qris;
        $jumlah_bayar = $data->jumlah_bayar;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $tgl_strd_akhir = $data->tgl_strd_akhir;

        $status_ttd = Utility::checkStatusTTD($status_ttd);

        //* Bunga
        list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);

        //* Total Bayar + Bunga
        $total_bayar = $jumlah_bayar + $jumlahBunga;
        $terbilang   = Html_number::terbilang($total_bayar) . 'rupiah';

        //TODO: generate QR Code (QRIS)
        $imgQRIS = '';
        if ($data->text_qris) {
            $imgQRIS = Utility::createQrQris($text_qris);
        }

        //* Tanggal Jatuh Tempo STRD
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'portrait');
        $pdf->loadView('pages.print.strd', compact(
            'imgQRIS',
            'data',
            'terbilang',
            'jumlahBunga',
            'total_bayar',
            'kenaikan',
            'tgl_jatuh_tempo',
            'status_ttd'
        ));

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
