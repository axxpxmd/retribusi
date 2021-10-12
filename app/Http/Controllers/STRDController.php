<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use DataTables;
use Carbon\Carbon;
use Firebase\JWT\JWT;

use App\Http\Services\VABJB;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

// Models
use App\Models\OPD;
use App\Models\DataWP;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TransaksiOPD;
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;
use App\Models\RincianJenisPendapatan;

class STRDController extends Controller
{
    protected $route = 'strd.';
    protected $view  = 'pages.strd.';
    protected $title = 'STRD';
    protected $path  = '';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:STRD']);
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

        $time = Carbon::yesterday();
        $today = $time->format('Y-m-d');

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

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::querySTRD($from, $to, $opd_id, $no_skrd, $status_ttd);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                $path_sftp = 'file_ttd_skrd/';
                $fileName  = str_replace(' ', '', $p->nm_wajib_pajak) . '-' . $p->no_skrd . ".pdf";

                if ($p->tgl_strd_akhir == null) {
                    $tgl_jatuh_tempo = $p->tgl_skrd_akhir;
                } else {
                    $tgl_jatuh_tempo = $p->tgl_strd_akhir;
                }
                $daysDiff = $this->getDiffDays($tgl_jatuh_tempo);

                $filettd = "<a href='" . config('app.sftp_src') . $path_sftp . $fileName . "' target='_blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";
                $sendttd = "<a href='#' onclick='updateStatusTTD(" . $p->id . ")' class='amber-text' title='Kirim Untuk TTD'><i class='icon icon-send'></i></a>";
                $delete  = "<a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus Data'><i class='icon icon-remove'></i></a>";
                $edit    = "<a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary mr-2' title='Edit Data'><i class='icon icon-edit'></i></a>";

