<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\DB;
use App\Libraries\Html\Html_number;
use Illuminate\Support\Facades\Http;

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

    // public function index()
    // {
    //     $timestamp_now = Carbon::now()->timestamp;
    //     $timestamp_1hour = Carbon::now()->addHour()->timestamp;
    //     $url = 'http://10.31.224.34:23808/oauth/client/token';

    //     $key = "pUyzZIK_YUlX3VqFC5WQJYeqM5A9ceokMFwtOCcb2R0";
    //     $signature = "path=/billing&method=POST&token=hLkZHONXVhnpRE4ai-wZG-igTIZ9Bzz6H1QhutG6TAg&timestamp=1580375822&body={'client_id': 'XXR4SKMQ'}";
    //     $payload = array(
    //         "sub" => "va-online",
    //         "aud" => "access-token",
    //         "iat" => $timestamp_now,
    //         "exp" => $timestamp_1hour
    //     );

    //     // signature get va
    //     $sha256 = hash_hmac('sha256', $signature, $key);
    //     $base64 = base64_encode($sha256);
    //     dd($base64);

    //     // signature get token
    //     $jwt = JWT::encode($payload, $key);
    //     $response = Http::contentType("text/plain")->send('POST', $url, [
    //         'body' => $jwt
    //     ])->json();

    //     dd($response);
    // }

    public function index()
    {
        // Time
        $time  = Carbon::now();
        $date  = $time->toDateString();
        $month = $time->month;
        $day   = Carbon::today();

        $opd_id = Auth::user()->pengguna->opd_id;

        // Card 1
        $jenisOpdIn     = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $transaksiOPD   = OPD::whereIn('id', $jenisOpdIn)->withCount('transaksi_opd')->get();
        $transaksiTotal = TransaksiOPD::count();

        // Card 2 
        $sudahBayar = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id) as total_data"))->where('status_bayar', 1)->where('id_opd', $opd_id)->groupBy('id_opd')->first();
        $belumBayar = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id) as total_data"))->where('status_bayar', 0)->where('id_opd', $opd_id)->groupBy('id_opd')->first();
        // check
        $sudahBayarTotalData  = $sudahBayar != null ? $sudahBayar->total_data : 0;
        $sudahBayarTotalBayar = $sudahBayar != null ? $sudahBayar->total_bayar : 0;
        $belumBayarTotalData  = $belumBayar != null ? $belumBayar->total_data : 0;
        $belumBayarTotalBayar = $belumBayar != null ? $belumBayar->total_bayar : 0;


        $jenisPendapatanOpds = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id_jenis_pendapatan) as jumlah"), 'id_jenis_pendapatan')
            ->where('id_opd', $opd_id)
            ->groupBy('id_jenis_pendapatan')
            ->paginate(5);
        $jenisPendapatanTotal = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id_jenis_pendapatan) as jumlah"))->where('id_opd', $opd_id)->first();
        $jenisPendapatanTotalSudahBayar = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id_jenis_pendapatan) as jumlah"))->where('id_opd', $opd_id)->where('status_bayar', 1)->first();

        $todays     = TransaksiOPD::where('id_opd', $opd_id)->whereDate('created_at', $day)->orderBy('id', 'DESC')->get();
        $todaysskrd = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 0)->whereDate('created_at', $day)->count();
        $todayssts  = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 1)->whereDate('created_at', $day)->count();

        $months     = TransaksiOPD::where('id_opd', $opd_id)->whereRaw('extract(month from created_at) = ?', [$month])->orderBy('id', 'DESC')->get();
        $monthsskrd = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 0)->whereRaw('extract(month from created_at) = ?', [$month])->count();
        $monthssts  = TransaksiOPD::where('id_opd', $opd_id)->where('status_bayar', 1)->whereRaw('extract(month from created_at) = ?', [$month])->count();

        $higherIncome = TransaksiOPD::select(DB::raw("SUM(total_bayar) as y"), 'tmopds.n_opd as name', 'tmopds.n_opd as drilldown', 'id_opd')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->groupBy('id_opd')
            ->orderBy('y', 'DESC')
            ->get();

        foreach ($higherIncome as $key => $value) {
            $color = ['#26a69a', '#26c6da', '#42a5f5', '#ef5350', '#ff7043', '#5c6bc0', '#ffee58', '#bdbdbd', '#66bb6a ', '#ec407a'];

            $parents[$key] = [
                'y'    => $value->y,
                'name' => $value->name,
                'drilldown' => $value->drilldown,
                'id_opd'    => $value->id_opd,
                'color'     => $color[$key]
            ];

            $higherIncomeRetribution = TransaksiOPD::select(DB::raw("SUM(total_bayar) as y"), 'id_jenis_pendapatan', 'id_opd')
                ->where('id_opd', $value->id_opd)
                ->groupBy('id_jenis_pendapatan')
                ->orderBy('y', 'DESC')
                ->get();

            foreach ($higherIncomeRetribution as $key1 => $value1) {
                $dataChills[$key1] = [
                    'name' => $value1->jenis_pendapatan->jenis_pendapatan,
                    'y'    => $value1->y
                ];
            }

            $childs[$key] = [
                'name'  => 'Jenis Pendapatan',
                'id'    => $value1->opd->n_opd,
                'data'  => $dataChills,
                'color' => $color[$key]
            ];
        }
        $parentJson = json_encode($parents);
        $childJson  = json_encode($childs);

        $totalSKRD = TransaksiOPD::where('status_bayar', 0)->count();
        $totalSTS  = TransaksiOPD::where('status_bayar', 1)->count();

        $todayDatas = TransaksiOPD::orderBy('id', 'DESC')->whereDate('created_at', $day)->get();

        return view('home', compact(
            'transaksiOPD',
            'transaksiTotal',
            'sudahBayarTotalData',
            'sudahBayarTotalBayar',
            'belumBayarTotalData',
            'belumBayarTotalBayar',
            'jenisPendapatanOpds',
            'jenisPendapatanTotal',
            'todays',
            'months',
            'todaysskrd',
            'todayssts',
            'monthsskrd',
            'monthssts',
            'parentJson',
            'higherIncome',
            'childJson',
            'totalSKRD',
            'totalSTS',
            'todayDatas',
            'jenisPendapatanTotalSudahBayar'
        ));
    }
}
