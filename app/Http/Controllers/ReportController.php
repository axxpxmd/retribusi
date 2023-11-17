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
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\Utility;
use App\Models\TransaksiOPD;
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;
use App\Models\RincianJenisPendapatan;

use Maatwebsite\Excel\Facades\Excel;
use Spatie\SimpleExcel\SimpleExcelWriter;

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

    public function index(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $today = Carbon::now()->format('Y-m-d');
        $role  = Auth::user()->pengguna->modelHasRole->role->name;

        $opd_id = $request->opd_id ? $request->opd_id : Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        $to    = $request->to;
        $from  = $request->from;
        $jenis = $request->jenis;
        $status_bayar = $request->status_bayar;
        $channel_bayar = $request->channel_bayar;
        $jenis_pendapatan_id   = $request->jenis_pendapatan_id;
        $rincian_pendapatan_id = $request->rincian_pendapatan_id;

        $status = $request->status;
        $tahun  = $request->year;

        if ($request->ajax()) {
            return $this->dataTable($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id, $status, $tahun);
        }

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'today',
            'status',
            'tahun',
            'opd_id',
            'role'
        ));
    }

    public function dataTable($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id, $status, $tahun)
    {
        $data = TransaksiOPD::queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id, $status, $tahun);

        return DataTables::of($data)
            ->editColumn('no_skrd', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_skrd . "</a>";
            })
            ->editColumn('no_bayar', function ($p) {
                $status_ttd = Utility::checkStatusTTD($p->status_ttd);

                return $status_ttd ? $p->no_bayar : substr($p->no_bayar, 0, 6) . 'xxxxxxxx';
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
            ->editColumn('ketetapan', function ($p) {
                return 'Rp. ' . number_format($p->jumlah_bayar);
            })
            ->editColumn('denda', function ($p) {
                $dateNow = Carbon::now()->format('Y-m-d');

                $jumlah_bayar   = $p->jumlah_bayar;
                $status_bayar   = $p->status_bayar;
                $tgl_skrd_akhir = $p->tgl_skrd_akhir;
                $tgl_bayar      = $p->tgl_bayar;

                $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

                $kenaikan    = 0;
                $jumlahBunga = 0;
                if ($status_bayar == 1) {
                    list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar);
                } else {
                    if ($jatuh_tempo) {
                        list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
                    }
                }

                return 'Rp. ' . number_format($jumlahBunga);
                // return 'Rp. 0';
            })
            ->editColumn('status_bayar', function ($p) {
                if ($p->status_bayar == 1) {
                    return "<span class='badge badge-success'>Sudah bayar</span>";
                } else {
                    return  "<span class='badge badge-danger'>Belum bayar</span>";
                }
            })
            ->addColumn('cetak_skrd', function ($p) {
                $path_sftp  = 'file_ttd_skrd/';
                $fileName   = str_replace(' ', '', $p->nm_wajib_pajak) . '-' . $p->no_skrd . ".pdf";
                $status_ttd = Utility::checkStatusTTD($p->status_ttd);

                //* SKRD
                if ($status_ttd) {
                    return "<a href='" . config('app.sftp_src') . $path_sftp . $fileName  . "' target='_blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";
                } else {
                    return "<span>Belum TTD</span>";
                }
            })
            ->addColumn('cetak_sts', function ($p) {
                if ($p->status_bayar == 1) {
                    return "<a href='" . route('sts.reportTTD', Crypt::encrypt($p->id)) . "' target='blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";
                } else {
                    return "<span>-</span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['no_skrd', 'opd_id', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_bayar', 'diskon', 'cetak_skrd', 'cetak_sts'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';

        $va_number      = (int) $data->nomor_va_bjb;
        $status_ttd     = $data->status_ttd;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $status_bayar   = $data->status_bayar;
        $jumlah_bayar   = $data->jumlah_bayar;
        $tgl_bayar      = $data->tgl_bayar;

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar);
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
            }
        }

        $status_ttd = Utility::checkStatusTTD($status_ttd);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'path_sftp',
            'fileName',
            'kenaikan',
            'jumlahBunga',
            'dateNow',
            'status_ttd'
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
        $opd_id = Auth::user()->pengguna->opd_id == 0 ? $request->opd_id : Auth::user()->pengguna->opd_id;

        $to    = $request->to;
        $from  = $request->from;
        $jenis = $request->jenis;
        $status = $request->status;
        $tahun  = $request->year;
        $status_bayar  = $request->status_bayar;
        $channel_bayar = $request->channel_bayar;
        $jenis_pendapatan_id   = $request->jenis_pendapatan_id;
        $rincian_pendapatan_id = $request->rincian_pendapatan_id;

        $datas = TransaksiOPD::queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id, $status, $tahun);
        $data  = [];
        $totalDenda = [0];
        foreach ($datas as $key => $i) {
            $tgl_bayar      = $i->tgl_bayar;
            $status_bayar   = $i->status_bayar;
            $tgl_skrd_awal  = $i->tgl_skrd_awal;
            $tgl_skrd_akhir = $i->tgl_skrd_akhir;
            $jumlah_bayar   = $i->jumlah_bayar;

            $dateNow     = Carbon::now()->format('Y-m-d');
            $jatuh_tempo = Utility::isJatuhTempo($i->tgl_skrd_akhir, $dateNow);

            //TODO: Get bunga
            $kenaikan    = 0;
            $jumlahBunga = 0;
            if ($status_bayar == 1) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar);
            } else {
                if ($jatuh_tempo) {
                    list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
                }
            }

            $totalDenda[$key] = $jumlahBunga;

            $data[$key] = [
                'no_bayar' => $i->no_bayar,
                'no_skrd'  => $i->no_skrd,
                'nm_wajib_pajak' => $i->nm_wajib_pajak,
                'rincian_pendapatan' => $i->rincian_jenis->rincian_pendapatan,
                'tgl_skrd_awal' => $i->tgl_skrd_awal,
                'tgl_bayar' => $i->tgl_bayar,
                'chanel_bayar' => $i->chanel_bayar,
                'jumlah_bayar' => $i->jumlah_bayar,
                'diskon' => $i->diskon,
                'denda' => $jumlahBunga,
                'jumlah_bayar' => $i->jumlah_bayar,
                'status_bayar' => $i->status_bayar,
                'ntb' => $i->ntb
            ];
        }

        $totalBayar = $datas->sum('total_bayar') + array_sum($totalDenda);

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

        /**
         * 1. VA
         * 2. ATM
         * 3. BJB Mobile
         * 4. Teller
         * 5. QRIS
         * 6. Bendahara OPD
         * 7. Transfer RKUD
         * 8. RTGS/SKN
         * 9. Lainnya
         */

        $metode_bayar = 'Semua';
        if ($channel_bayar != 0) {
            switch ($channel_bayar) {
                case "1":
                    $metode_bayar = 'Virtual Account';
                    break;
                case 2:
                    $metode_bayar = 'ATM';
                    break;
                case 3;
                    $metode_bayar = 'BJB Mobile';
                    break;
                case 4;
                    $metode_bayar = 'Teller';
                    break;
                case 5;
                    $metode_bayar = 'QRIS';
                    break;
                case 6;
                    $metode_bayar = 'Bendahara';
                    break;
                case 7;
                    $metode_bayar = 'Transfer RKUD';
                    break;
                case 8;
                    $metode_bayar = 'RTGS/SKN';
                    break;
                case 9;
                    $metode_bayar = 'Lainnya';
                    break;
                default:
                    $metode_bayar = 'Lainnya';
                    break;
            }
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
            'metode_bayar',
            'status_bayar',
            'datas'
        ))->setPaper('a3', 'landscape');

        return $pdf->download('Laporan' . $title . ' ' . $from . ' - ' . $to . ".pdf");
    }

    public function getTotalBayar(Request $request)
    {
        $opd_id = Auth::user()->pengguna->opd_id == 0 ? $request->opd_id : Auth::user()->pengguna->opd_id;

        $to    = $request->to;
        $from  = $request->from;
        $jenis = $request->jenis;
        $status = $request->status;
        $tahun  = $request->year;
        $status_bayar  = $request->status_bayar;
        $channel_bayar = $request->channel_bayar;
        $jenis_pendapatan_id   = $request->jenis_pendapatan_id;
        $rincian_pendapatan_id = $request->rincian_pendapatan_id;

        $datas = TransaksiOPD::queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id, $status, $tahun);
        $totalBayar = $datas->sum('total_bayar');

        $totalBayarJson = [
            'total_bayar' => 'Rp. ' . number_format($totalBayar)
        ];

        return $totalBayarJson;
    }

    public function reportToExcel(Request $request)
    {
        $opd_id = Auth::user()->pengguna->opd_id == 0 ? $request->opd_id : Auth::user()->pengguna->opd_id;

        $to    = $request->to;
        $from  = $request->from;
        $jenis = $request->jenis;
        $status = $request->status;
        $tahun  = $request->year;
        $status_bayar  = $request->status_bayar;
        $channel_bayar = $request->channel_bayar;
        $jenis_pendapatan_id   = $request->jenis_pendapatan_id;
        $rincian_pendapatan_id = $request->rincian_pendapatan_id;

        $awal  = Carbon::createFromFormat('Y-m-d', $from)->isoFormat('D MMMM Y');
        $akhir =  Carbon::createFromFormat('Y-m-d', $to)->isoFormat('D MMMM Y');

        $writer = SimpleExcelWriter::streamDownload('Report ' . $awal . ' - ' . $akhir . '.xlsx');
        $query1 = TransaksiOPD::queryReport($opd_id, $jenis_pendapatan_id, $status_bayar, $from, $to, $jenis, $channel_bayar, $rincian_pendapatan_id, $status, $tahun);

        $i = 0;
        foreach ($query1->lazy(1000) as $q) {
            $writer->addRow([
                'no_bayar' => $q->no_bayar,
                'no_skrd'  => $q->no_skrd,
                'nm_wajib_pajak' => $q->nm_wajib_pajak,
                'opd' => $q->opd->n_opd,
                'jenis_pendapatan' => $q->jenis_pendapatan->jenis_pendapatan,
                'rincian_pendapatan' => $q->rincian_jenis->rincian_pendapatan,
                'tgl_skrd_awal' => $q->tgl_skrd_awal,
                'tgl_bayar' => $q->tgl_bayar,
                'ntb' => $q->ntb,
                'chanel_bayar' => $q->chanel_bayar,
                'jumlah_bayar' => $q->jumlah_bayar,
                'diskon' => $q->diskon,
                'denda' => $q->status_bayar == 1 ? $q->total_bayar_bjb - $q->jumlah_bayar : 0,
                'total_bayar_bjb' => $q->total_bayar_bjb,
                'status_bayar' => $q->status_bayar == 1 ? 'Sudah Bayar' : 'Belum Bayar',
            ]);

            if ($i % 1000 === 0) {
                flush(); // Flush the buffer every 1000 rows
            }
            $i++;
        }

        return $writer->toBrowser();
    }
}
