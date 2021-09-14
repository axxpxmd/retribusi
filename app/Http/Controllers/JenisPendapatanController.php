<?php

namespace App\Http\Controllers;

use DataTables;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Modles
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;
use App\Models\TransaksiOPD;

class JenisPendapatanController extends Controller
{
    protected $route = 'jenis-pendapatan.';
    protected $title = 'Jenis Pendapatan';
    protected $view  = 'pages.jenisPendapatan.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Jenis Pendapatan']);
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
        $jenisPendapatans = JenisPendapatan::orderBy('id', 'DESC')->get();

        return DataTables::of($jenisPendapatans)
            ->addColumn('action', function ($p) {
                $check = OPDJenisPendapatan::where('id_jenis_pendapatan', $p->id)->count();
                $check1 = TransaksiOPD::where('id_jenis_pendapatan', $p->id)->count();
                if ($check != 0 || $check1 != 0) {
                    return "<a href='#' onclick='edit(" . $p->id . ")' title='Edit Permission'><i class='icon-edit mr-1'></i></a>";
                } else {
                    return "<a href='#' onclick='edit(" . $p->id . ")' title='Edit Permission'><i class='icon-edit mr-1'></i></a>
                            <a href='#' onclick='remove(" . $p->id . ")' class='text-danger' title='Hapus Role'><i class='icon-remove'></i></a>";
                }
            })
            ->editColumn('target_pendapatan', function ($p) {
                if ($p->target_pendapatan != null) {
                    return 'Rp. ' . number_format($p->target_pendapatan);
                } else {
                    return '-';
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate([
            'jenis_pendapatan' => 'required|unique:tmjenis_pendapatan,jenis_pendapatan',
        ]);

        $input = [
            'jenis_pendapatan'  => $request->jenis_pendapatan,
            'target_pendapatan' => (int) str_replace(['.', 'Rp', ' '], '', $request->target_pendapatan)
        ];

        JenisPendapatan::create($input);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil tersimpan.'
        ]);
    }

    public function edit($id)
    {
        $jenis_pendapatan = JenisPendapatan::find($id);
        return $jenis_pendapatan;
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jenis_pendapatan' => 'required|unique:tmjenis_pendapatan,jenis_pendapatan,' . $id,
        ]);

        $input = [
            'jenis_pendapatan'  => $request->jenis_pendapatan,
            'target_pendapatan' => (int) str_replace(['.', 'Rp', ' '], '', $request->target_pendapatan)
        ];

        $jenis_pendapatan = JenisPendapatan::where('id', $id)->first();
        $jenis_pendapatan->update($input);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        JenisPendapatan::destroy($id);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
