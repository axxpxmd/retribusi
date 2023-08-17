<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use Carbon\Carbon;

use App\Libraries\VABJBRes;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\Utility;
use App\Models\TransaksiOPD;
use App\Models\TransaksiDelete;
use App\Models\OPDJenisPendapatan;

class BatalSKRDController extends Controller
{
    protected $route  = 'batalSkrd.';
    protected $title  = 'Batal SKRD';
    protected $view   = 'pages.batalSkrd.';

    public function __construct(VABJBRes $vabjbres)
    {
        $this->vabjbres   = $vabjbres;
    }

    public function cari(Request $request)
    {
        $title = $this->title;
        $route = $this->route;

        $role  = Auth::user()->pengguna->modelHasRole->role->name;
        $opd_id   = Auth::user()->pengguna->opd_id == 0 ? $request->opd_id : Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        $no_skrd = $request->no_skrd;

        if ($request->ajax()) {
            return $this->dataTableCari($no_skrd);
        }

        return view($this->view . 'cari', compact(
            'route',
            'title',
            'opds',
            'opd_id',
            'role'
        ));
    }

    public function dataTableCari($no_skrd)
    {
        $data = TransaksiOPD::querySearchNoSkrd($no_skrd);

        return Datatables::of($data)
            ->addColumn('action', function ($p) {
                $delete  = "<a href='#' onclick='batalSkrd(" . $p->id . ")' class='text-danger mr-2' title='Batal SKRD'><i class='icon icon-remove'></i></a>";

                return $delete;
            })
            ->editColumn('no_skrd', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_skrd . "</a>";
            })
            ->editColumn('no_bayar', function ($p) {
                return $p->no_bayar;
            })
            ->editColumn('id_opd', function ($p) {
                return $p->opd->n_opd;
            })
            ->editColumn('id_jenis_pendapatan', function ($p) {
                return $p->jenis_pendapatan->jenis_pendapatan;
            })
            ->addColumn('tgl_skrd', function ($p) {
                return Carbon::createFromFormat('Y-m-d', $p->tgl_skrd_awal)->format('d M Y');
            })
            ->addColumn('masa_berlaku', function ($p) {
                return Carbon::createFromFormat('Y-m-d', $p->tgl_skrd_akhir)->format('d M Y');
            })
            ->editColumn('jumlah_bayar', function ($p) {
                return 'Rp. ' . number_format($p->jumlah_bayar);
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_ttd', 'no_bayar'])
            ->toJson();
    }

    public function index(Request $request)
    {
        $title = $this->title;
        $route = $this->route;

        $today = Carbon::now()->format('Y-m-d');
        $role  = Auth::user()->pengguna->modelHasRole->role->name;
        $opd_id   = Auth::user()->pengguna->opd_id == 0 ? $request->opd_id : Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        $from   = $request->from;
        $to     = $request->to;
        $no_skrd    = $request->no_skrd;
        $status_ttd = $request->status_ttd;

        if ($request->ajax()) {
            return $this->dataTable($from, $to, $opd_id, $no_skrd);
        }

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'opd_id',
            'today',
            'opd_id',
            'role',
            'from',
            'to'
        ));
    }

    public function dataTable($from, $to, $opd_id, $no_skrd)
    {
        $data = TransaksiDelete::queryTable($from, $to, $opd_id, $no_skrd);

        return Datatables::of($data)
            ->editColumn('no_skrd', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_skrd . "</a>";
            })
            ->editColumn('no_bayar', function ($p) {
                return $p->no_bayar;
            })
            ->editColumn('id_opd', function ($p) {
                return $p->opd->n_opd;
            })
            ->editColumn('id_jenis_pendapatan', function ($p) {
                return $p->jenis_pendapatan->jenis_pendapatan;
            })
            ->addColumn('tgl_skrd', function ($p) {
                return Carbon::createFromFormat('Y-m-d', $p->tgl_skrd_awal)->format('d M Y');
            })
            ->addColumn('masa_berlaku', function ($p) {
                return Carbon::createFromFormat('Y-m-d', $p->tgl_skrd_akhir)->format('d M Y');
            })
            ->editColumn('jumlah_bayar', function ($p) {
                return 'Rp. ' . number_format($p->jumlah_bayar);
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_ttd', 'no_bayar'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        if (!$data) {
            $data = TransaksiDelete::find($id);
        }

        $status_ttd = $data->status_ttd;
        $status_ttd = Utility::checkStatusTTD($status_ttd);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'status_ttd'
        ));
    }

    public function batalSKRD(Request $request)
    {
        /* Tahapan :
         * 1. tmtransaksi_opd (delete)
         * 2. Backup Data (Store)
         * 3. Update VA (make va expired)
         */

        //* Params
        $id = $request->id;
        $keterangan = $request->keterangan;

        //* Tahap 1
        $data = TransaksiOPD::where('id', $id)->first();

        //* Tahap 2
        $dataBackup = $data->toArray();
        $dataKet = [
            'updated_by' => Auth::user()->pengguna->full_name . ' | Batal SKRD', 
            'keterangan' => $keterangan
        ];
        TransaksiDelete::create(array_merge($dataBackup, $dataKet));

        //* Tahap 3
        $amount = \strval((int) str_replace(['.', 'Rp', ' '], '', $data->jumlah_bayar));
        $customerName = $data->nm_wajib_pajak;
        $va_number    = (int) $data->nomor_va_bjb;
        $clientRefnum = $data->no_bayar;

        if ($va_number) {
            $expiredDate = Carbon::now()->addMinutes(20)->format('Y-m-d H:i:s');

            //TODO: Get Token VA
            list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
            if ($err) {
                DB::rollback(); //* DB Transaction Failed
                return response()->json([
                    'message' => $errMsg
                ], 500);
            }

            //TODO: Update VA BJB (make Va expired)
            list($err, $errMsg, $VABJB) = $this->vabjbres->updateVABJBres($tokenBJB, $amount, $expiredDate, $customerName, $va_number, 2, $clientRefnum);
            if ($err) {
                DB::rollback(); //* DB Transaction Failed
                return response()->json([
                    'message' => $errMsg
                ], 500);
            }
        }

        //* LOG
        Log::channel('skrd_delete')->info('Batal Data SRKD | ' . 'Oleh:' . Auth::user()->pengguna->full_name, $data->toArray());

        $data->delete();

        return response()->json([
            'message' => 'SKRD berhasil dibatalkan.'
        ]);
    }
}
