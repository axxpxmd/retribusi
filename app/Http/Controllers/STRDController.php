<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

// Models
use App\Models\OPD;
use App\Models\DataWP;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TransaksiOPD;
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;
use App\Models\RincianJenisPendapatan;

class STRDController extends Controller
{
    protected $route = 'strd.';
    protected $view  = 'pages.strd.';
    protected $title = 'STRD';
    protected $path  = '';

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();

        if ($opd_id == 0) {
            $opds = OPD::select('id', 'n_opd')->whereIn('id', $opdArray)->get();
        } else {
            $opds = OPD::where('id', $opd_id)->whereIn('id', $opdArray)->get();
        }

        $time = Carbon::now();
        $today = $time->format('Y-m-d');

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'opd_id',
            'today'
        ));
    }

    public function api(Request $request)
    {
        $from  = $request->tgl_skrd;
        $to    = $request->tgl_skrd1;
        $no_skrd    = $request->no_skrd;
        $status_ttd = $request->status_ttd;

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::querySTRD($from, $to, $opd_id, $no_skrd, $status_ttd);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                $path_sftp = 'file_ttd_skrd/';
                $fileName  = str_replace(' ', '', $p->nm_wajib_pajak) . '-' . $p->no_skrd . ".pdf";

                $report  = "<a href='" . route('skrd.report', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                $filettd = "<a href='" . config('app.sftp_src') . $path_sftp . $fileName . "' target='_blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";
                $sendttd = "<a href='#' onclick='updateStatusTTD(" . $p->id . ")' class='amber-text' title='Kirim Untuk TTD'><i class='icon icon-send'></i></a>";
                $edit    = "<a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary mr-2' title='Edit Data'><i class='icon icon-edit'></i></a>";
                $delete  = "<a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus Data'><i class='icon icon-remove'></i></a>";

                if ($p->status_ttd == 1) {
                    return $filettd;
                } else {
                    if ($p->status_ttd != 2) {
                        return $edit . $delete . $sendttd;
                    }
                    return '-';
                }
            })
            ->editColumn('no_skrd', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Show Data'>" . $p->no_skrd . "</a>";
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
            ->addColumn('status_ttd', function ($p) {
                if ($p->status_ttd == 0) {
                    return "<span class='badge badge-danger'>Belum</span>";
                } elseif ($p->status_ttd == 1) {
                    return "<span class='badge badge-success'>Sudah</span>";
                } elseif ($p->status_ttd == 2) {
                    return "<span class='badge badge-warning'>Proses</span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_ttd'])
            ->toJson();
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'path_sftp',
            'fileName'
        ));
    }

    public function printData(Request $request, $id)
    {
        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);
        $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

        // Update Jumlah Cetak
        $this->updateJumlahCetak($id, $data->jumlah_cetak);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'report', compact(
            'data',
            'terbilang'
        ));

        return $pdf->stream($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }

    public function updateJumlahCetak($id, $jumlah_cetak)
    {
        $time    = Carbon::now();
        $tanggal = $time->toDateString();
        $jam     = $time->toTimeString();
        $now     = $tanggal . ' ' . $jam;

        TransaksiOPD::where('id', $id)->update([
            'jumlah_cetak' => $jumlah_cetak + 1,
            'tgl_cetak_trkhr' => $now
        ]);
    }

    public function updateStatusKirimTTD($id)
    {
        $data = TransaksiOPD::find($id);
        $data->update([
            'status_ttd' => 2
        ]);

        return redirect()
            ->route($this->route . 'index')
            ->withSuccess('Selamat! Data berhasil dikirim untuk ditandatangan.');
    }

    public function destroy($id)
    {
        TransaksiOPD::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
