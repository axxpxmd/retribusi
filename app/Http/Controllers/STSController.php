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
use DateTime;
use DataTables;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\Http\Services\VABJB;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;

class STSController extends Controller
{
    protected $route = 'sts.';
    protected $title = 'STS';
    protected $view  = 'pages.sts.';


    public function __construct(VABJB $vabjb)
    {
        $this->vabjb = $vabjb;

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
        $channel_bayar = $request->channel_bayar;

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::querySTS($from, $to, $opd_id, $status_bayar, $jenis_tanggal, $no_bayar, $channel_bayar);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                $edit      = "<a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary mr-2' title='Edit Data'><i class='icon icon-edit'></i></a>";
                $report    = "<a href='" . route('print.sts', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                $reportTTD = "<a href='" . route($this->route . 'reportTTD', Crypt::encrypt($p->id)) . "' target='blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";

                if ($p->status_bayar == 1) {
                    if ($p->status_ttd == 1) {
                        return $reportTTD;
                    } else {
                        return $report;
                    }
                } else {
                    if ($p->status_ttd == 1 || $p->status_ttd == 3) {
                        return $edit . $reportTTD;
                    } elseif ($p->status_ttd == 2 || $p->status_ttd == 4) {
                        return $edit . $report;
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

        $va_number = (int) $data->nomor_va_bjb;
        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';
        $dateNow   = Carbon::now()->format('Y-m-d');

        //TODO: Get bunga
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        //TODO: Get bunga
        $jumlahBunga = 0;
        $kenaikan = 0;
        $dateNow = Carbon::now()->format('Y-m-d');
        if ($data->tgl_skrd_akhir < $dateNow) {
            $tgl_skrd_akhir = $data->tgl_skrd_akhir;
            $total_bayar    = $data->jumlah_bayar;
            list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);
        }

        //* Check status pembayaran VA BJB
        if ($data->status_bayar == 0 && $data->nomor_va_bjb != null && $data->tgl_skrd_akhir > $dateNow) {
            //TODO: Get Token BJB
            $resGetTokenBJB = $this->vabjb->getTokenBJB();
            if ($resGetTokenBJB->successful()) {
                $resJson = $resGetTokenBJB->json();
                if ($resJson['rc'] != 0000)
                    return redirect()
                        ->route($this->route . 'index')
                        ->withErrors('Terjadi kegagalan saat mengambil token. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . '');
                $tokenBJB = $resJson['data'];
            } else {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors("Terjadi kegagalan saat mengambil token. Error Code " . $resGetTokenBJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
            }

            //TODO: Check VA BJB
            $resCheckVABJB = $this->vabjb->CheckVABJB($tokenBJB, $va_number);
            if ($resCheckVABJB->successful()) {
                $resJson = $resCheckVABJB->json();
                if (isset($resJson['rc']) != 0000)
                    return redirect()
                        ->route($this->route . 'index')
                        ->withErrors('Terjadi kegagalan saat mengecek status pembayaran VA BJB. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . '');
                $VABJB  = $resJson['va_number'];
                $status = $resJson['status'];
                $transactionTime = $resJson['transactions']['transaction_date'];
                $transactionAmount = $resJson['transactions']['transaction_amount'];
            } else {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors("Terjadi kegagalan saat mengecek status pembayaran VA BJB. Error Code " . $resCheckVABJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
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

        if ($data->status_ttd == 1 || $data->status_ttd == 3) {
            $status_ttd = true;
        }else{
            $status_ttd = false;
        }

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

    public function edit($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $role = Auth::user()->pengguna->modelHasRole->role->name;
        $now  = Carbon::now()->format('Y-m-d\T:H:i');

        //TODO: Check role
        if ($role == 'super-admin' || $role == 'admin-opd') {
            $readonly = '';
        } else {
            $readonly = 'readonly';
        }

        $data      = TransaksiOPD::find($id);
        $va_number = (int) $data->nomor_va_bjb;

        //TODO: Get bunga
        $jumlahBunga = 0;
        $dateNow = Carbon::now()->format('Y-m-d');
        if ($data->tgl_skrd_akhir < $dateNow) {
            $tgl_skrd_akhir = $data->tgl_skrd_akhir;
            $total_bayar    = $data->jumlah_bayar;
            list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);
        }

        //* Check status pembayaran VA BJB
        if ($data->status_bayar == 0 && $data->nomor_va_bjb != null && $data->tgl_skrd_akhir > $dateNow) {
            //TODO: Get Token BJB
            $resGetTokenBJB = $this->vabjb->getTokenBJB();
            if ($resGetTokenBJB->successful()) {
                $resJson = $resGetTokenBJB->json();
                if ($resJson['rc'] != 0000)
                    return redirect()
                        ->route($this->route . 'index')
                        ->withErrors('Terjadi kegagalan saat mengambil token. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . '');
                $tokenBJB = $resJson['data'];
            } else {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors("Terjadi kegagalan saat mengambil token. Error Code " . $resGetTokenBJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
            }

            //TODO: Check VA BJB
            $resCheckVABJB = $this->vabjb->CheckVABJB($tokenBJB, $va_number);
            if ($resCheckVABJB->successful()) {
                $resJson = $resCheckVABJB->json();
                if (isset($resJson['rc']) != 0000)
                    return redirect()
                        ->route($this->route . 'index')
                        ->withErrors('Terjadi kegagalan saat mengecek status pembayaran VA BJB. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . '');
                $VABJB  = $resJson['va_number'];
                $status = $resJson['status'];
                $transactionTime = $resJson['transactions']['transaction_date'];
                $transactionAmount = $resJson['transactions']['transaction_amount'];
            } else {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors("Terjadi kegagalan saat mengecek status pembayaran VA BJB. Error Code " . $resCheckVABJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
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
            'chanel_bayar' => 'required'
        ]);

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);
        $role = Auth::user()->pengguna->modelHasRole->role->name;

        $status_bayar = $request->status_bayar;
        $tgl_bayar    = $request->tgl_bayar;

        //* Status Denda
        if ($request->denda == 0 || $request->denda == null) {
            $status_denda = 0;
        } else {
            $status_denda = 1;
        }

        // Check 
        if ($status_bayar == 1) {
            $data->update([
                'status_bayar' => 1,
                'tgl_bayar'    => $tgl_bayar,
                'no_bku'       => $request->no_bku,
                // 'tgl_bku'   => $request->tgl_bku,
                'chanel_bayar' => $request->chanel_bayar,
                'status_denda' => $status_denda,
                'ntb'    => $request->ntb,
                'denda'  => (int) str_replace(['.', 'Rp', ' '], '', $request->denda),
                'total_bayar_bjb' => $request->total_bayar_bjb == 0 ? null : (int) str_replace(['.', 'Rp', ' '], '', $request->total_bayar_bjb),
                'updated_by'      => Auth::user()->pengguna->full_name . ' | Update data menu STS'
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
                    'diskon' => 0,
                    'status_diskon' => 0,
                    'total_bayar_bjb' => null,
                    'total_bayar' => $data->jumlah_bayar,
                    'updated_by'  => Auth::user()->pengguna->full_name . ' | Update data menu STS'
                ]);
            } else {
                $data->update([
                    'status_bayar' => $status_bayar,
                    'tgl_bayar'    => $tgl_bayar,
                    'no_bku'       => $request->no_bku,
                    // 'tgl_bku'   => $request->tgl_bku,
                    'chanel_bayar' => $request->chanel_bayar,
                    'status_denda' => $status_denda,
                    'ntb'    => $request->ntb,
                    'denda'  => (int) str_replace(['.', 'Rp', ' '], '', $request->denda),
                    'total_bayar_bjb' => $request->total_bayar_bjb == 0 ? null : (int) str_replace(['.', 'Rp', ' '], '', $request->total_bayar_bjb),
                    'updated_by'      => Auth::user()->pengguna->full_name . ' | Update data menu STS'
                ]);
            }
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function getDiffDays($tgl_skrd_akhir)
    {
        $timeNow = Carbon::now();

        $dateTimeNow = new DateTime($timeNow);
        $expired     = new DateTime($tgl_skrd_akhir . ' 23:59:59');
        $interval    = $dateTimeNow->diff($expired);
        $daysDiff    = $interval->format('%r%a');

        return $daysDiff;
    }

    public function printDataTTD(Request $request, $id)
    {
        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);

        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        $daysDiff = $this->getDiffDays($tgl_skrd_akhir);

        //TODO: Check bunga (STRD)
        if ($daysDiff > 0) {
            $jumlahBunga = 0;
            $kenaikan = 0;
        } else {
            //* Bunga
            list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);
        }

        //* Total Bayar + Bunga
        if ($data->status_bayar == 1) {
            $total_bayar = $total_bayar + $data->denda;
        } else {
            $total_bayar = $total_bayar + $jumlahBunga;
        }

        $terbilang   = Html_number::terbilang($total_bayar) . 'rupiah';

        //TODO: generate QR Code
        $imgQRIS = '';
        if ($data->text_qris) {
            $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
            $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
            $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(1000)->errorCorrection('H')->margin(0)->generate($data->text_qris));
            $imgQRIS = '<img width="150" src="data:image/png;base64, ' . $b . '" alt="qr code" />';
        }

        //* Tanggal Jatuh Tempo STRD
        if ($data->tgl_strd_akhir == null) {
            $tgl_jatuh_tempo = $data->tgl_skrd_akhir;
        } else {
            $tgl_jatuh_tempo = $data->tgl_strd_akhir;
        }

        //TODO: generate QR Code
        $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
        $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->merge(public_path('images/logo-png.png'), 0.2, true)->size(900)->errorCorrection('H')->margin(0)->generate($file_url));
        $img = '<img width="60" height="61" src="data:image/png;base64, ' . $b . '" alt="qr code" />';

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'portrait');

        //TODO: Check status TTD
        if ($data->status_ttd == 1) {
            $file = 'pages.tandaTangan.reportTTEskrd';
        } elseif ($data->status_ttd == 3) {
            $file = 'pages.tandaTangan.reportTTEstrd';
        }

        $statusSTS = 1;

        $pdf->loadView($file, compact(
            'data',
            'terbilang',
            'jumlahBunga',
            'total_bayar',
            'kenaikan',
            'tgl_jatuh_tempo',
            'img',
            'statusSTS',
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
        ]);

        return redirect()
            ->route($this->route . 'show', $id_encrypt)
            ->withSuccess('Selamat! Data berhasil diubah.');
    }
}
