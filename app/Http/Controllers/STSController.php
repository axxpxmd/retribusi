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
use Illuminate\Support\Facades\Crypt;

use App\Libraries\VABJBRes;
use App\Http\Services\VABJB;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

// Models
use App\User;
use App\Models\OPD;
use App\Models\Utility;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;

class STSController extends Controller
{
    protected $route = 'sts.';
    protected $title = 'STS';
    protected $view  = 'pages.sts.';


    public function __construct(VABJBRes $vabjbres)
    {
        $this->vabjbres   = $vabjbres;

        $this->middleware(['permission:STS']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id   = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        //TODO: Set filters to date now
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
                $edit      = "<a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary' title='Edit Data'><i class='icon icon-edit'></i></a>";

                if ($p->status_bayar == 0) {
                    if ($p->status_ttd == 1 || $p->status_ttd == 3) {
                        return $edit;
                    } else {
                        return "<span>-</span>";
                    }
                } else {
                    return "<span>-</span>";
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
            ->addColumn('file_sts', function ($p) {
                $reportTTD = "<a href='" . route($this->route . 'reportTTD', Crypt::encrypt($p->id)) . "' target='blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";

                if ($p->status_bayar == 1) {
                    if ($p->status_ttd == 1 || $p->status_ttd == 3) {
                        return $reportTTD;
                    } else {
                        return "<span>-</span>";
                    }
                } else {
                    return "<span>-</span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'no_bayar', 'opd_id', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_bayar', 'file_sts'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id      = \Crypt::decrypt($id);
        $data    = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $va_number = (int) $data->nomor_va_bjb;
        $no_bayar  = $data->no_bayar;
        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        $status_ttd     = $data->status_ttd;
        $tgl_bayar      = $data->tgl_bayar;
        $status_bayar   = $data->status_bayar;

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

        $status_ttd = Utility::checkStatusTTD($status_ttd);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar, $tgl_bayar);
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar);
            }
        }

        //* Check status pembayaran VA BJB
        if ($data->status_bayar == 0 && $data->nomor_va_bjb != null && $jatuh_tempo == false) {
            //TODO: Get Token BJB
            list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }

            //TODO: Check VA BJB
            list($err, $errMsg, $VABJB, $status, $transactionTime, $transactionAmount) = $this->vabjbres->CheckVABJBres($tokenBJB, $va_number, 1, $no_bayar);
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }

            //TODO: Update tmtransaksi_opd
            if ($status == 2) {
                $ntb = \md5($data->no_bayar);
                $data->update([
                    'ntb'        => $ntb,
                    'tgl_bayar'  => $transactionTime,
                    'updated_by' => 'Bank BJB | Check Inquiry',
                    'status_bayar' => 1,
                    'chanel_bayar' => 'Virtual Account',
                    'total_bayar_bjb' => $transactionAmount,
                ]);
            }
        }

