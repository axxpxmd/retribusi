<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

// Models
use App\Models\Utility;
use App\Models\TransaksiOPD;

class UtilityController extends Controller
{
    public function printDataTTD(Request $request, $id)
    {
        $id      = base64_decode($id);
        $data    = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $jumlah_bayar   = $data->jumlah_bayar;
        $status_bayar   = $data->status_bayar;
        $denda          = $data->denda;
        $text_qris      = $data->text_qris;
        $nm_wajib_pajak = $data->nm_wajib_pajak;
        $no_skrd        = $data->no_skrd;
        $tgl_strd_akhir = $data->tgl_strd_akhir;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $tgl_skrd_awal  = $data->tgl_skrd_awal;
        $tgl_bayar      = $data->tgl_bayar;
        $send_sts       = $request->send_sts;

        $fileName = str_replace(' ', '', $nm_wajib_pajak) . '-' . $no_skrd . ".pdf";
        $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_awal, $jumlah_bayar, $tgl_bayar);
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
            }
        }

        //TODO: Total Bayar + Bunga
        $total_bayar = Utility::createDenda($status_bayar, $jumlah_bayar, $denda, $jumlahBunga);

        $terbilang = Html_number::terbilang($total_bayar) . 'rupiah';
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);

        //TODO: generate QR Code (QRIS)
        $imgQRIS = '';
        if ($text_qris) {
            $imgQRIS = Utility::createQrQris($text_qris);
        }

        //TODO: generate QR Code (TTD)
        $img = Utility::createQrTTD($file_url);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'portrait');

        $pdf->loadView('pages.sts.reportTTE', compact(
            'data',
            'terbilang',
            'jumlahBunga',
            'total_bayar',
            'kenaikan',
            'tgl_jatuh_tempo',
            'img',
            'imgQRIS',
            'send_sts'
        ));

        return $pdf->stream($data->nm_wajib_pajak . ' - ' . $data->no_skrd . ".pdf");
    }

    public function getDataSKRD($id)
    {
        $id = base64_decode($id);

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

        return storage::disk('sftp')->download($path_sftp . $fileName);
    }
}
