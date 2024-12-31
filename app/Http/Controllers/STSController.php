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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

use App\Libraries\VABJBRes;
use App\Http\Services\Email;
use App\Http\Services\WhatsApp;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

// Queque
use App\Jobs\CallbackJob;

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


    public function __construct(VABJBRes $vabjbres, WhatsApp $whatsapp, Email $email)
    {
        $this->email = $email;
        $this->vabjbres = $vabjbres;
        $this->whatsapp = $whatsapp;

        $this->middleware(['permission:STS']);
    }

    public function index(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $today    = Carbon::now()->format('Y-m-d');
        $role     = Auth::user()->pengguna->modelHasRole->role->name;
        $opd_id   = Auth::user()->pengguna->opd_id == 0 ? $request->opd_id : Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        $from     = $request->from;
        $to       = $request->to;
        $no_bayar = $request->no_bayar;
        $status_bayar  = $request->status_bayar;
        $jenis_tanggal = $request->jenis_tanggal;

        $status = $request->status;

        if ($request->ajax()) {
            return $this->dataTable($from, $to, $opd_id, $status_bayar, $jenis_tanggal, $no_bayar, $status);
        }

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opd_id',
            'opds',
            'today',
            'status',
            'role'
        ));
    }

    public function dataTable($from, $to, $opd_id, $status_bayar, $jenis_tanggal, $no_bayar, $status)
    {
        $data = TransaksiOPD::querySTS($from, $to, $opd_id, $status_bayar, $jenis_tanggal, $no_bayar, $status);

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
            ->rawColumns(['action', 'no_skrd', 'opd_id', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_bayar', 'file_sts'])
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
        $tgl_strd_akhir = $data->tgl_strd_akhir;
        $jumlah_bayar   = $data->jumlah_bayar;
        $status_ttd     = $data->status_ttd;
        $tgl_bayar      = $data->tgl_bayar;
        $status_bayar   = $data->status_bayar;
        $total_bayar_bjb = $data->total_bayar_bjb;
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir);

        $status_ttd = Utility::checkStatusTTD($status_ttd);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            if (Carbon::parse($tgl_bayar)->format('Y-m-d') > $tgl_skrd_akhir) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar, $total_bayar_bjb);
            }
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
            }
        }

        //* Check status pembayaran VA BJB
        if ($jumlah_bayar != 0 && $data->status_bayar == 0 && $data->nomor_va_bjb != null && $tgl_jatuh_tempo > $dateNow && config('app.status_va') == 1) {
            //TODO: Get Token BJB
            list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }

            //TODO: Check VA BJB (INQUIRY)
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
            'jatuh_tempo',
            'tgl_jatuh_tempo'
        ));
    }

    public function edit($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = is_numeric($id) ? $id : \Crypt::decrypt($id);
        $role = Auth::user()->pengguna->modelHasRole->role->name;
        $now  = Carbon::now()->format('Y-m-d\TH:i');
        $data = TransaksiOPD::find($id);
        $dateNow   = Carbon::now()->format('Y-m-d');
        $va_number = (int) $data->nomor_va_bjb;
        $no_bayar  = $data->no_bayar;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $tgl_strd_akhir = $data->tgl_strd_akhir;
        $jumlah_bayar   = $data->jumlah_bayar;
        $status_bayar   = $data->status_bayar;
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);

        //* Check status bayar
        if ($status_bayar == 1) {
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Data sudah dibayar');
        }

        //TODO: Check role
        $readonly = User::checkRole($role);

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($jatuh_tempo) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
        }

        //* Check status pembayaran VA BJB
        if ($jumlah_bayar != 0 && $data->status_bayar == 0 && $data->nomor_va_bjb != null && $tgl_jatuh_tempo > $dateNow && config('app.status_va') == 1) {
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
            'jumlahBunga',
            'kenaikan'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_bayar' => 'required',
            'chanel_bayar' => 'required',
            'tgl_bayar'    => 'required',
            'total_bayar_bjb' => 'required',
            'file_pendukung'  => 'required'
        ]);

        //* Under Maintenance
        if (config('app.status_maintenance') == 1) {
            return response()->json([
                'message' => 'Penerimaan setoran pajak daerah dan retribusi daerah tahun anggaran 2025 dimulai pada tanggal 02 Januari 2025.'
            ], 500);
        }

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);

        $no_bku    = $request->no_bku;
        $ntb       = $request->ntb;
        $tgl_bayar = $request->tgl_bayar;
        $denda     = $request->denda;
        $status_bayar = $request->status_bayar;
        $chanel_bayar = $request->chanel_bayar;
        $total_bayar_bjb = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->total_bayar_bjb));
        $file_pendukung  = $request->file_pendukung;

        //TODO: Check denda
        $status_denda = Utility::checkDenda($denda);

        //* Check status bayar
        if ($status_bayar == 1) {
            if ($file_pendukung) {
                $ext = $request->file('file_pendukung')->extension();
                if (!in_array($ext, ['pdf', 'png', 'jpeg', 'jpg']))
                    return response()->json([
                        'message' => 'Format file tidak diperbolehkan'
                    ], 500);

                //TODO: Saved to storage
                $file     = $request->file('file_pendukung');
                $fileName = time() . "-" . $data->no_bayar . "." . $file->extension();
                $request->file('file_pendukung')->storeAs('file_pendukung/', $fileName, 'sftp', 'public');
            }

            $data->update([
                'status_bayar' => 1,
                'tgl_bayar'    => $tgl_bayar,
                'no_bku'       => $no_bku,
                'chanel_bayar' => $chanel_bayar,
                'status_denda' => $status_denda,
                'ntb'    => $ntb,
                'denda'  => (int) str_replace(['.', 'Rp', ' '], '', $denda),
                'total_bayar_bjb' => $total_bayar_bjb == 0 ? null : (int) str_replace(['.', 'Rp', ' '], '', $total_bayar_bjb),
                'updated_by'      => Auth::user()->pengguna->full_name . ' | Update data menu STS',
                'file_pendukung'  => $fileName
            ]);
        } else {
            return response()->json([
                'message' => 'Pilih status bayar'
            ], 500);
        }

        //* LOG
        Log::channel('sts_edit')->info('Edit Data SRKD | ' . 'Oleh:' . Auth::user()->pengguna->full_name, array_merge($data->toArray(), $request->all()));

        //* Send Email
        if ($data->email) {
            $this->email->sendSTS($data);
        }

        //* Send WA
        if ($data->no_telp) {
            $this->whatsapp->sendSTS($tgl_bayar, $ntb, $chanel_bayar, $total_bayar_bjb, $data);
        }

        //* Send Callback
        if ($data->user_api != null) {
            $url = $data->userApi->url_callback;
            $reqBody = [
                'nomor_va_bjb'  => $data->nomor_va_bjb,
                'no_bayar'      => $data->no_bayar,
                'waktu_bayar'   => $data->tgl_bayar,
                'jumlah_bayar'  => $data->total_bayar_bjb,
                'status_bayar'  => 1,
                'channel_bayar' => $data->chanel_bayar
            ];

            if ($url) {
                dispatch(new CallbackJob($reqBody, $url));
            }
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
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

        //* Send Callback
        if ($data->userApi != null) {
            $url = $data->userApi->url_callback;
            $reqBody = [
                'nomor_va_bjb'  => $data->nomor_va_bjb,
                'no_bayar'      => $data->no_bayar,
                'waktu_bayar'   => null,
                'jumlah_bayar'  => null,
                'status_bayar'  => 0,
                'channel_bayar' => null
            ];

            if ($url) {
                dispatch(new CallbackJob($reqBody, $url));
            }
        }

        //* LOG
        Log::channel('sts_batal_bayar')->info('Edit Data SRKD | ' . 'Oleh:' . Auth::user()->pengguna->full_name, $data->toArray());

        return redirect()
            ->route($this->route . 'show', $id_encrypt)
            ->withSuccess('Selamat! Data berhasil diubah.');
    }

    public function printDataTTD(Request $request, $id)
    {
        $id      = Crypt::decrypt($id);
        $data    = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $jumlah_bayar   = $data->jumlah_bayar;
        $status_bayar   = $data->status_bayar;
        $denda          = $data->denda;
        $text_qris      = $data->text_qris;
        $nm_wajib_pajak = $data->nm_wajib_pajak;
        $no_skrd        = $data->no_skrd;
        $tgl_strd_akhir = $data->tgl_strd_akhir;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $tgl_skrd_awal  = $data->tgl_skrd_awal;
        $tgl_bayar      = $data->tgl_bayar;
        $send_sts       = $request->send_sts;
        $total_bayar_bjb = $data->total_bayar_bjb;

        $fileName = str_replace(' ', '', $nm_wajib_pajak) . '-' . $no_skrd . ".pdf";
        $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            if (Carbon::parse($tgl_bayar)->format('Y-m-d') > $tgl_skrd_akhir) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar, $total_bayar_bjb);
            }
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
            }
        }

        //TODO: Total Bayar + Bunga
        $total_bayar = Utility::createDenda($status_bayar, $jumlah_bayar, $denda, $jumlahBunga);

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
            'imgQRIS',
            'send_sts'
        ));

        return $pdf->stream($data->nm_wajib_pajak . ' - ' . $data->no_skrd . ".pdf");
    }
}
