<?php

namespace App\Libraries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\TransaksiOPD;

class GenerateNumber
{
    public static function generate($opd_id, $jenis_pendapatan_id, $jenisGenerate)
    {
        $time  = carbon::now();
        $date  = $time->day;
        $month = $time->month;
        $year  = substr($time->year, 2);

        // Check total retribusi by opd, jenis pendapatan, tahun
        $totalSKRD = TransaksiOPD::where('id_opd', $opd_id)
            ->where('id_jenis_pendapatan', $jenis_pendapatan_id)
            ->where(DB::raw('YEAR(created_at)'), '=', $time->year)
            ->count();
        if ($totalSKRD != 0) {
            $noUrutNoSKRD = $totalSKRD + 1;
        } else {
            $noUrutNoSKRD = '1';
        }

        // Check total retribusi by opd, tahun
        $totalNoBayar = TransaksiOPD::where('id_opd', $opd_id)
            ->where(DB::raw('YEAR(created_at)'), '=', $time->year)
            ->count();
        if ($totalNoBayar != 0) {
            $noUrutNoBayar = $totalNoBayar + 1;
        } else {
            $noUrutNoBayar = '1';
        }

        // ID OPD
        if (\strlen($opd_id) == 1) {
            $generateIdOPD = '0' . $opd_id;
        } elseif (\strlen($opd_id) == 2) {
            $generateIdOPD = $opd_id;
        }

        // ID Jenis Pendapatan
        if (\strlen($jenis_pendapatan_id) == 1) {
            $generateIdJenisPendapatan = '0' . $jenis_pendapatan_id;
        } elseif (\strlen($jenis_pendapatan_id) == 2) {
            $generateIdJenisPendapatan = $jenis_pendapatan_id;
        }

        // Date
        if (\strlen($date) == 1) {
            $generateDay = '0' . $date;
        } elseif (\strlen($date) == 2) {
            $generateDay = $date;
        }

        // Month
        if (\strlen($month) == 1) {
            $generateMonth = '0' . $month;
        } elseif (\strlen($month) == 2) {
            $generateMonth = $month;
        }

        // Nomor Urut SKRD (4 digits)
        if (\strlen($noUrutNoSKRD) == 1) {
            $generateSKRD = '000' . $noUrutNoSKRD;
        } elseif (\strlen($noUrutNoSKRD) == 2) {
            $generateSKRD = '00' . $noUrutNoSKRD;
        } elseif (\strlen($noUrutNoSKRD) == 3) {
            $generateSKRD = '0' . $noUrutNoSKRD;
        } elseif (\strlen($noUrutNoSKRD) == 4) {
            $generateSKRD = $noUrutNoSKRD;
        }

        // Nomor Urut No Bayar (5 digits)
        if (\strlen($noUrutNoBayar) == 1) {
            $generateNoBayar = '0000' . $noUrutNoBayar;
        } elseif (\strlen($noUrutNoBayar) == 2) {
            $generateNoBayar = '000' . $noUrutNoBayar;
        } elseif (\strlen($noUrutNoBayar) == 3) {
            $generateNoBayar = '00' . $noUrutNoBayar;
        } elseif (\strlen($noUrutNoBayar) == 4) {
            $generateNoBayar = '0' . $noUrutNoBayar;
        } elseif (\strlen($noUrutNoBayar) == 5) {
            $generateNoBayar = $noUrutNoBayar;
        }

        // Check Jenis Generate
        if ($jenisGenerate == 'no_skrd') {
            $no_skrd = $generateIdOPD . '.' . $generateIdJenisPendapatan . '.'  . $year . '.'  . $generateSKRD; // id_skpd,id_jenis_pendapatan,tahun,no_urut
            return $no_skrd;
        } else if ($jenisGenerate == 'no_bayar') {
            $no_bayar = $generateDay . $generateMonth .  $year .  $generateIdOPD . $generateNoBayar; // tanggal,bulan,tahun,id_skpd,no_urut
            return $no_bayar;
        }
    }
}
