<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use Carbon\Carbon;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

// Models
use App\Models\OPD;
use App\Models\Booking;
use App\Models\KuotaBooking;
use App\Models\OPDJenisPendapatan;

class BookingController extends Controller
{
    protected $route = 'booking.';
    protected $title = 'Booking';
    protected $view  = 'pages.booking.';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $today  = Carbon::now()->format('Y-m-d');
        $opd_id = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'opd_id',
            'today'
        ));
    }

    public function api()
    {
        $data = Booking::queryBooking();

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                return "-";
            })
            ->editColumn('no_booking', function ($p) {
                return $p->no_booking;
            })
            ->editColumn('nama', function ($p) {
                return $p->nama;
            })
            ->editColumn('no_hp', function ($p) {
                return $p->no_telp;
            })
            ->editColumn('email', function ($p) {
                return $p->email;
            })
            ->editColumn('status_booking', function ($p) {
                return '-';
            })
            ->editColumn('tgl_booking', function ($p) {
                return $p->tgl_booking;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }

    public function searchNoBooking()
    {
        $route = $this->route;
        $title = $this->title;

        return view($this->view . 'cari', compact(
            'route',
            'title'
        ));
    }

    public function kuotaBooking()
    {
        $route = $this->route;
        $title = $this->title;

        return view($this->view . 'kuotaBooking', compact(
            'route',
            'title'
        ));
    }

    public function apiKuotaBooking()
    {
        $data = KuotaBooking::queryKuotaBooking();

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                return "<a href='#' onclick='modalEdit(" . $p->id . ")' class='text-primary' title='Edit Data'><i class='icon icon-edit'></i></a>";
            })
            ->editColumn('opd', function ($p) {
                return $p->opd ? $p->opd->n_opd : '-';
            })
            ->editColumn('jumlah', function ($p) {
                return $p->jumlah;
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }

    public function getDetailKuotaBooking($id)
    {
        return KuotaBooking::select('tm_kuota_bookings.id as id', 'n_opd', 'jumlah')
            ->join('tmopds', 'tmopds.id', '=', 'tm_kuota_bookings.id_opd')
            ->where('tm_kuota_bookings.id', $id)->first();
    }

    public function updatekuotaBooking(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required'
        ]);

        KuotaBooking::where('id', $id)->update([
            'jumlah' => $request->jumlah
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }
}
