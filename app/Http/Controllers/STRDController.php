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

use App\Libraries\QRISBJBRes;
use App\Libraries\VABJBRes;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\Utility;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;

class STRDController extends Controller
{
    protected $route = 'strd.';
    protected $view  = 'pages.strd.';
    protected $title = 'STRD';
    protected $path  = '';

    public function __construct(VABJBRes $vabjbres, QRISBJBRes $qrisbjbres)
    {
        $this->vabjbres   = $vabjbres;
        $this->qrisbjbres = $qrisbjbres;

        $this->middleware(['permission:STRD']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id   = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        $today = Carbon::yesterday()->format('Y-m-d');

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'opd_id',
            'today'
        ));
    }

    public function api(Request $request)
    {
        $from  = $request->tgl_skrd;
        $to    = $request->tgl_skrd1;
        $no_skrd    = $request->no_skrd;
        $status_ttd = $request->status_ttd;

        $dateNow  = Carbon::now()->format('Y-m-d');
        $checkOPD = Auth::user()->pengguna->opd_id;

        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::querySTRD($from, $to, $opd_id, $no_skrd, $status_ttd);

        return DataTables::of($data)
            ->addColumn('action', function ($p) use ($dateNow) {
                $fileName  = str_replace(' ', '', $p->nm_wajib_pajak) . '-' . $p->no_skrd . ".pdf";
                $path_sftp = 'file_ttd_skrd/';

                $filettd = "<a href='" . config('app.sftp_src') . $path_sftp . $fileName . "' target='_blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";
                $sendttd = "<a href='#' onclick='updateStatusTTD(" . $p->id . ")' class='amber-text' title='Kirim Untuk TTD'><i class='icon icon-send'></i></a>";

                $tgl_skrd_akhir = $p->tgl_skrd_akhir;
                $tgl_strd_akhir = $p->tgl_strd_akhir;

                $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);
                $jatuh_tempo = Utility::isJatuhTempo($tgl_jatuh_tempo, $dateNow);

                if ($jatuh_tempo) {
                    return $sendttd;
                } else {
                    if ($p->status_ttd == 3) {
                        return $filettd;
                    }
                    if ($p->status_ttd == 4) {
                        return '-';
                    }
                    if ($p->status_ttd == 0) {
                        return $sendttd;
                    }
                }
            })
            ->editColumn('no_skrd', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_skrd . "</a>";
            })
            ->editColumn('id_opd', function ($p) {
                return $p->opd->n_opd;
            })
            ->editColumn('id_jenis_pendapatan', function ($p) {
                return $p->jenis_pendapatan->jenis_pendapatan;
            })
            ->addColumn('masa_berlaku_skrd', function ($p) {
                return Carbon::createFromFormat('Y-m-d', $p->tgl_skrd_akhir)->format('d M Y');
            })
            ->addColumn('masa_berlaku_strd', function ($p) {
                if ($p->tgl_strd_akhir != null) {
                    return Carbon::createFromFormat('Y-m-d', $p->tgl_strd_akhir)->format('d M Y');
                } else {
                    return '-';
                }
            })
            ->editColumn('jumlah_bayar', function ($p) {
                return 'Rp. ' . number_format($p->jumlah_bayar);
            })
            ->addColumn('bunga', function ($p) {
                $tgl_skrd_akhir = $p->tgl_skrd_akhir;
                $total_bayar    = $p->jumlah_bayar;
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar);

                return 'Rp. ' . number_format($jumlahBunga) . ' (' . $kenaikan . '%)';
            })
            ->addColumn('status_ttd', function ($p) use ($dateNow) {
                $tgl_skrd_akhir = $p->tgl_skrd_akhir;
                $tgl_strd_akhir = $p->tgl_strd_akhir;

                $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);
                $jatuh_tempo = Utility::isJatuhTempo($tgl_jatuh_tempo, $dateNow);

                if ($jatuh_tempo) {
                    return "<span class='badge badge-danger'>Belum</span>";
                } else {
                    if ($p->status_ttd == 0 || $p->status_ttd == 1 || $p->status_ttd == 2) {
                        return "<span class='badge badge-danger'>Belum</span>";
                    } elseif ($p->status_ttd == 3) {
                        return "<span class='badge badge-success'>Sudah</span>";
                    } elseif ($p->status_ttd == 4) {
                        return "<span class='badge badge-warning'>Proses</span>";
                    }
                }
            })
            ->addColumn('status_strd', function ($p) use ($dateNow) {
                $kadaluarsa = "<span class='badge badge-warning' style='font-size: 10.5px !important'>Kadaluarsa</span>";
                $berlaku    = "<span class='badge badge-success' style='font-size: 10.5px !important'>Berlaku</span>";
                $perbarui   = "<a href='#' onclick='perbaruiSTRD(" . $p->id . ")' class='text-primary mr-2' title='Perbarui STRD'><i class='icon-refresh'></i></a>";

                $tgl_skrd_akhir = $p->tgl_skrd_akhir;
                $tgl_strd_akhir = $p->tgl_strd_akhir;

                $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);
                $jatuh_tempo = Utility::isJatuhTempo($tgl_jatuh_tempo, $dateNow);

                if ($p->tgl_strd_akhir != null) {
                    if ($jatuh_tempo !== false) {
                        return $kadaluarsa . ' &nbsp; ' . $perbarui;
                    } else {
                        return $berlaku;
                    }
                } else {
                    return $kadaluarsa . ' &nbsp; ' . $perbarui;
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'masa_berlaku_skrd', 'masa_berlaku_strd', 'status_ttd', 'bunga', 'status_strd'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $data = TransaksiOPD::find($id);

        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $tgl_strd_akhir = $data->tgl_strd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        $status_ttd     = $data->status_ttd;

        $status_ttd = Utility::checkStatusTTD($status_ttd);
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);

        //TODO: Get diff days
        list($dayDiff, $monthDiff) = Utility::getDiffDate($tgl_skrd_akhir);
        $checkJatuhTempo = Utility::isJatuhTempo($tgl_jatuh_tempo, $dateNow);

        //TODO: Get bunga
        list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'path_sftp',
            'fileName',
            'dayDiff',
            'tgl_jatuh_tempo',
            'jumlahBunga',
            'kenaikan',
            'checkJatuhTempo',
            'status_ttd'
        ));
    }

    public function perbaruiSTRD($id)
    {
        $data = TransaksiOPD::find($id);

        /* Tahapan : 
         * 1. VA
         * 2. QRIS
         * 2. tmtransaksi_opd
         */

        $jumlah_bayar   = $data->jumlah_bayar;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $tgl_strd_akhir = $data->tgl_strd_akhir;

        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);

        //TODO: Generate new tgl_jatuh_tempo (+30 day from last jatuh tempo)
        list($dayDiff, $monthDiff) = Utility::getDiffDate($tgl_skrd_akhir);
        $addDays = ($monthDiff + 1) * 30;

        $tgl_jatuh_tempo_baru = Carbon::createFromFormat('Y-m-d', $tgl_jatuh_tempo)->addDays($addDays)->format('Y-m-d');

        //* Bunga
        list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
        $total_bayar = $jumlah_bayar + $jumlahBunga;

        $amount = \strval((int) str_replace(['.', 'Rp', ' '], '', $total_bayar));
        $expiredDate  = $tgl_jatuh_tempo_baru . ' 23:59:59';
        $customerName = $data->nm_wajib_pajak;
        $va_number    = (int) $data->nomor_va_bjb;
        $VABJB        = $data->nomor_va_bjb;
        $clientRefnum = $data->no_bayar;
        $productCode  = $data->rincian_jenis->kd_jenis;
        $no_hp        = $data->rincian_jenis->no_hp;

        //* Tahap 1
        //TODO: Get Token BJB
        list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
        if ($err) {
            return redirect()
                ->route($this->route . 'index')
                ->withErrors($errMsg);
        }

        if ($VABJB == null) {
            //TODO: Create VA BJB
            list($err, $errMsg, $VABJB) = $this->vabjbres->createVABJBres($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode, 3, $clientRefnum);
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }
        } else {
            //TODO: Update VA BJB
            list($err, $errMsg, $VABJB) = $this->vabjbres->updateVABJBres($tokenBJB, $amount, $expiredDate, $customerName, $va_number, 3, $clientRefnum);
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }
        }

        //* Tahap 2
        $invoiceId = null;
        $textQRIS = null;
        if ($amount <= 10000000 && env('STATUS_QRIS') == 1) { //* Nominal QRIS maksimal 10 juta, jika lebih maka tidak terbuat
            //TODO: Get Token QRIS
            list($err, $errMsg, $tokenQRISBJB) = $this->qrisbjbres->getTokenQrisres();
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }

            // TODO: Create QRIS
            list($err, $errMsg, $invoiceId, $textQRIS) = $this->qrisbjbres->createQRISres($tokenQRISBJB, $amount, $no_hp, 3, $clientRefnum);
            if ($err) {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors($errMsg);
            }
        } else {
            $invoiceId = null;
            $textQRIS = null;
        }

        //* Tahap 3
        $data->update([
            'tgl_strd_akhir' => $tgl_jatuh_tempo_baru,
            'nomor_va_bjb'   => $VABJB,
            'invoice_id' => $invoiceId,
            'text_qris'  => $textQRIS,
            'status_ttd' => 0,
            'updated_by'  => Auth::user()->pengguna->full_name . ' | Perbarui SKRD'
        ]);

        return redirect()
            ->route($this->route . 'index')
            ->withSuccess('Selamat! Data STRD berhasil diperbaharui.');
    }

    public function updateStatusKirimTTD($id)
    {
        $data = TransaksiOPD::find($id);
        $data->update([
            'status_ttd' => 4
        ]);

        return redirect()
            ->route($this->route . 'index')
            ->withSuccess('Selamat! Data berhasil dikirim untuk ditandatangan.');
    }

    public function destroy($id)
    {
        TransaksiOPD::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
