<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;

// Models
use App\Models\OPD;
use App\Models\OPDJenisPendapatan;
use App\Models\TransaksiOPD;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Time
        $time  = Carbon::now();
        $date  = $time->toDateString();
        $month = $time->month;
        $day = Carbon::today();

        $opd_id = Auth::user()->pengguna->opd_id;

        // Card 1
        $jenisOpdIn = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $transaksiOPD = OPD::whereIn('id', $jenisOpdIn)->withCount('transaksi_opd')->get();
        $transaksiTotal = TransaksiOPD::count();

        // Card 2 
        $sudahBayar = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 1)->count();
        $belumBayar = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 0)->count();

        $jenisPendapatanOpds = OPDJenisPendapatan::where('id_opd', $opd_id)->withCount('transaksi_pendapatan')->orderBy('transaksi_pendapatan_count', 'DESC')->paginate(5);
        $jenisPendapatanTotal = TransaksiOPD::where('id_opd', $opd_id)->count();

        $todays = TransaksiOPD::where('id_opd', $opd_id)->whereDate('created_at', $day)->orderBy('id', 'DESC')->get();
        $todaysskrd = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 0)->whereDate('created_at', $day)->count();
        $todayssts = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 1)->whereDate('created_at', $day)->count();

        $months = TransaksiOPD::where('id_opd', $opd_id)->whereRaw('extract(month from created_at) = ?', [$month])->orderBy('id', 'DESC')->get();
        $monthsskrd = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 0)->whereRaw('extract(month from created_at) = ?', [$month])->count();
        $monthssts = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 1)->whereRaw('extract(month from created_at) = ?', [$month])->count();

        $date = Carbon::create(2021, 8, \rand(1, 30));

        return view('home', compact(
            'transaksiOPD',
            'transaksiTotal',
            'sudahBayar',
            'belumBayar',
            'jenisPendapatanOpds',
            'jenisPendapatanTotal',
            'todays',
            'months',
            'todaysskrd',
            'todayssts',
            'monthsskrd',
            'monthssts'
        ));
    }
}
