<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use DataTables;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Services\VABJB;
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
        //TODO: Validation
        $status_diskon = $request->status_diskon;
        if ($status_diskon == null)
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Silahkan pilih diskon.');


        if ($status_diskon == 1) {
            $request->validate([
                'diskon' => 'required|numeric|max:100'
            ]);

            $diskon = $request->diskon;
        } else {
            $diskon = 0;
        }

        //TODO: Get params
        $to      = $request->tgl_skrd1;
        $from    = $request->tgl_skrd;
        $no_skrd = $request->no_skrd;
        $jenis_pendapatan_id  = $request->jenis_pendapatan_id;
        $status_diskon_filter = $request->status_diskon_filter;

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $datas = TransaksiOPD::queryDiskon($opd_id, $jenis_pendapatan_id, $from, $to, $status_diskon_filter, $no_skrd);

        //TODO: Get length datas
        $dataLength = count($datas);
        if ($dataLength == 0)
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Tidak ada data yang diupdate, pastikan filter data sudah sesuai.');

        //TODO: Get Token BJB
        $resGetTokenBJB = VABJB::getTokenBJB();
        if ($resGetTokenBJB->successful()) {
            $resJson = $resGetTokenBJB->json();
            if ($resJson['rc'] != 0000)
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors('Terjadi kegagalan saat mengambil token. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . '');
            $tokenBJB = $resJson['data'];
        } else {
            return redirect()
                ->route($this->route . 'index')
                ->withErrors("Terjadi kegagalan saat mengambil token. Error Code " . $resGetTokenBJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
        }

        /* Tahapan:
         * 1. Update VA BJB
         * 2. tmtransaksi_opd
         */
        for ($i = 0; $i < $dataLength; $i++) {
            //TODO: Check expired data
            if ($datas[$i]->tgl_strd_akhir == null) {
                $tgl_jatuh_tempo = $datas[$i]->tgl_skrd_akhir;
            } else {
                $tgl_jatuh_tempo = $datas[$i]->tgl_strd_akhir;
            }

            $timeNow = Carbon::now();

            $dateTimeNow = new DateTime($timeNow);
            $expired     = new DateTime($tgl_jatuh_tempo . ' 23:59:59');
            $interval    = $dateTimeNow->diff($expired);
            $daysDiff    = $interval->format('%r%a');

            if ($daysDiff < 0)
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors('Error, No SKRD ' . $datas[$i]->no_skrd . ' tidak bisa diupdate dikarenakan tanggal jatuh tempo kadaluarsa');

            if ($status_diskon == 1) {
                //TODO: Create discount
                $total_bayar    = $datas[$i]->jumlah_bayar;
                $diskon_percent = $diskon / 100;

                $diskon_harga       = $diskon_percent * $total_bayar;
                $total_bayar_update = $total_bayar - $diskon_harga;
            } else {
                $total_bayar_update = $datas[$i]->jumlah_bayar;
            }

            //* Tahap 1
            $amount = \strval((int) str_replace(['.', 'Rp', ' '], '', $total_bayar_update));
            $expiredDate  = $tgl_jatuh_tempo . ' 23:59:59';
            $customerName = $datas[$i]->nm_wajib_pajak;
            $va_number    = (int) $datas[$i]->nomor_va_bjb;

            $resUpdateVABJB = VABJB::updateVaBJB($tokenBJB, $amount, $expiredDate, $customerName, $va_number);
            if ($resUpdateVABJB->successful()) {
                $resJson = $resUpdateVABJB->json();
                if (isset($resJson['rc']) != 0000)
                    return redirect()
                        ->route($this->route . 'index')
                        ->withErrors('Terjadi kegagalan saat memperbarui Virtual Account. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . '');
                $VABJB = $resJson['va_number'];
            } else {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors("Terjadi kegagalan saat memperbarui Virtual Account. Error Code " . $resUpdateVABJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
            }

            //* Tahap 2
            $datas[$i]->update([
                'total_bayar'   => $total_bayar_update,
                'status_diskon' => $status_diskon,
                'nomor_va_bjb'  => $VABJB,
                'diskon'        => $diskon,
                'updated_by'    => Auth::user()->pengguna->full_name . ' | Update diskon'
            ]);
        }

        return redirect()
            ->route($this->route . 'index')
            ->withSuccess('Selamat! ' . $dataLength . ' Data berhasil diperbaharui.');
    }
}
