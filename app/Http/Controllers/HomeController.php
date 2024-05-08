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

use App\Models\JenisPendapatan;

use Illuminate\Http\Request;
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
        $this->middleware('checksinglesession');
    }

    public function index(Request $request)
    {
        $time  = Carbon::now();
        $date  = $time->format('Y-m-d');
        $year  = $request->tahun ? $request->tahun : $time->format('Y');
        $role  = Auth::user()->pengguna->modelHasRole->role->name;
        $opd_id = $request->opd_id ? $request->opd_id : Auth::user()->pengguna->opd_id;
        $n_opd  = $request->opd_id ? OPD::select('n_opd', 'id')->where('id', $request->opd_id)->first() : Auth::user()->pengguna->opd;
        $nip    = Auth::user()->pengguna->nip;

        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds = OPD::getAll($opdArray, $opd_id);

        $color = ['#26a69a', '#26c6da', '#42a5f5', '#ef5350', '#ff7043', '#5c6bc0', '#ffee58', '#bdbdbd', '#66bb6a ', '#ec407a', '#42a5f5', '#26a69a', '#ff7043'];

        //* Tabel Target Pendapatan
        $targetPendapatan = JenisPendapatan::select(
            DB::raw("COUNT(tmtransaksi_opd.id) as jumlah"),
            DB::raw("SUM(tmtransaksi_opd.total_bayar) as diterima"),
            DB::raw("SUM(tmtransaksi_opd.total_bayar_bjb - tmtransaksi_opd.jumlah_bayar) as totalDenda"),
            DB::raw("round((SUM(tmtransaksi_opd.total_bayar) / target_pendapatan * 100), 2) as realisasi"),
            'denda',
            'jenis_pendapatan',
            'target_pendapatan',
            'tmopds.initial',
            'tmopds.n_opd'
        )
            ->join('tmtransaksi_opd', 'tmtransaksi_opd.id_jenis_pendapatan', '=', 'tmjenis_pendapatan.id')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where('status_bayar', 1)
            ->groupBy('tmtransaksi_opd.id_jenis_pendapatan')
            ->orderBy('diterima', 'DESC')
            ->get();

        //* Total SKRD, STRD, STS, Wajib Retribusi
        $totalSKRD = TransaksiOPD::select(DB::raw("SUM(tmtransaksi_opd.total_bayar) as total_bayar"), DB::raw("COUNT(tmtransaksi_opd.id) as total_skrd"))
            ->where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '>=', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->first();
        $totalSTS = TransaksiOPD::select(DB::raw("SUM(tmtransaksi_opd.total_bayar) as total_bayar"), DB::raw("COUNT(tmtransaksi_opd.id) as total_skrd"))
            ->where('status_bayar', 1)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->first();
        $totalSTRD = TransaksiOPD::select('id', DB::raw("SUM(tmtransaksi_opd.total_bayar) as total_bayar"), DB::raw("COUNT(tmtransaksi_opd.id) as total_skrd"))
            ->where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '<', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->first();
        $totalKeseluruhan = TransaksiOPD::select(DB::raw("SUM(tmtransaksi_opd.total_bayar) as total_bayar"), DB::raw("COUNT(tmtransaksi_opd.id) as total_skrd"))
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->first();

        $totalRetribusi = TransaksiOPD::select(DB::raw("SUM(tmtransaksi_opd.total_bayar) as total_bayar"), DB::raw("COUNT(tmtransaksi_opd.id) as total_skrd"))->whereYear('created_at', $year)->first();
        $existedOPD = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $totalRetribusiOPD = TransaksiOPD::select(DB::raw("COUNT('id') as total"), DB::raw("SUM(total_bayar) as total_bayar"), 'initial', 'n_opd')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->whereIn('id_opd', $existedOPD)
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->groupBy('id_opd')
            ->get();

        //* Channel Bayar
        $qris = TransaksiOPD::select('chanel_bayar', DB::raw("COUNT('id') as total"), DB::raw("SUM(total_bayar) as total_bayar"))
            ->where('status_bayar', 1)
            ->where('chanel_bayar', 'like', '%qris%')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->get()->toArray();
        $mobileBanking = TransaksiOPD::select('chanel_bayar', DB::raw("COUNT('id') as total"), DB::raw("SUM(total_bayar) as total_bayar"))
            ->where('status_bayar', 1)
            ->where('chanel_bayar', 'like', '%MOBIL%')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->get()->toArray();
        $channelBayar = TransaksiOPD::select('chanel_bayar', DB::raw("COUNT('id') as total"), DB::raw("SUM(total_bayar) as total_bayar"))
            ->where('status_bayar', 1)
            ->whereIn('chanel_bayar', ['Bendahara OPD', 'ATM', 'Lainnya', 'TELLER', 'Transfer RKUD', 'Virtual Account', 'RTGS/SKN'])
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->groupBy('chanel_bayar')
            ->get()->toArray();
        $totalChannelBayar = array_merge($channelBayar, $qris, $mobileBanking);
        $dataPieChartChanelBayar = [];
        foreach ($totalChannelBayar as $key => $i) {
            if ($i['chanel_bayar']) {
                if (str_contains($i['chanel_bayar'], 'QRIS')) {
                    $chanel_bayar = 'QRIS';
                } else if (str_contains($i['chanel_bayar'], 'Virtual Account')) {
                    $chanel_bayar = 'VA';
                } else {
                    $chanel_bayar = $i['chanel_bayar'];
                }

                $dataPieChartChanelBayar[$key] = [
                    'y'    => $i['total'],
                    'name' => $chanel_bayar,
                    'color' => $color[$key]
                ];
            }
        }
        $dataPieChartChanelBayar = json_encode($dataPieChartChanelBayar);

        //* Notifikasi
        $skrdToday = TransaksiOPD::when($opd_id != 0, function ($q) use ($opd_id) {
            $q->where('tmtransaksi_opd.id_opd', $opd_id);
        })->where('tgl_skrd_awal', $date)->count();
        $stsToday = TransaksiOPD::when($opd_id != 0, function ($q) use ($opd_id) {
            $q->where('tmtransaksi_opd.id_opd', $opd_id);
        })->whereDate('tgl_bayar', $time)
            ->where('status_bayar', 1)->count();
        $strdToday = TransaksiOPD::when($opd_id != 0, function ($q) use ($opd_id) {
            $q->where('tmtransaksi_opd.id_opd', $opd_id);
        })->where('tgl_skrd_akhir', '<', $date)
            ->where('status_bayar', 0)->count();
        $tandaTanganToday = TransaksiOPD::when($opd_id != 0, function ($q) use ($opd_id) {
            $q->where('tmtransaksi_opd.id_opd', $opd_id);
        })->where('tgl_ttd', $date)
            ->when($nip, function ($q) use ($nip) {
                $q->where('nip_ttd', $nip);
            })
            ->whereIn('status_ttd', [2, 4])->count();

        //* Diagram Chart (Role: super-admin)
        $pendapatanOPD = TransaksiOPD::select(DB::raw("SUM(total_bayar) as y"), 'tmopds.n_opd as name', 'id_opd', 'tmtransaksi_opd.id as id')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->where('status_bayar', 1)
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->groupBy('id_opd')
            ->orderBy('y', 'DESC')
            ->get();

        $parents = [];
        $childs  = [];

        foreach ($pendapatanOPD as $keyOPD => $opd) {
            $parents[$keyOPD] = [
                'name' => $opd->name,
                'y'    => $opd->y,
                'drilldown' => $opd->name,
                'color'     => $color[$keyOPD]
            ];
        }

        $parentJson = json_encode($parents);
        $childJson  = json_encode($childs);

        //* Pembayaran Hari ini
        $pembayaranHariIni = TransaksiOPD::select('tmtransaksi_opd.id as id', 'no_bayar', 'no_skrd', 'initial', 'n_opd', 'tgl_bayar', 'total_bayar', 'chanel_bayar')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })->whereDate('tgl_bayar', $time)
            ->orderBy('tgl_bayar', 'DESC')->get();

        //* Chart Pendapatan per Tahun
        $retribusiPerTahun = TransaksiOPD::select(DB::raw('YEAR(created_at) as tahun'), DB::raw("SUM(total_bayar) as total_bayar"))
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where('status_bayar', 1)
            ->whereIn(DB::raw('YEAR(created_at)'),  [2021, 2022, 2023, 2024])
            ->groupBy('tahun')->get();
        $tahunMulai = count($retribusiPerTahun) != 0 ? $retribusiPerTahun[0]['tahun'] : 2024;
        $parentsRetribusiPerTahun = [];
        foreach ($retribusiPerTahun as $keyretribusiPerTahun => $retribusiPerTahun) {
            $parentsRetribusiPerTahun[$keyretribusiPerTahun] = $retribusiPerTahun->total_bayar;
        }
        $parentJsonRetribusiPerTahun = json_encode($parentsRetribusiPerTahun);

        return view('home', compact(
            'totalSKRD',
            'targetPendapatan',
            'year',
            'n_opd',
            'totalSTS',
            'totalSTRD',
            'totalKeseluruhan',
            'opds',
            'opd_id',
            'totalRetribusiOPD',
            'totalRetribusi',
            'totalChannelBayar',
            'skrdToday',
            'stsToday',
            'strdToday',
            'role',
            'dataPieChartChanelBayar',
            'tandaTanganToday',
            'parentJson',
            'childJson',
            'pembayaranHariIni',
            'parentJsonRetribusiPerTahun',
            'tahunMulai'
        ));
    }
}