                if ($p->status_ttd == 3) {
                    return $filettd;
                } else {
                    if ($p->status_ttd == 4) {
                        return '-';
                    }
                    if ($daysDiff < 0) {
                        return $delete;
                    } else {
                        return $delete . $sendttd;
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
                list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);

                return 'Rp. ' . number_format($jumlahBunga) . ' (' . $kenaikan . '%)';
            })
            ->addColumn('status_ttd', function ($p) {
                if ($p->status_ttd == 0 || $p->status_ttd == 1 || $p->status_ttd == 2) {
                    return "<span class='badge badge-danger'>Belum</span>";
                } elseif ($p->status_ttd == 3) {
                    return "<span class='badge badge-success'>Sudah</span>";
                } elseif ($p->status_ttd == 4) {
                    return "<span class='badge badge-warning'>Proses</span>";
                }
            })
            ->addColumn('status_strd', function ($p) {
                if ($p->tgl_strd_akhir == null) {
                    $tgl_jatuh_tempo = $p->tgl_skrd_akhir;
                } else {
                    $tgl_jatuh_tempo = $p->tgl_strd_akhir;
                }
                $daysDiff = $this->getDiffDays($tgl_jatuh_tempo);

                $kadaluarsa = "<span class='badge badge-warning' style='font-size: 10.5px !important'>Kadaluarsa</span>";
                $berlaku    = "<span class='badge badge-success' style='font-size: 10.5px !important'>Berlaku</span>";
                $perbarui   = "<a href='#' onclick='perbaruiSTRD(" . $p->id . ")' class='text-primary mr-2' title='Perbarui STRD'><i class='icon-refresh'></i></a>";

                if ($p->tgl_strd_akhir != null) {
                    if ($daysDiff < 0) {
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

    public function getDiffDays($tgl_skrd_akhir)
    {
        $timeNow = Carbon::now();

        $dateTimeNow = new DateTime($timeNow);
        $expired     = new DateTime($tgl_skrd_akhir . ' 23:59:59');
        $interval    = $dateTimeNow->diff($expired);
        $daysDiff    = $interval->format('%r%a');

        return $daysDiff;
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';

        //TODO: Get diff days
        if ($data->tgl_strd_akhir == null) {
            $tgl_jatuh_tempo = $data->tgl_skrd_akhir;
        } else {
            $tgl_jatuh_tempo = $data->tgl_strd_akhir;
        }
        $daysDiff = $this->getDiffDays($tgl_jatuh_tempo);

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
            'daysDiff',
            'tgl_jatuh_tempo',
            'jumlahBunga',
            'kenaikan'
        ));
    }

    public function perbaruiSTRD($id)
    {
        $data = TransaksiOPD::find($id);

        /* Tahapan : 
         * 1. Update VA BJB / Create VA BJB
         * 2. tmtransaksi_opd
         */

        //* Tahap 1
        if ($data->tgl_strd_akhir == null) {
            $tgl_jatuh_tempo = $data->tgl_skrd_akhir;
        } else {
            $tgl_jatuh_tempo = $data->tgl_strd_akhir;
        }

        //TODO: Generate new tgl_jatuh_tempo (+30 day from last jatuh tempo)
        $daysDiff = $this->getDiffDays($data->tgl_skrd_akhir);
        $days = (int) abs($daysDiff) + 30;
        $tgl_jatuh_tempo = Carbon::createFromFormat('Y-m-d', $tgl_jatuh_tempo)->addDays($days)->format('Y-m-d');

        //* Bunga
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);
        $total_bayar = $data->total_bayar + $jumlahBunga;

        //TODO: Create amount
        if ($data->denda == 0) {
            $total_amount = $total_bayar;
        } else {
            $total_amount = $data->denda;
        }

        $amount = \strval((int) str_replace(['.', 'Rp', ' '], '', $total_amount));
        $expiredDate  = $tgl_jatuh_tempo . ' 23:59:59';
        $customerName = $data->nm_wajib_pajak;
        $va_number    = (int) $data->nomor_va_bjb;
        $VABJB        = $data->nomor_va_bjb;
        $clientRefnum = $data->no_bayar;
        $productCode  = $data->rincian_jenis->kd_jenis;

        //TODO: Get Token BJB
        $resGetTokenBJB = VABJB::getTokenBJB();
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

        if ($VABJB == null) {
            //TODO: Create VA BJB
            $resGetVABJB = VABJB::createVABJB($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode);
            if ($resGetVABJB->successful()) {
                $resJson = $resGetVABJB->json();
                if (isset($resJson['rc']) != 0000)
                    return redirect()
                        ->route($this->route . 'index')
                        ->withErrors('Terjadi kegagalan saat membuat Virtual Account. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . '');
                $VABJB = $resJson['va_number'];
            } else {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors("Terjadi kegagalan saat membuat Virtual Account. Error Code " . $resGetVABJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
            }
        } else {
            //TODO: Update VA BJB
            $resUpdateVABJB = VABJB::updateVaBJB($tokenBJB, $amount, $expiredDate, $customerName, $va_number);
            if ($resUpdateVABJB->successful()) {
                $resJson = $resUpdateVABJB->json();
                if (isset($resJson['rc']) != 0000)
                    return redirect()
                        ->route($this->route . 'index')
                        ->withErrors('Terjadi kegagalan saat memperbarui Virtual Account. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . '');
                $VABJB = $resJson['va_number'];
            } else {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors("Terjadi kegagalan saat memperbarui Virtual Account. Error Code " . $resUpdateVABJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
            }
        }

        //* Tahap 2
        $data->update([
            'tgl_strd_akhir' => $tgl_jatuh_tempo,
            'nomor_va_bjb'   => $VABJB
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

    //* Sedang dihide
    public function updateStatusKirimTTDs(Request $request)
    {
        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        //TODO: Get params
        $from = $request->tgl_skrd;
        $to   = $request->tgl_skrd1;
        $no_skrd    = $request->no_skrd;
        $status_ttd = $request->status_ttd;

        $datas = TransaksiOPD::querySTRD($from, $to, $opd_id, $no_skrd, $status_ttd);

        //TODO: Check length datas
        $dataLength = count($datas);
        if ($dataLength == 0)
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Tidak ada data yang dikirim, pastikan filter data sudah sesuai.');

        //TODO: Proses update status TTD
        for ($i = 0; $i < $dataLength; $i++) {
            $datas[$i]->update([
                'status_ttd' => 4
            ]);
        }

        return redirect()
            ->route($this->route . 'index')
            ->withSuccess('Selamat! ' . $dataLength . ' Data berhasil dikirim untuk ditandatangan.');
    }

    public function destroy($id)
    {
        TransaksiOPD::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
