<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;


class TandaTanganController extends Controller
{
    protected $route = 'tanda-tangan.';
    protected $title = 'Tanda Tangan';
    protected $view  = 'pages.tandaTangan.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Tanda Tangan']);
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
            'opds',
            'opd_id',
            'today'
        ));
    }

    public function api(Request $request)
    {
        $from  = $request->tgl_skrd;
        $to    = $request->tgl_skrd1;
        $no_skrd = $request->no_skrd;

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0 || $checkOPD == 99999) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::querySKRD($from, $to, $opd_id, $no_skrd);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                if ($p->status_ttd == 1) {
                    return "<a href='" . route($this->route . 'report', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                } else {
                    return "";
                }
            })
            ->editColumn('no_skrd', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Show Data'>" . $p->no_skrd . "</a>";
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
            ->rawColumns(['action', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data'
        ));
    }

    public function report()
    {
        // 
    }
}
