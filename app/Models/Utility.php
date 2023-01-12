<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    public static function getDiffDate($tgl_jatuh_tempo, $tgl_bayar = null)
    {
        $startDate = Carbon::parse($tgl_jatuh_tempo);
        $endDate   = $tgl_bayar ? Carbon::parse($tgl_bayar) : Carbon::now();

        $dayDiff = $endDate->diff($startDate)->format('%r%a');
        $monthDiff = $startDate->diffInMonths($endDate);

        return [$dayDiff, $monthDiff];
    }

    public static function checkBunga($dayDiff, $tgl_skrd_akhir, $total_bayar)
    {
        if ($dayDiff >= 0) {
            $kenaikan    = 0;
            $jumlahBunga = 0;
        } else {
            list($jumlahBunga, $kenaikan) = self::createBunga($tgl_skrd_akhir, $total_bayar);
        }

        return [$jumlahBunga, $kenaikan];
    }

    public static function createBunga($tgl_skrd_akhir, $total_bayar, $tgl_bayar = null)
    {
        //TODO: Create Bunga (kenaikan 2% tiap bulan)
        list($dayDiff, $monthDiff) = self::getDiffDate($tgl_skrd_akhir, $tgl_bayar);

        // dd($dayDiff, $monthDiff);
        //TODO: Check status bayar
        if ($tgl_bayar) {
            if ($monthDiff == 0) {
                $kenaikan = 0;
            } else {
                $kenaikan = ((int) $monthDiff) * 2;
            }
        } else {
            $kenaikan = ((int) $monthDiff + 1) * 2;
        }

        $bunga    = $kenaikan / 100;
        $jumlahBunga = $total_bayar * $bunga;

        return [$jumlahBunga, $kenaikan];
    }

    public static function createDenda($status_bayar, $total_bayar, $denda, $jumlahBunga)
    {
        if ($status_bayar == 1) {
            $total_bayar = $total_bayar + $denda;
        } else {
            $total_bayar = $total_bayar + $jumlahBunga;
        }

        return $total_bayar;
    }

    public static function createQrQris($text_qris)
    {
        $data    = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(1000)->errorCorrection('H')->margin(0)->generate($text_qris));
        $imgQRIS = '<img width="150" src="data:image/png;base64, ' . $data . '" alt="qr code" />';

        return $imgQRIS;
    }

    public static function createQrTTD($file_url)
    {
        $data   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->merge(public_path('images/logo-png.png'), 0.2, true)->size(900)->errorCorrection('H')->margin(0)->generate($file_url));
        $img    = '<img width="60" height="61" src="data:image/png;base64, ' . $data . '" alt="qr code" />';

        return $img;
    }

    public static function tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir)
    {
        if ($tgl_strd_akhir == null) {
            $tgl_jatuh_tempo = $tgl_skrd_akhir;
        } else {
            $tgl_jatuh_tempo = $tgl_strd_akhir;
        }

        return $tgl_jatuh_tempo;
    }

    public static function isJatuhTempo($tgl_jatuh_tempo, $dateNow)
    {
        if ($dateNow <= $tgl_jatuh_tempo) {
            $jatuh_tempo = false;
        } else {
            $jatuh_tempo = true;
        }
       
        return $jatuh_tempo;
    }

    public static function checkStatusTTD($status_ttd)
    {
        if ($status_ttd == 1 || $status_ttd == 3) {
            $status_ttd = true;
        } else {
            $status_ttd = false;
        }

        return $status_ttd;
    }
}
