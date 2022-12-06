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

use Auth;
use Carbon\Carbon;

use App\Models\DataWP;
use App\Models\JenisPendapatan;

use Illuminate\Support\Facades\DB;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;

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
        $day   = Carbon::today();
        $dateNow = $time->format('Y-m-d');

        // Check opd_id
        $opd_id = Auth::user()->pengguna->opd_id;

        //* Tabel Target Pendapatan
        $targetPendapatan = JenisPendapatan::select(DB::raw("SUM(tmtransaksi_opd.total_bayar_bjb) as diterima"), DB::raw("SUM(tmtransaksi_opd.total_bayar) as ketetapan"), 'jenis_pendapatan', 'target_pendapatan')
            ->join('tmtransaksi_opd', 'tmtransaksi_opd.id_jenis_pendapatan', '=', 'tmjenis_pendapatan.id')
            ->where('tmtransaksi_opd.total_bayar_bjb', '!=', 0)
            ->where(DB::raw('YEAR(created_at)'), '=', $time->year)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->groupBy('tmtransaksi_opd.id_jenis_pendapatan')
            ->orderBy('diterima', 'DESC')
            ->get();

        //* Total SKRD, STRD, STS, Wajib Retribusi
        $totalSKRD = TransaksiOPD::where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '>=', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->count();
        $totalSKRDduit = TransaksiOPD::where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '>=', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->sum('total_bayar');
        $totalSTRD = TransaksiOPD::where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '<', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->count();
        $totalSTRDduit = TransaksiOPD::where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '<', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->sum('total_bayar');
        $totalSTS  = TransaksiOPD::where('status_bayar', 1)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->count();
        $totalSTSduit  = TransaksiOPD::where('status_bayar', 1)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->sum('total_bayar');
        $totalWR   = TransaksiOPD::when($opd_id != 0, function ($q) use ($opd_id) {
            $q->where('tmtransaksi_opd.id_opd', $opd_id);
        })
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->count();
        $totalWRduit   = TransaksiOPD::when($opd_id != 0, function ($q) use ($opd_id) {
            $q->where('tmtransaksi_opd.id_opd', $opd_id);
        })
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->sum('total_bayar');

        //* Total Retribsui / Dinas
        $totalRetribusi = TransaksiOPD::whereYear('created_at', $time->year)->count();
        $existedOPD     = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $totalRetribusiOPD = TransaksiOPD::whereIn('id_opd', $existedOPD)->groupBy('id_opd')->get();
        $totalRetribusiOPD = OPD::whereIn('id', $existedOPD)->withCount('transaksi_opd')->get();

        //* Diagram Chart (Role: super-admin)
        $higherIncome = TransaksiOPD::select(DB::raw("SUM(total_bayar) as y"), 'tmopds.n_opd as name', 'tmopds.n_opd as drilldown', 'id_opd')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
            ->groupBy('id_opd')
            ->orderBy('y', 'DESC')
            ->get();

        $parents = [];
        $childs  = [];

        foreach ($higherIncome as $key => $value) {
            $color = ['#26a69a', '#26c6da', '#42a5f5', '#ef5350', '#ff7043', '#5c6bc0', '#ffee58', '#bdbdbd', '#66bb6a ', '#ec407a', '#42a5f5', '#26a69a', '#ff7043'];

            $parents[$key] = [
                'y'    => $value->y,
                'name' => $value->name,
                'drilldown' => $value->drilldown,
                'id_opd'    => $value->id_opd,
                'color'     => $color[$key]
            ];

            $higherIncomeRetribution = TransaksiOPD::select(DB::raw("SUM(total_bayar) as y"), 'id_jenis_pendapatan', 'id_opd')
                ->where('id_opd', $value->id_opd)
                ->where(DB::raw('YEAR(tmtransaksi_opd.created_at)'), '=', $time->year)
                ->groupBy('id_jenis_pendapatan')
                ->orderBy('y', 'DESC')
                ->get();

            foreach ($higherIncomeRetribution as $key1 => $value1) {
                $dataChills[$key1] = [
                    'name' => $value1->jenis_pendapatan->jenis_pendapatan,
                    'y'    => $value1->y
                ];

                $childs[$key] = [
                    'name'  => 'Jenis Pendapatan',
                    'id'    => $value1->opd->n_opd,
                    'data'  => $dataChills,
                    'color' => $color[$key]
                ];
            }
        }
        $parentJson = json_encode($parents);
        $childJson  = json_encode($childs);

        return view('home', compact(
            'targetPendapatan',
            'totalSKRD',
            'totalSKRDduit',
            'totalSTRD',
            'totalSTRDduit',
            'totalSTS',
            'totalSTSduit',
            'totalWR',
            'totalRetribusi',
            'totalRetribusiOPD',
            'parentJson',
            'childJson',
            'time',
            'totalWRduit'
        ));
    }
}
