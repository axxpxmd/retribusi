<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;

class DiskonController extends Controller
{
    protected $route = 'diskon.';
    protected $title = 'Diskon';
    protected $view  = 'pages.diskon.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Diskon']);
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
        $status_diskon_filter = $request->status_diskon_filter;
        $no_skrd = $request->no_skrd;

        $data = TransaksiOPD::queryDiskon($opd_id, $jenis_pendapatan_id, $from, $to, $status_diskon_filter, $no_skrd);

        return DataTables::of($data)
            ->editColumn('no_skrd', function ($p) {
                return $p->no_skrd;
                // return "<a href='" . route($this->route . 'show', $p->id) . "' class='text-primary' title='Show Data'>" . $p->no_skrd . "</a>";
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
            ->editColumn('total_bayar', function ($p) {
                return 'Rp. ' . number_format($p->total_bayar);
            })
            ->editColumn('diskon', function ($p) {
                $total_bayar = (int) $p->jumlah_bayar;
                $diskon_percent = (int) $p->diskon / 100;

                $diskon_harga = $diskon_percent * $total_bayar;

                if ($p->status_diskon == 0) {
                    return "-";
                } else {
                    return '( ' . $p->diskon . '% )' . ' Rp. ' . number_format((int) $diskon_harga);
                }
            })
            ->addIndexColumn()
            ->rawColumns(['no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'diskon'])
            ->toJson();
    }

    public function updateDiskon(Request $request)
    {
        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        // For Filter
        $jenis_pendapatan_id = $request->jenis_pendapatan_id;
        $from  = $request->tgl_skrd;
        $to    = $request->tgl_skrd1;
        $status_diskon_filter = $request->status_diskon_filter;
        $no_skrd = $request->no_skrd;

        // Data
        $status_diskon = $request->status_diskon;
        if ($status_diskon == 1) {
            $request->validate([
                'diskon' => 'required|numeric|max:100'
            ]);

            $diskon = $request->diskon;
        } else {
            $diskon = 0;
        }

        $datas = TransaksiOPD::queryDiskon($opd_id, $jenis_pendapatan_id, $from, $to, $status_diskon_filter, $no_skrd);
        $dataLength = count($datas);

        // Check status diskon
        if ($status_diskon == null) {
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Silahkan pilih diskon.');
        }

        // check data if empty
        if ($dataLength == 0)
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Tidak ada data yang diupdate, pastikan filter data sudah sesuai.');

        /**
         * * Check
         * 0 = Tidak Diskon
         * 1 = Diskon
         */
        for ($i = 0; $i < $dataLength; $i++) {
            if ($status_diskon == 1) {
                $total_bayar = $datas[$i]->jumlah_bayar;
                $diskon_percent = $diskon / 100;

                $diskon_harga = $diskon_percent * $total_bayar;
                $total_bayar_update = $total_bayar - $diskon_harga;

                $datas[$i]->update([
                    'total_bayar'   => $total_bayar_update,
                    'status_diskon' => $status_diskon,
                    'diskon' => $diskon
                ]);
            } else {
                $total_bayar_update = $datas[$i]->jumlah_bayar;

                $datas[$i]->update([
                    'total_bayar'   => $total_bayar_update,
                    'status_diskon' => $status_diskon,
                    'diskon' => $diskon
                ]);
            }
        }

        return redirect()
            ->route($this->route . 'index')
            ->withSuccess('Selamat! ' . $dataLength . ' Data berhasil diperbaharui.');
    }
}
