<?php

namespace App\Libraries;

use Auth;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Models
use App\Models\Pengguna;
use App\Models\TransaksiOPD;

class GenerateNumber
{
    public static function generate($opd_id, $jenis_pendapatan_id, $jenisGenerate)
    {
        $time  = carbon::now();
        $month = $time->month;
        $year  = substr($time->year, 2);

        //TODO: get id_operator
        $user = Pengguna::select('full_name')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'tmpenggunas.user_id')
            ->where('opd_id', $opd_id)
            ->where('role_id', 9)
            ->get()->toArray();

        $full_name = Auth::user()->pengguna->full_name;
        $status    = false;
        foreach ($user as $key => $i) {
            if ($i['full_name'] == $full_name) {
                $status = true;
                $urutan = $key;
            }
        }

        if ($status) {
            $incr = $full_name == 'Ratna Sari' ? 31 : 1;
            $incr = $full_name == 'Zetro Silaban S.Hut' ? 93 : 1;
            $incr = $full_name == 'Fifi Nur Indahsari, S.T' ? 51 : 1;
            $incr = $full_name == 'Lukman Hakim S.Kom' ? 39 : 1;
            $incr = $full_name == 'Gilang Fakhry R. S.IP' ? 69 : 1;
            $id_operator = $urutan + $incr;
        } else {
            $id_operator = mt_rand(0, 90) + 1;
        }

        if (strlen($id_operator) == 1) {
            $generateIdOperator = '0' . $id_operator;
        } else {
            $generateIdOperator = $id_operator + 1;
        }

        //TODO: Generate no_skrd (Data melanjutkan nomor terakhir)
        $totalSKRD = TransaksiOPD::where('id_opd', $opd_id)
            // ->where('id_jenis_pendapatan', $jenis_pendapatan_id)
            ->where(DB::raw('YEAR(created_at)'), '=', $time->year)
            ->orderBy('id', 'DESC')
            ->first();

        if ($totalSKRD != null) {
            $noUrutNoSKRD = substr($totalSKRD->no_skrd, 9) + 1;
        } else {
            $noUrutNoSKRD = '1';
        }

        //* no_skrd terdiri dari 5 digits
        if (\strlen($noUrutNoSKRD) == 1) {
            $generateSKRD = '0000' . $noUrutNoSKRD;
        } elseif (\strlen($noUrutNoSKRD) == 2) {
            $generateSKRD = '000' . $noUrutNoSKRD;
        } elseif (\strlen($noUrutNoSKRD) == 3) {
            $generateSKRD = '00' . $noUrutNoSKRD;
        } elseif (\strlen($noUrutNoSKRD) == 4) {
            $generateSKRD = '0' . $noUrutNoSKRD;
        } elseif (\strlen($noUrutNoSKRD) == 5) {
            $generateSKRD = $noUrutNoSKRD;
        }

        //TODO: Generate no_bayar
        $totalNoBayar = TransaksiOPD::where('id_opd', $opd_id)
            ->where(DB::raw('YEAR(created_at)'), '=', $time->year)
            ->orderBy('id', 'DESC')
            ->first();

        if ($totalNoBayar != null) {
            $noUrutNoBayar = substr($totalNoBayar->no_bayar, 8) + 1;
        } else {
            $noUrutNoBayar = '1';
        }

        //* no_bayar terdiri dari 5 digits
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

        //TODO: Generate id_opd
        if (\strlen($opd_id) == 1) {
            $generateIdOPD = '0' . $opd_id;
        } elseif (\strlen($opd_id) == 2) {
            $generateIdOPD = $opd_id;
        }

        //TODO: Generate id_jenis_pendapatan
        if (\strlen($jenis_pendapatan_id) == 1) {
            $generateIdJenisPendapatan = '0' . $jenis_pendapatan_id;
        } elseif (\strlen($jenis_pendapatan_id) == 2) {
            $generateIdJenisPendapatan = $jenis_pendapatan_id;
        }

        //TODO: Generate Month
        if (\strlen($month) == 1) {
            $generateMonth = '0' . $month;
        } elseif (\strlen($month) == 2) {
            $generateMonth = $month;
        }

        //TODO: Check jenis return
        if ($jenisGenerate == 'no_skrd') {
            $no_skrd = $generateIdOPD . '.' . $generateIdOperator . '.'  . $year . '.'  . $generateSKRD; // id_skpd,id_operator,tahun,no_urut
            return $no_skrd;
        } else if ($jenisGenerate == 'no_bayar') {
            $no_bayar = $generateIdOperator . $generateMonth .  $year .  $generateIdOPD . $generateNoBayar; // id_operator,bulan,tahun,id_skpd,no_urut
            return $no_bayar;
        }
    }
}
