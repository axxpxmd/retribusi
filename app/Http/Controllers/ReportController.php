<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;

class ReportController extends Controller
{
    protected $route = 'report.';
    protected $title = 'Report';
    protected $view = 'pages.report.';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id = Auth::user()->pengguna->opd_id;

        if ($opd_id == 0) {
            $opds = OPD::select('id', 'n_opd')->get();
        } else {
            $opds = OPD::where('id', $opd_id)->get();
        }

        $time = Carbon::now();
        $today = $time->format('Y-m-d');

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'today'
        ));
    }

    public function api(Request $request)
    {
        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0 || $checkOPD == 99999) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $jenis_pendapatan_id = $request->jenis_pendapatan_id;
        $status_bayar = $request->status_bayar;
        $from = $request->tgl_skrd;
        $to = $request->tgl_skrd1;
        $jenis = $request->jenis;

        $data = TransaksiOPD::queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis);

        return DataTables::of($data)
            // ->addColumn('action', function ($p) {
            //     return "
            //         <a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary mr-2' title='Hapus Data'><i class='icon icon-edit'></i></a>
            //         <a href='" . route($this->route . 'report', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
            // })
            ->editColumn('no_bayar', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Show Data'>" . $p->no_bayar . "</a>";
            })
            ->editColumn('opd_id', function ($p) {
                return $p->opd->n_opd;
            })
            ->editColumn('id_jenis_pendapatan', function ($p) {
                return $p->jenis_pendapatan->jenis_pendapatan;
            })
            ->addColumn('tgl_bayar', function ($p) {
                if ($p->tgl_bayar != null) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $p->tgl_bayar)->format('d M Y | H:i:s');
                } else {
                    return '-';
                }
            })
            ->addColumn('tgl_skrd', function ($p) {
                return Carbon::createFromFormat('Y-m-d', $p->tgl_skrd_awal)->format('d M Y');
            })
            ->editColumn('total_bayar', function ($p) {
                return 'Rp. ' . number_format($p->total_bayar);
            })
            ->editColumn('diskon', function ($p) {
                $diskonHarga = ((int) $p->diskon / 100) * $p->jumlah_bayar;
                if ($p->status_diskon == 0) {
                    return "-";
                } else {
                    return '( ' . $p->diskon . '% )' . ' Rp. ' . number_format($diskonHarga);
                }
            })
            ->editColumn('denda', function ($p) {
                if ($p->status_denda == 0) {
                    return '-';
                } else {
                    return ' Rp. ' . number_format((int) $p->denda);
                }
            })
            ->editColumn('status_bayar', function ($p) {
                if ($p->status_bayar == 1) {
                    // return "Sudah Dibayar <i class='icon icon-check-circle text-primary'></i>";
                    return 'Sudah Dibayar';
                } else {
                    // return "Belum Dibayar <i class='icon icon-times-circle text-danger'></i>";
                    return 'Belum Dibayar';
                }
            })
            ->addIndexColumn()
            ->rawColumns(['no_bayar', 'opd_id', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_bayar', 'diskon'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data'
        ));
    }

    public function getJenisPendapatanByOpd($opd_id)
    {
        $datas = OPDJenisPendapatan::select('tmopd_jenis_pendapatan.id_jenis_pendapatan as id', 'tmjenis_pendapatan.jenis_pendapatan')
            ->join('tmjenis_pendapatan', 'tmjenis_pendapatan.id', '=', 'tmopd_jenis_pendapatan.id_jenis_pendapatan')
            ->where('tmopd_jenis_pendapatan.id_opd', $opd_id)
            ->get();

        return $datas;
    }

    public function cetakSKRD(Request $request)
    {
        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0 || $checkOPD == 99999) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        // Get time now
        $time = Carbon::now();
        $today = $time->format('Y-m-d');

        $jenis_pendapatan_id = $request->jenis_pendapatan_id;
        $status_bayar = $request->status_bayar;
        $from = $request->tgl_skrd != null ? $request->tgl_skrd : $today;
        $to = $request->tgl_skrd1 != null ? $request->tgl_skrd1 : $today;
        $jenis = $request->jenis;

        $data = TransaksiOPD::queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis);

        if ($jenis == 1 || $jenis == 0) {
            $title = 'SKRD (Surat Ketetapan Retribusi Daerah)';
        } else {
            $title = 'STS (Surat Tanda Setoran)';
        }

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'report', compact(
            'data',
            'title',
            'jenis'
        ))->setPaper('a3', 'landscape');

        return $pdf->download('Laporan ' . $title . ".pdf");
    }
}
