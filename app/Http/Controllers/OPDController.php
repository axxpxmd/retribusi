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

    public function api(Request $request)
    {
        $data = OPD::all();

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                return "<a href='" . route($this->route . 'edit', $p->id) . "' class='text-primary' title='Edit Data'><i class='icon icon-edit'></i></a>";
            })
            ->editColumn('n_opd', function ($p) {
                return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Menampilkan Data'>" . $p->n_opd . "</a>";
            })
            ->editColumn('jenis_pendapatan', function ($p) {
                $jenis_pendapatan = OPDJenisPendapatan::where('id_opd', $p->id)->count();
                return $jenis_pendapatan . " <a href='" . route($this->route . 'editJenisPendapatan', $p->id) . "' class='text-success pull-right' title='Edit Jenis Pendapatan'><i class='icon-clipboard-list2 mr-1'></i></a>";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'n_opd', 'jenis_pendapatan'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $data = OPD::find($id);

        $jenis_pendaptans = OPDJenisPendapatan::where('id_opd', $id)->get();

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'jenis_pendaptans'
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
            'initial' => 'required',
            'nm_ttd'  => 'required',
            'nip_ttd' => 'required|numeric|digits:18'
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
        $datas = OPDJenisPendapatan::select('jenis_pendapatan', 'tmopd_jenis_pendapatan.id')
            ->join('tmjenis_pendapatan', 'tmjenis_pendapatan.id', '=', 'tmopd_jenis_pendapatan.id_jenis_pendapatan')
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
}