        return view($this->view . 'show', compact(
            'id',
            'route',
            'title',
            'data',
            'path_sftp',
            'fileName',
            'kenaikan',
            'jumlahBunga',
            'dateNow',
            'status_ttd',
            'jatuh_tempo'
        ));
    }

    public function edit($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $role = Auth::user()->pengguna->modelHasRole->role->name;
        $now  = Carbon::now()->format('Y-m-d\TH:i');
        $data = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $va_number = (int) $data->nomor_va_bjb;
        $no_bayar  = $data->no_bayar;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        $status_bayar   = $data->status_bayar;

        //TODO: Check role
        $readonly = User::checkRole($role);

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($jatuh_tempo) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar);
        }

        //* Check status bayar
        if ($status_bayar == 1) {
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Data sudah dibayar');
        }

        //* Check status pembayaran VA BJB
        if ($data->status_bayar == 0 && $data->nomor_va_bjb != null && $data->tgl_skrd_akhir > $dateNow) {
            //TODO: Get Token BJB
            list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }

            //TODO: Check VA BJB
            list($err, $errMsg, $VABJB, $status, $transactionTime, $transactionAmount) = $this->vabjbres->CheckVABJBres($tokenBJB, $va_number, 2, $no_bayar);
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }

            //TODO: Update tmtransaksi_opd
            if ($status == 2) {
                $ntb = \md5($data->no_bayar);
                $data->update([
                    'ntb'        => $ntb,
                    'tgl_bayar'  => $transactionTime,
                    'updated_by' => 'Bank BJB | Check Inquiry',
                    'status_bayar' => 1,
                    'chanel_bayar' => 'Virtual Account',
                    'total_bayar_bjb' => $transactionAmount,
                ]);
            }
        }

        return view($this->view . 'edit', compact(
            'id',
            'route',
            'title',
            'data',
            'role',
            'readonly',
            'now',
            'jumlahBunga'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_bayar' => 'required',
            'chanel_bayar' => 'required',
            'tgl_bayar' => 'required'
        ]);

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);
        $status_bayar = $request->status_bayar;
        $tgl_bayar    = $request->tgl_bayar;

        //TODO: Check denda
        $status_denda = TransaksiOPD::checkDenda($request->denda);

        // Check 
        if ($status_bayar == 1) {
            $data->update([
                'status_bayar' => 1,
                'tgl_bayar'    => $tgl_bayar,
                'no_bku'       => $request->no_bku,
                'chanel_bayar' => $request->chanel_bayar,
                'status_denda' => $status_denda,
                'ntb'    => $request->ntb,
                'denda'  => (int) str_replace(['.', 'Rp', ' '], '', $request->denda),
                'total_bayar_bjb' => $request->total_bayar_bjb == 0 ? null : (int) str_replace(['.', 'Rp', ' '], '', $request->total_bayar_bjb),
                'updated_by'      => Auth::user()->pengguna->full_name . ' | Update data menu STS'
            ]);
        } else {
            return response()->json([
                'message' => 'Pilih status bayar'
            ], 500);
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function printDataTTD(Request $request, $id)
    {
        $id      = \Crypt::decrypt($id);
        $data    = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        $status_bayar   = $data->status_bayar;
        $denda          = $data->denda; 
        $text_qris      = $data->text_qris;
        $nm_wajib_pajak = $data->nm_wajib_pajak;
        $no_skrd        = $data->no_skrd;
        $tgl_strd_akhir = $data->tgl_strd_akhir;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $tgl_skrd_awal  = $data->tgl_skrd_awal;
        $tgl_bayar      = $data->tgl_bayar;

        $fileName = str_replace(' ', '', $nm_wajib_pajak) . '-' . $no_skrd . ".pdf";
        $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_awal, $total_bayar, $tgl_bayar);
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar);
            }
        }

        //TODO: Total Bayar + Bunga
        $total_bayar = Utility::createDenda($status_bayar, $total_bayar, $denda, $jumlahBunga);

        $terbilang = Html_number::terbilang($total_bayar) . 'rupiah';
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);

        //TODO: generate QR Code (QRIS)
        $imgQRIS = '';
        if ($text_qris) {
            $imgQRIS = Utility::createQrQris($text_qris);
        }

        //TODO: generate QR Code (TTD)
        $img = Utility::createQrTTD($file_url);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'portrait');

        $pdf->loadView('pages.sts.reportTTE', compact(
            'data',
            'terbilang',
            'jumlahBunga',
            'total_bayar',
            'kenaikan',
            'tgl_jatuh_tempo',
            'img',
            'imgQRIS'
        ));

        return $pdf->stream($data->nm_wajib_pajak . ' - ' . $data->no_skrd . ".pdf");
    }

    public function batalBayar($id)
    {
        $data       = TransaksiOPD::find($id);
        $id_encrypt = \Crypt::encrypt($id);

        $data->update([
            'status_bayar' => 0,
            'tgl_bayar'    => null,
            'no_bku'       => null,
            'ntb'          => null,
            'updated_by'   => Auth::user()->pengguna->full_name . ' | Batal Bayar',
            'chanel_bayar' => null,
            'total_bayar_bjb' => null,
            'denda' => 0,
            'status_denda' => 1
        ]);

        return redirect()
            ->route($this->route . 'show', $id_encrypt)
            ->withSuccess('Selamat! Data berhasil diubah.');
    }
}
