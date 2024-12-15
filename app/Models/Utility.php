<?php

namespace App\Models;

use Carbon\Carbon;

use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    public static function generateNewJatuhTempo($tgl_skrd_akhir, $tgl_bayar = null)
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $endDate = $tgl_bayar ? $tgl_bayar : $dateNow;

        $tgl_jatuh_tempo_baru = Carbon::createFromFormat('Y-m-d', $tgl_skrd_akhir)->addDays(30)->format('Y-m-d');
        while ($tgl_jatuh_tempo_baru <=  $endDate) {
            $tgl_jatuh_tempo_baru = Carbon::createFromFormat('Y-m-d', $tgl_jatuh_tempo_baru)->addDays(30)->format('Y-m-d');
        }

        return $tgl_jatuh_tempo_baru;
    }

    public static function getDiffDate($tgl_skrd_akhir, $tgl_strd_akhir = null)
    {
        $startDate = Carbon::parse($tgl_skrd_akhir);
        $endDate   = Carbon::parse($tgl_strd_akhir);

        $dayDiff = $startDate->diff($endDate)->format('%r%a');
        $monthDiff = $dayDiff / 30;

        return [$dayDiff, $monthDiff];
    }

    public static function createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar = null, $percent = 1)
    {
        //TODO: Generate New Jatuh Tempo
        $tgl_jatuh_tempo_baru = self::generateNewJatuhTempo($tgl_skrd_akhir, $tgl_bayar);

        //TODO: Get total ketelambatan
        list($dayDiff, $monthDiff) = self::getDiffDate($tgl_skrd_akhir, $tgl_jatuh_tempo_baru);

        //TODO: Create Bunga (kenaikan 1% tiap bulan)
        $kenaikan = ((int) $monthDiff) * $percent;

        $bunga = $kenaikan / 100;
        $jumlahBunga = $jumlah_bayar * $bunga;

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

    public static function isJatuhTempo($tgl_jatuh_tempo)
    {
        $dateNow = Carbon::now()->format('Y-m-d');

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

    public static function checkDenda($denda)
    {
        if ($denda == 0 || $denda == null) {
            $status_denda = 0;
        } else {
            $status_denda = 1;
        }

        return $status_denda;
    }
}
