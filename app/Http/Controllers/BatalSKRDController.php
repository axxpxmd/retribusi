<?php

namespace App\Http\Controllers;

use Auth;
use Validator;
use DataTables;
use Carbon\Carbon;

use App\Libraries\VABJBRes;
use App\Libraries\QRISBJBRes;
use App\Libraries\GenerateNumber;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\DataWP;
use App\Models\TtdOPD;
use App\Models\Utility;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TransaksiOPD;
use App\Models\TransaksiDelete;
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;
use App\Models\RincianJenisPendapatan;

class BatalSKRDController extends Controller
{
    protected $route  = 'batalSkrd.';
    protected $title  = 'Batal SKRD';
    protected $view   = 'pages.batalSKrd.';

    public function cari()
    {
        $title = $this->title;
        $route = $this->route;

        return view($this->view . 'cari', compact(
            'route',
            'title'
        ));
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

        $data = TransaksiDelete::find($id);

        $status_ttd = $data->status_ttd;
        $status_ttd = Utility::checkStatusTTD($status_ttd);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'status_ttd'
        ));
    }
}
