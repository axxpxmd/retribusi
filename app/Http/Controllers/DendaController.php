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

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;

class DendaController extends Controller
{
    protected $route = 'denda.';
    protected $title = 'Denda';
    protected $view  = 'pages.denda.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Denda']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id = Auth::user()->pengguna->opd_id;

        if ($opd_id == 0) {
            $opds = OPD::select('id', 'n_opd')->get();
        } else {
            $opds = OPD::where('id', $opd_id)->get();
        }

        $time = Carbon::now();
        $today = $time->format('Y-m-d');

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'today'
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
        $from  = $request->tgl_skrd;
        $to    = $request->tgl_skrd1;
        $status_denda_filter = $request->status_denda_filter;
        $no_skrd = $request->no_skrd;

        $data = TransaksiOPD::queryDenda($opd_id, $jenis_pendapatan_id, $from, $to, $status_denda_filter, $no_skrd);

        return DataTables::of($data)
            ->editColumn('no_skrd', function ($p) {
                return $p->no_skrd;
                // return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_skrd . "</a>";
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
                return 'Rp. ' . number_format((int) $p->jumlah_bayar);
            })
            ->editColumn('total_bayar', function ($p) {
                return 'Rp. ' . number_format((int) $p->total_bayar);
            })
            ->editColumn('denda', function ($p) {
                if ($p->status_denda == 0) {
                    return '-';
                } else {
                    return ' Rp. ' . number_format((int) $p->denda);
                }
            })
            ->addIndexColumn()
            ->rawColumns(['no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'denda'])
            ->toJson();
    }

    public function updateDenda(Request $request)
    {
        //TODO: Validation
        $status_denda = $request->status_denda;
        if ($status_denda == null)
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Silahkan pilih denda.');

        //TODO: Get params
        $from    = $request->tgl_skrd;
        $to      = $request->tgl_skrd1;
        $no_skrd = $request->no_skrd;
        $jenis_pendapatan_id = $request->jenis_pendapatan_id;
        $status_denda_filter = $request->status_denda_filter;

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $datas = TransaksiOPD::queryDenda($opd_id, $jenis_pendapatan_id, $from, $to, $status_denda_filter, $no_skrd);

        //TODO: Get length datas
        $dataLength = count($datas);
        if ($dataLength == 0)
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Tidak ada data yang diupdate, pastikan filter data sudah sesuai.');

        //* Update tmtransaksi_opd
        for ($i = 0; $i < $dataLength; $i++) {
            $datas[$i]->update([
                'status_denda' => $status_denda,
                'updated_by'   => Auth::user()->pengguna->full_name . ' | Update denda'
            ]);
        }

        return redirect()
            ->route($this->route . 'index')
            ->withSuccess('Selamat! ' . $dataLength . ' Data berhasil diperbaharui.');
    }
}
