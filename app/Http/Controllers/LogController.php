<?php

namespace App\Http\Controllers;

use DataTables;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

use App\Http\Controllers\Controller;

// Models
use App\Models\TableLog;

class LogController extends Controller
{
    protected $route = 'log.';
    protected $title = 'Log';
    protected $view  = 'pages.log.';

    //* Check Permission
    public function __construct()
    {
        $this->middleware(['permission:Log']);
    }

    public function index(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $today = Carbon::now()->format('Y-m-d');
        $channel_bayar = $request->channel_bayar;
        $from = $request->from;
        $to = $request->to;
        $status = $request->status;

        if ($request->ajax()) {
            return $this->dataTable($channel_bayar, $from, $to, $status);
        }

        return view($this->view . 'index', compact(
            'route',
            'title',
            'today'
        ));
    }

    public function dataTable($channel_bayar, $from, $to, $status)
    {
        $data = TableLog::queryTable($channel_bayar, $from, $to, $status);

        return DataTables::of($data)
            ->editColumn('waktu', function ($p) {
                return $p->waktu ? Carbon::createFromFormat('Y-m-d H:i:s', $p->waktu)->format('d M Y | H:i:s') : '-';
            })
            ->editColumn('status', function ($p) {
                if ($p->status == 1) {
                    return "<span class='badge badge-success'>Berhasil</span>";
                } else {
                    return  "<span class='badge badge-danger'>Gagal</span>";
                }
            })
            ->editColumn('waktu', function($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->waktu . "</a>";
            })
            ->editColumn('nomor_va', function ($p) {
                return $p->dataRetribusi ? $p->dataRetribusi->nomor_va_bjb : '-';
            })
            ->editColumn('invoice_qris', function ($p) {
                return $p->dataRetribusi ? $p->dataRetribusi->invoice_id : '-';
            })
            ->addIndexColumn()
            ->rawColumns(['status', 'waktu'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;
        
        $id   = \Crypt::decrypt($id);
        $data = TableLog::find($id);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data'
        ));
    }
}
