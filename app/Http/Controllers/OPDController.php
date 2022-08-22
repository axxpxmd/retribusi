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

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\OPD;
use App\Models\TtdOPD;
use App\Models\Pengguna;
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;

class OPDController extends Controller
{
    protected $route = 'opd.';
    protected $title = 'OPD';
    protected $view  = 'pages.opd.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:OPD']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        return view($this->view . 'index', compact(
            'route',
            'title'
        ));
    }

    public function api()
    {
        $data = OPD::with(['getApiKey', 'countJenisPendapatan', 'countPenandaTangan'])->get();

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                return "<a href='" . route($this->route . 'edit', $p->id) . "' class='text-primary' title='Edit Data'><i class='icon icon-edit'></i></a>";
            })
            ->editColumn('n_opd', function ($p) {
                return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Menampilkan Data'>" . $p->n_opd . "</a>";
            })
            ->editColumn('jenis_pendapatan', function ($p) {
                $jenis_pendapatan = $p->countJenisPendapatan->count();
                return $jenis_pendapatan . " <a href='" . route($this->route . 'editJenisPendapatan', $p->id) . "' class='text-success pull-right ml-1' title='Edit Jenis Pendapatan'><i class='icon-clipboard-list2 mr-1'></i></a>";
            })
            ->addColumn('penanda_tangan', function ($p) {
                $penanda_tangan = $p->countPenandaTangan->count();
                return $penanda_tangan . " <a href='" . route($this->route . 'penandaTangan', $p->id) . "' class='amber-text pull-right ml-1' title='Edit Jenis Pendapatan'><i class='icon-pencil mr-1'></i></a>";
            })
            ->addColumn('api_key', function ($p) {
                return $p->getApiKey->api_key;
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'n_opd', 'jenis_pendapatan', 'penanda_tangan'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $data = OPD::find($id);

        $jenis_pendaptans = OPDJenisPendapatan::where('id_opd', $id)->get();
        $penanda_tangans = TtdOPD::where('id_opd', $id)->get();

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'jenis_pendaptans',
            'penanda_tangans'
        ));
    }

    public function edit($id)
    {
        $route = $this->route;
        $title = $this->title;

        $data = OPD::find($id);

        return view($this->view . 'edit', compact(
            'route',
            'title',
            'data'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'n_opd' => 'required|unique:tmopds,n_opd,' . $id,
            'kode'  => 'required|unique:tmopds,n_opd,' . $id,
            'initial' => 'required'
        ]);

        $data = OPD::find($id);

        $input = $request->all();
        $data->update($input);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function jenisPendapatan($id)
    {
        $route = $this->route;
        $title = $this->title;

        $opd = OPD::where('id', $id)->first();
        $exist_jenis_pendapatas = OPDJenisPendapatan::select('id_jenis_pendapatan')->where('id_opd', $id)->get()->toArray();
        $jenis_pendapatans = JenisPendapatan::select('id', 'jenis_pendapatan')->whereNotIn('id', $exist_jenis_pendapatas)->get();

        return view($this->view . 'formJenisPendapatans', compact(
            'route',
            'title',
            'opd',
            'jenis_pendapatans'
        ));
    }

    public function storeJenisPendapatan(Request $request)
    {
        $id_opd = $request->id;
        $id_jenis_pendapatans = $request->jenis_pendatans;
        $id_jenis_pendapatans_length = count($id_jenis_pendapatans);

        for ($i = 0; $i < $id_jenis_pendapatans_length; $i++) {
            $data = new OPDJenisPendapatan();
            $data->id_opd = $id_opd;
            $data->id_jenis_pendapatan = $id_jenis_pendapatans[$i];
            $data->save();
        }

        return response()->json([
            'message' => 'Data permission berhasil tersimpan.'
        ]);
    }

    public function getJenisPendapatan($id)
    {
        $datas = OPDJenisPendapatan::select('jenis_pendapatan', 'tr_opd_jenis_pendapatans.id')
            ->join('tmjenis_pendapatan', 'tmjenis_pendapatan.id', '=', 'tr_opd_jenis_pendapatans.id_jenis_pendapatan')
            ->where('id_opd', $id)
            ->get();

        return $datas;
    }

    public function destroyJenisPendapatan($id)
    {
        OPDJenisPendapatan::where('id', $id)->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function penandaTangan($id)
    {
        $route = $this->route;
        $title = $this->title;

        $opd = OPD::where('id', $id)->first();
        $exist_penanda_tangans = TtdOPD::select('user_id')->where('id_opd', $id)->get()->toArray();
        $penanda_tangans = Pengguna::select('user_id', 'full_name', 'nip')
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'tmpenggunas.user_id')
            ->where('model_has_roles.role_id', 11)
            ->where('opd_id', $id)
            ->whereNotIn('user_id', $exist_penanda_tangans)
            ->whereNotNull('nip')
            ->get();

        return view($this->view . 'formPenandaTangan', compact(
            'route',
            'title',
            'opd',
            'penanda_tangans'
        ));
    }

    public function getPenandaTangan($id)
    {
        $datas = TtdOPD::select('tr_ttd_opds.id as id', 'full_name', 'nip')
            ->join('tmpenggunas', 'tmpenggunas.user_id', '=', 'tr_ttd_opds.user_id')
            ->where('tr_ttd_opds.id_opd', $id)
            ->get();

        return $datas;
    }

    public function storePenandaTangan(Request $request)
    {
        $id_opd = $request->id;
        $id_penanda_tangans = $request->penanda_tangans;
        $id_penanda_tangans_length = count($id_penanda_tangans);

        for ($i = 0; $i < $id_penanda_tangans_length; $i++) {
            $data = new TtdOPD();
            $data->id_opd = $id_opd;
            $data->user_id = $id_penanda_tangans[$i];
            $data->save();
        }

        return response()->json([
            'message' => 'Data permission berhasil tersimpan.'
        ]);
    }

    public function destroyPenandaTangan($id)
    {
        TtdOPD::where('id', $id)->delete();

        return response()->json([
            'success' => true
        ]);
    }
}
