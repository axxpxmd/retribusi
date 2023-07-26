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

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\DataWP;
use App\Models\OPDJenisPendapatan;

class DataWPController extends Controller
{
    protected $route = 'datawp.';
    protected $title = 'Data Pemohon Retribusi';
    protected $view  = 'pages.datawp.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Data WP']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();

        $opds = OPD::select('id', 'n_opd')
            ->when($opd_id != 0, function ($q) use ($opd_id) {
                $q->where('id', $opd_id);
            })
            ->whereIn('id', $opdArray)->get();

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'opd_id'
        ));
    }

    public function api(Request $request)
    {
        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $jenis_pendapatan_id = $request->jenis_pendapatan_id;

        $data = DataWP::queryTable($opd_id, $jenis_pendapatan_id);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                return "
                <a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus Data'><i class='icon-remove'></i></a>
                <a href='" . route('skrd.create', array('data_wp_id' =>  Crypt::encrypt($p->id))) . "' title='Buat SKRD' class=''><i class='icon-plus mr-1'></i></a>";
            })
            ->editColumn('nm_wajib_pajak', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->nm_wajib_pajak . "</a>";
            })
            ->editColumn('id_opd', function ($p) {
                return $p->opd->n_opd;
            })
            ->editColumn('id_jenis_pendapatan', function ($p) {
                return $p->jenis_pendapatan->jenis_pendapatan;
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'nm_wajib_pajak'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);

        $data = DataWP::find($id);

        $riwayatRetribusi = $data->riwayatRetribusi($data);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'riwayatRetribusi'
        ));
    }

    public function destroy($id)
    {
        DataWP::where('id', $id)->delete();

        return response()->json([
            'message' => $this->title . ' berhasil dihapus.'
        ]);
    }
}
