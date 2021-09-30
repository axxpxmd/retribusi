<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Services\VABJB;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;

class STSController extends Controller
{
    protected $route = 'sts.';
    protected $title = 'STS';
    protected $view  = 'pages.sts.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:STS']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();

        if ($opd_id == 0) {
            $opds = OPD::select('id', 'n_opd')->whereIn('id', $opdArray)->get();
        } else {
            $opds = OPD::where('id', $opd_id)->whereIn('id', $opdArray)->get();
        }

        $time = Carbon::now();
        $today = $time->format('Y-m-d');

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opd_id',
            'opds',
            'today'
        ));
    }

    public function api(Request $request)
    {
        $from = $request->tgl_bayar;
        $to   = $request->tgl_bayar1;
        $status_bayar  = $request->status_bayar;
        $jenis_tanggal = $request->jenis_tanggal;
        $no_bayar = $request->no_bayar;

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::querySTS($from, $to, $opd_id, $status_bayar, $jenis_tanggal, $no_bayar);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                $edit      = "<a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary mr-2' title='Edit Data'><i class='icon icon-edit'></i></a>";
                $report    = "<a href='" . route($this->route . 'report', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                $reportTTD = "<a href='" . route($this->route . 'report', Crypt::encrypt($p->id)) . "' target='blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";

                if ($p->status_bayar == 1) {
                    if ($p->status_ttd == 1) {
                        return $reportTTD;
                    } else {
                        return $report;
                    }
                } else {
                    if ($p->status_ttd == 1) {
                        return $reportTTD . ' ' . $edit;
                    } elseif ($p->status_ttd == 2) {
                        return $report . $edit;
                    } elseif ($p->status_ttd == 0) {
                        return $edit . $report;
                    }
                }
            })
            ->editColumn('no_bayar', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_bayar . "</a>";
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
                if ($p->total_bayar_bjb != null) {
                    return 'Rp. ' . number_format((int) $p->total_bayar_bjb);
                } else {
                    return '-';
                }
            })
            ->editColumn('status_bayar', function ($p) {
                if ($p->status_bayar == 1) {
                    return "<span class='badge badge-success'>Sudah bayar</span>";
                } else {
                    return  "<span class='badge badge-danger'>Belum bayar</span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'no_bayar', 'opd_id', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_bayar'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);

        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'path_sftp',
            'fileName'
        ));
    }

    public function edit($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $role = Auth::user()->pengguna->modelHasRole->role->name;

        // Check role
        if ($role == 'super-admin' || $role == 'admin-opd') {
            $readonly = '';
        } else {
            $readonly = 'readonly';
        }

        $data = TransaksiOPD::find($id);

        return view($this->view . 'edit', compact(
            'route',
            'title',
            'data',
            'role',
            'readonly'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_bayar' => 'required'
        ]);

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);
        $role = Auth::user()->pengguna->modelHasRole->role->name;

        $status_bayar = $request->status_bayar;

        // Check 
        if ($status_bayar == 1) {
            $data->update([
                'status_bayar' => 1,
                'tgl_bayar'    => $request->tgl_bayar,
                'no_bku'       => $request->no_bku,
                // 'tgl_bku'   => $request->tgl_bku,
                'chanel_bayar' => $request->chanel_bayar,
                'ntb'    => $request->ntb,
                'denda'  => (int) str_replace(['.', 'Rp', ' '], '', $request->denda),
                'diskon' => $request->diskon,
                'total_bayar_bjb' => (int) str_replace(['.', 'Rp', ' '], '', $request->total_bayar_bjb)
            ]);
        } else {
            if ($role == 'bendahara-opd') {
                $data->update([
                    'status_bayar' => $status_bayar,
                    'tgl_bayar'    => null,
                    'no_bku'       => null,
                    // 'tgl_bku'   => $request->tgl_bku,
                    'chanel_bayar' => $request->chanel_bayar,
                    'ntb'    => null,
                    'denda'  => 0,
                    'diskon' => null,
                    'total_bayar_bjb' => null,
                    'total_bayar' => $data->jumlah_bayar
                ]);
            } else {
                $data->update([
                    'status_bayar' => $status_bayar,
                    'tgl_bayar'    => $request->tgl_bayar,
                    'no_bku'       => $request->no_bku,
                    // 'tgl_bku'   => $request->tgl_bku,
                    'chanel_bayar' => $request->chanel_bayar,
                    'ntb'    => $request->ntb,
                    'denda'  => (int) str_replace(['.', 'Rp', ' '], '', $request->denda),
                    'diskon' => $request->diskon,
                    'total_bayar_bjb' => (int) str_replace(['.', 'Rp', ' '], '', $request->total_bayar_bjb)
                ]);
            }
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function printData(Request $request, $id)
    {
        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);
        if ($data->total_bayar_bjb != null) {
            $total_bayar_final = $data->total_bayar_bjb;
        } else {
            $total_bayar_final = $data->total_bayar;
        }
        $terbilang = Html_number::terbilang($total_bayar_final) . 'rupiah';

        // generate QR Code
        $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
        $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->merge(public_path('images/logo-png.png'), 0.2, true)->size(900)->errorCorrection('H')->margin(0)->generate($file_url));
        $img = '<img width="60" height="61" src="data:image/png;base64, ' . $b . '" alt="qr code" />';

        // Update Jumlah Cetak
        $this->updateJumlahCetak($id, $data->jumlah_cetak);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);

        // Check status TTD
        if ($data->status_ttd == 1) {
            $file = 'reportTTE';
        } else {
            $file = 'report';
        }

        $pdf->loadView($this->view . $file, compact(
            'img',
            'data',
            'terbilang',
            'total_bayar_final'
        ));

        return $pdf->stream($data->nm_wajib_pajak . ' - ' . $data->no_skrd . ".pdf");
    }

    public function updateJumlahCetak($id, $jumlah_cetak)
    {
        $time    = Carbon::now();
        $tanggal = $time->toDateString();
        $jam     = $time->toTimeString();
        $now     = $tanggal . ' ' . $jam;

        TransaksiOPD::where('id', $id)->update([
            'jumlah_cetak' => $jumlah_cetak + 1,
            'tgl_cetak_trkhr' => $now
        ]);
    }
}
