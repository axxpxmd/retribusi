<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

// Models
use App\Models\OPD;
use App\Models\Booking;
use App\Models\OPDJenisPendapatan;

class BookingController extends Controller
{
    protected $route  = 'booking.';
    protected $title  = 'Booking';
    protected $view   = 'pages.booking.';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'opd_id'
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
                return '-';
            })
            ->editColumn('nama', function ($p) {
                return '-';
            })
            ->editColumn('no_hp', function ($p) {
                return '-';
            })
            ->editColumn('email', function ($p) {
                return '-';
            })
            ->editColumn('status_booking', function ($p) {
                return '-';
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->toJson();
    }
}
