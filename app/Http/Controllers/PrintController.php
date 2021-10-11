<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;

use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

// Models
use App\Models\TransaksiOPD;

class PrintController extends Controller
{
    public function printSKRD($id)
    {
        $data = TransaksiOPD::find($id);
        $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pages.skrd.report', compact(
            'data',
            'terbilang'
        ));

        return $pdf->stream($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }

    public static function createBunga($tgl_skrd_akhir, $total_bayar)
    {
        //TODO: Create Bunga (kenaikan 2% tiap bulan)
        $timeNow     = Carbon::now();
        $dateTimeNow = new DateTime($timeNow);
        $expired     = new DateTime($tgl_skrd_akhir . ' 23:59:59');
        $interval    = $dateTimeNow->diff($expired);
        $monthDiff   = $interval->format('%m');

        $kenaikan = ((int) $monthDiff + 1) * 2;
        $bunga    = $kenaikan / 100;
        $jumlahBunga = $total_bayar * $bunga;

        return [$jumlahBunga, $kenaikan];
    }

    public function printSTS($id)
    {
        $data = TransaksiOPD::find($id);

        //* Bunga
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        list($jumlahBunga, $kenaikan) = $this->createBunga($tgl_skrd_akhir, $total_bayar);

        //* Total Bayar + Bunga
        $total_bayar = $data->total_bayar + $jumlahBunga;
        $terbilang   = Html_number::terbilang($total_bayar) . 'rupiah';

        //* Tanggal Jatuh Tempo STRD
        if ($data->tgl_strd_akhir == null) {
            $tgl_jatuh_tempo = $data->tgl_skrd_akhir;
        } else {
            $tgl_jatuh_tempo = $data->tgl_strd_akhir;
        }

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pages.strd.report', compact(
            'data',
            'terbilang',
            'jumlahBunga',
            'total_bayar',
            'kenaikan',
            'tgl_jatuh_tempo'
        ));

        return $pdf->stream($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }
}
