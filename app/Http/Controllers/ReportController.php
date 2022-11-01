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
use DataTables;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\JenisPendapatan;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;
use App\Models\RincianJenisPendapatan;

class ReportController extends Controller
{
    protected $route = 'report.';
    protected $title = 'Laporan';
    protected $view = 'pages.report.';

    //* Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Laporan']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id   = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

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
        $opd_id = $checkOPD == 0 ? $request->opd_id : $checkOPD;

        $jenis_pendapatan_id = $request->jenis_pendapatan_id;
        $status_bayar = $request->status_bayar;
        $from = $request->tgl_skrd;
        $to = $request->tgl_skrd1;
        $jenis = $request->jenis;
        $channel_bayar = $request->channel_bayar;
        $rincian_pendapatan_id = $request->rincian_pendapatan_id;

        $data = TransaksiOPD::queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id);

        return DataTables::of($data)
            ->editColumn('no_bayar', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_bayar . "</a>";
            })
            ->editColumn('opd_id', function ($p) {
                return $p->opd->n_opd;
            })
            ->editColumn('rincian_pendapatan', function ($p) {
                return $p->rincian_jenis->rincian_pendapatan;
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
                return $p->denda;
            })
            ->editColumn('status_bayar', function ($p) {
                if ($p->status_bayar == 1) {
                    return "<span class='badge badge-success'>Sudah bayar</span>";
                } else {
                    return  "<span class='badge badge-danger'>Belum bayar</span>";
                }
            })
            ->addColumn('cetak_skrd', function ($p) {
                $path_sftp = 'file_ttd_skrd/';
                $fileName  = str_replace(' ', '', $p->nm_wajib_pajak) . '-' . $p->no_skrd . ".pdf";
                $dateNow   = Carbon::now()->format('Y-m-d');
                $belumTTD = "<a href='" . config('app.sftp_src') . $path_sftp . $fileName . "' target='_blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";

                //* SKRD
                if ($p->tgl_skrd_akhir >= $dateNow) {
                    if ($p->status_ttd == 1) {
                        return $belumTTD;
                    } else {
                        return  "<a href='" . route('print.skrd', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                    }
                }

                //* STRD
                if ($p->tgl_skrd_akhir < $dateNow) {
                    if ($p->status_ttd == 3) {
                        return $belumTTD;
                    } else {
                        return "<a href='" . route('print.strd', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                    }
                }
            })
            ->addColumn('cetak_sts', function ($p) {
                if ($p->status_ttd == 1 || $p->status_ttd == 3) {
                    return "<a href='" . route('sts.reportTTD', Crypt::encrypt($p->id)) . "' target='blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";
                } else {
                    return "<a href='" . route('print.sts', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['no_bayar', 'opd_id', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_bayar', 'diskon', 'cetak_skrd', 'cetak_sts'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);

        $va_number = (int) $data->nomor_va_bjb;
        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';
        $dateNow   = Carbon::now()->format('Y-m-d');

        //TODO: Get bunga
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'path_sftp',
            'fileName',
            'kenaikan',
            'jumlahBunga',
            'dateNow'
        ));
    }

    public function getJenisPendapatanByOpd($opd_id)
    {
        $datas = OPDJenisPendapatan::select('tr_opd_jenis_pendapatans.id_jenis_pendapatan as id', 'tmjenis_pendapatan.jenis_pendapatan')
            ->join('tmjenis_pendapatan', 'tmjenis_pendapatan.id', '=', 'tr_opd_jenis_pendapatans.id_jenis_pendapatan')
            ->where('tr_opd_jenis_pendapatans.id_opd', $opd_id)
            ->get();

        return $datas;
    }

    public function getRincianByJenisPendapatan($jenis_pendapatan_id)
    {
        $data = RincianJenisPendapatan::where('id_jenis_pendapatan', $jenis_pendapatan_id)->get();

        return $data;
    }

    public function cetakSKRD(Request $request)
    {
        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $jenis_pendapatan_id = $request->jenis_pendapatan_id;
        $rincian_pendapatan_id = $request->rincian_pendapatan_id;
        $status_bayar = $request->status_bayar;
        $from = $request->tgl_skrd;
        $to = $request->tgl_skrd1;
        $jenis = $request->jenis;
        $channel_bayar = $request->channel_bayar;

        $data = TransaksiOPD::queryReportCetak($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id);
        $totalBayar = $data->sum('total_bayar');

        if ($jenis == 1 || $jenis == 0) {
            $title = 'SKRD (Surat Ketetapan Retribusi Daerah)';
            $jnsTanggal = 'SKRD';
        } else {
            $title = 'STS (Surat Tanda Setoran)';
            $jnsTanggal = 'Bayar';
        }

        // Filter
        $opd = OPD::find($opd_id);
        $jenis_pendapatan = JenisPendapatan::find($jenis_pendapatan_id);
        $rincian_pendapatan = RincianJenisPendapatan::find($rincian_pendapatan_id);

        $metode_bayar = 'Semua';
        switch ($channel_bayar) {
            case "1":
                $metode_bayar = 'BJB Virtual Account';
                break;
            case 2:
                $metode_bayar = 'ATM BJB';
                break;
            case 3;
                $metode_bayar = 'QRIS';
                break;
            default:
                // 
                break;
        }

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'landscape');
        $pdf->loadView($this->view . 'report', compact(
            'data',
            'title',
            'jenis',
            'from',
            'to',
            'totalBayar',
            'jnsTanggal',
            'opd',
            'jenis_pendapatan',
            'rincian_pendapatan',
            'channel_bayar',
            'metode_bayar'
        ))->setPaper('a3', 'landscape');

        return $pdf->download('Laporan' . $title . ' ' . $from . ' - ' . $to . ".pdf");
    }

    public function getTotalBayar(Request $request)
    {
        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $jenis_pendapatan_id = $request->jenis_pendapatan_id;
        $rincian_pendapatan_id = $request->rincian_pendapatan_id;
        $status_bayar = $request->status_bayar;
        $from = $request->tgl_skrd;
        $to = $request->tgl_skrd1;
        $jenis = $request->jenis;
        $channel_bayar = $request->channel_bayar;

        $data = TransaksiOPD::queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id);
        $totalBayar = $data->sum('total_bayar');

        $totalBayarJson = [
            'total_bayar' => 'Rp. ' . number_format($totalBayar)
        ];

        return $totalBayarJson;
    }
}
