<?php

namespace App\Http\Controllers;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\TransaksiOPD;
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;
use App\Models\RincianJenisPendapatan;

class RincianJenisController extends Controller
{
    protected $route = 'rincian-jenis.';
    protected $title = 'Rincian Jenis Pendapatan';
    protected $view  = 'pages.rincianJenis.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Rincian Pendapatan']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $jenis_pendapatan_array = OPDJenisPendapatan::select('id_jenis_pendapatan')->get()->toArray();
        $jenis_pendapatans = JenisPendapatan::select('id', 'jenis_pendapatan')->whereIn('id', $jenis_pendapatan_array)->get();

        return view($this->view . 'index', compact(
            'route',
            'title',
            'jenis_pendapatans'
        ));
    }

    public function api(Request $request)
    {
        $id_jenis_pendapatan = $request->jenis_pendapatan;

        $datas = RincianJenisPendapatan::orderBy('id', 'DESC')->get();

        if ($id_jenis_pendapatan != 0) {
            $datas = RincianJenisPendapatan::where('id_jenis_pendapatan', $id_jenis_pendapatan)->orderBy('id', 'DESC')->get();
        }

        return DataTables::of($datas)
            ->addColumn('action', function ($p) {
                // Check
                $check = TransaksiOPD::where('id_rincian_jenis_pendapatan', $p->id)->count();

                if ($check == 0) {
                    return "<a href='" . route($this->route . 'edit', $p->id) . "' title='Edit Permission'><i class='icon-edit mr-1'></i></a>
                            <a href='#' onclick='remove(" . $p->id . ")' class='text-danger' title='Hapus Role'><i class='icon-remove'></i></a>";
                } else {
                    return "<a href='" . route($this->route . 'edit', $p->id) . "' title='Edit Permission'><i class='icon-edit mr-1'></i></a>";
                }
            })
            ->editColumn('rincian_pendapatan', function ($p) {
                return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Show Data'>" . $p->rincian_pendapatan . "</a>";
            })
            ->editColumn('id_jenis_pendapatan', function ($p) {
                return $p->jenis_pendapatan->jenis_pendapatan;
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'rincian_pendapatan'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_jenis_pendapatan' => 'required',
            'rincian_pendapatan'  => 'required',
            'nmr_rekening' => 'required',
            'kd_jenis'     => 'required|numeric|max:99'
        ]);

        $id_jenis_pendapatan = $request->id_jenis_pendapatan;
        $rincian_pendapatan  = $request->rincian_pendapatan;

        // Check duplicat data
        $check = RincianJenisPendapatan::where('id_jenis_pendapatan', $id_jenis_pendapatan)->where('rincian_pendapatan', $rincian_pendapatan)->count();
        if ($check != 0) {
            return response()->json([
                'message' => "Data ini sudah pernah tersimpan."
            ], 422);
        }

        $input = $request->all();
        RincianJenisPendapatan::create($input);

        return response()->json([
            'message' => "Data " . $this->title . " berhasil tersimpan."
        ]);
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $data = RincianJenisPendapatan::find($id);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data'
        ));
    }

    public function edit($id)
    {
        $route = $this->route;
        $title = $this->title;

        $data = RincianJenisPendapatan::find($id);

        $jenis_pendapatan_exist = OPDJenisPendapatan::select('id_jenis_pendapatan')->get()->toArray();
        $jenis_pendapatans = JenisPendapatan::select('id', 'jenis_pendapatan')->whereIn('id', $jenis_pendapatan_exist)->get();

        return view($this->view . 'edit', compact(
            'route',
            'title',
            'data',
            'jenis_pendapatans'
        ));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_jenis_pendapatan' => 'required',
            'rincian_pendapatan'  => 'required',
            'nmr_rekening' => 'required',
            'kd_jenis'     => 'required|numeric|max:99'
        ]);

        // get params
        $input = $request->all();
        $id_jenis_pendapatan = $request->id_jenis_pendapatan;
        $rincian_pendapatan  = $request->rincian_pendapatan;

        // get data
        $data = RincianJenisPendapatan::find($id);
        $idJenisPendapatan = $data->id_jenis_pendapatan;
        $rincianPendapatan = $data->rincian_pendapatan;

        // Check duplicate data
        if ($id_jenis_pendapatan == $idJenisPendapatan && $rincian_pendapatan == $rincianPendapatan) {
            $data->update($input);
        } else {
            $check = RincianJenisPendapatan::where('id_jenis_pendapatan', $id_jenis_pendapatan)->where('rincian_pendapatan', $rincian_pendapatan)->count();
            if ($check != 0) {
                return response()->json([
                    'message' => "Data ini sudah pernah tersimpan."
                ], 422);
            } else {
                $data->update($input);
            }
        }

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        RincianJenisPendapatan::destroy($id);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
