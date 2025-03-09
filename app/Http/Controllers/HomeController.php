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

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Models\JenisPendapatan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

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
        // $this->middleware('checkopd');
    }

    public function index(Request $request)
    {
        $time  = Carbon::now();
        $date  = $time->format('Y-m-d');
        $year  = $request->tahun ? $request->tahun : $time->format('Y');
        $role  = Auth::user()->pengguna->modelHasRole->role->name;
        $opd_id = $request->opd_id ? Crypt::decrypt($request->opd_id) : Auth::user()->pengguna->opd_id;
        $n_opd  = $request->opd_id ? OPD::select('n_opd', 'id')->where('id', Crypt::decrypt($request->opd_id))->first() : Auth::user()->pengguna->opd;
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
            ->whereYear('tmtransaksi_opd.tgl_skrd_awal', $year)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where('status_bayar', 1)
            ->groupBy('tmtransaksi_opd.id_jenis_pendapatan')
            ->orderBy('diterima', 'DESC')
            ->get();

        //* Total SKRD, STRD, STS, Wajib Retribusi
        $totalSKRD = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id) as total_skrd"))
            ->where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '>=', $date)
            ->when($opd_id != 0, function ($query) use ($opd_id) {
                $query->where('id_opd', $opd_id);
            })
            ->whereYear('tgl_skrd_awal', $year)
            ->first();
        $totalSTS = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id) as total_skrd"))
            ->where('status_bayar', 1)
            ->when($opd_id != 0, function ($query) use ($opd_id) {
                $query->where('id_opd', $opd_id);
            })
            ->whereYear('tgl_skrd_awal', $year)
            ->first();
        $totalSTRD = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id) as total_skrd"))
            ->where('status_bayar', 0)
            ->where('tgl_skrd_akhir', '<', $date)
            ->when($opd_id != 0, function ($query) use ($opd_id) {
                $query->where('id_opd', $opd_id);
            })
            ->whereYear('tgl_skrd_awal', $year)
            ->first();
        $totalKeseluruhan = TransaksiOPD::select(DB::raw("SUM(total_bayar) as total_bayar"), DB::raw("COUNT(id) as total_skrd"))
            ->when($opd_id != 0, function ($query) use ($opd_id) {
                $query->where('id_opd', $opd_id);
            })
            ->whereYear('tgl_skrd_awal', $year)
            ->first();

        $totalRetribusi = TransaksiOPD::select(DB::raw("SUM(tmtransaksi_opd.total_bayar) as total_bayar"), DB::raw("COUNT(tmtransaksi_opd.id) as total_skrd"))->whereYear('tgl_skrd_awal', $year)->first();
        $existedOPD = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $totalRetribusiOPD = TransaksiOPD::select(DB::raw("COUNT('id') as total"), DB::raw("SUM(total_bayar) as total_bayar"), 'initial', 'n_opd')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->whereIn('id_opd', $existedOPD)
            ->whereYear('tmtransaksi_opd.tgl_skrd_awal', $year)
            ->groupBy('id_opd')
            ->get();

        //* Channel Bayar
        $channels = [
            'channelBayar' => ['Bendahara OPD', 'ATM', 'Lainnya', 'TELLER', 'Transfer RKUD', 'Virtual Account', 'RTGS/SKN'],
            'qris' => 'qris',
            'mobileBanking' => 'MOBIL'
        ];

        $channelBayarData = [];
        foreach ($channels as $key => $value) {
            $query = TransaksiOPD::select('chanel_bayar', DB::raw("COUNT('id') as total"), DB::raw("SUM(total_bayar) as total_bayar"))
                ->where('status_bayar', 1)
                ->when($opd_id != 0, function ($q) use ($opd_id) {
                    $q->where('tmtransaksi_opd.id_opd', $opd_id);
                })
                ->whereYear('tmtransaksi_opd.tgl_skrd_awal', $year);

            if (is_array($value)) {
                $query->whereIn('chanel_bayar', $value)->groupBy('chanel_bayar');
            } else {
                $query->where('chanel_bayar', 'like', '%' . $value . '%');
            }
            $channelBayarData[$key] = $query->get()->toArray();
        }

        $totalChannelBayar = array_merge(...array_values($channelBayarData));
        $dataPieChartChanelBayar = collect($totalChannelBayar)->map(function ($item, $key) use ($color) {
            if ($item['chanel_bayar']) {
                if (str_contains($item['chanel_bayar'], 'QRIS')) {
                    $chanel_bayar = 'QRIS';
                } elseif (str_contains($item['chanel_bayar'], 'Virtual Account')) {
                    $chanel_bayar = 'VA';
                } else {
                    $chanel_bayar = $item['chanel_bayar'];
                }

                return [
                    'y'    => $item['total'],
                    'name' => $chanel_bayar,
                    'color' => $color[$key] ?? '#000000'
                ];
            }
        })->filter()->values()->toJson();

        //* Notifikasi
        $skrdToday = TransaksiOPD::where('tgl_skrd_awal', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })->count();
        $stsToday = TransaksiOPD::whereDate('tgl_bayar', $time)
            ->where('status_bayar', 1)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })->count();
        $strdToday = TransaksiOPD::where('tgl_skrd_akhir', '<', $date)
            ->where('status_bayar', 0)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })->count();
        $tandaTanganToday = TransaksiOPD::where('tgl_ttd', $date)
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id_opd', $opd_id);
            })
            ->when($nip, function ($q) use ($nip) {
                $q->where('nip_ttd', $nip);
            })
            ->whereIn('status_ttd', [2, 4])->count();

        //* Diagram Chart (Role: super-admin)
        $dataOpd = TransaksiOPD::select(DB::raw("SUM(total_bayar) as y"), 'tmopds.n_opd as name', 'tmopds.n_opd as drilldown', 'id_opd')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->whereYear('tmtransaksi_opd.created_at', $year)
            ->groupBy('id_opd')
            ->orderBy('y', 'DESC')
            ->get();

        $parents = [];
        $childs  = [];
        $color = ['#26a69a', '#26c6da', '#42a5f5', '#ef5350', '#ff7043', '#5c6bc0', '#ffee58', '#bdbdbd', '#66bb6a', '#ec407a'];

        foreach ($dataOpd as $key => $value) {
            $parents[] = [
                'y'    => $value->y,
                'name' => $value->name,
                'drilldown' => $value->drilldown,
                'id_opd'    => $value->id_opd,
                'color'     => $color[$key % count($color)]
            ];

            $dataJenisPendapatan = TransaksiOPD::select(DB::raw("SUM(total_bayar) as y"), 'id_jenis_pendapatan', 'id_opd')
                ->where('id_opd', $value->id_opd)
                ->whereYear('tmtransaksi_opd.created_at', $year)
                ->groupBy('id_jenis_pendapatan')
                ->orderBy('y', 'DESC')
                ->get();

            $dataChills = [];
            foreach ($dataJenisPendapatan as $value1) {
                $dataChills[] = [
                    'name' => $value1->jenis_pendapatan->jenis_pendapatan,
                    'y'    => $value1->y
                ];
            }

            $childs[] = [
                'name'  => 'Jenis Pendapatan',
                'id'    => $value->name,
                'data'  => $dataChills,
                'color' => $color[$key % count($color)]
            ];
        }

        $parentJson = json_encode($parents);
        $childJson  = json_encode($childs);

        //* Pembayaran Hari ini
        $pembayaranHariIni = TransaksiOPD::select('tmtransaksi_opd.id as id', 'no_bayar', 'no_skrd', 'initial', 'n_opd', 'tgl_bayar', 'total_bayar', 'chanel_bayar')
            ->join('tmopds', 'tmopds.id', '=', 'tmtransaksi_opd.id_opd')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->whereDate('tgl_bayar', $time)
            ->orderBy('tgl_bayar', 'DESC')->get();

        //* Chart Pendapatan per Tahun
        $retribusiPerTahun = TransaksiOPD::select(DB::raw('YEAR(tgl_skrd_awal) as tahun'), DB::raw("SUM(total_bayar) as total_bayar"))
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('tmtransaksi_opd.id_opd', $opd_id);
            })
            ->where('status_bayar', 1)
            ->whereIn(DB::raw('YEAR(tgl_skrd_awal)'), range(2021, 2025))
            ->groupBy('tahun')
            ->get();
        $tahunMulai = $retribusiPerTahun->isNotEmpty() ? $retribusiPerTahun->first()->tahun : 2025;
        $parentsRetribusiPerTahun = $retribusiPerTahun->pluck('total_bayar')->toArray();
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
