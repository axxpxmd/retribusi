<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public static function getDiffDays($tgl_skrd_akhir)
    {
        $timeNow = Carbon::now();

        $dateTimeNow = new DateTime($timeNow);
        $expired     = new DateTime($tgl_skrd_akhir . ' 23:59:59');
        $interval    = $dateTimeNow->diff($expired);
        $daysDiff    = $interval->format('%r%a');

        return $daysDiff;
    }
}
