<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use DataTables;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TransaksiOPD;
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;
use App\Models\RincianJenisPendapatan;

class SKRDController extends Controller
{
    protected $route = 'skrd.';
    protected $title = 'SKRD';
    protected $view  = 'pages.skrd.';

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
        $to = $request->tgl_skrd1;

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0 || $checkOPD == 99999) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::querySKRD($from, $to, $opd_id);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                if ($p->status_bayar == 0) {
                    return "
                        <a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus Data'><i class='icon icon-remove'></i></a>
                        <a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary mr-2' title='Edit Data'><i class='icon icon-edit'></i></a>
                        <a href='" . route($this->route . 'report', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                } else {
                    return "
                        <a href='" . route($this->route . 'report', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
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
            ->addIndexColumn()
            ->rawColumns(['action', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku'])
            ->toJson();
    }

    public function getJenisPendapatanByOpd($opd_id)
    {
        $opd_id = \Crypt::decrypt($opd_id);

        $datas = OPDJenisPendapatan::select('tmopd_jenis_pendapatan.id_jenis_pendapatan as id', 'tmjenis_pendapatan.jenis_pendapatan')
            ->join('tmjenis_pendapatan', 'tmjenis_pendapatan.id', '=', 'tmopd_jenis_pendapatan.id_jenis_pendapatan')
            ->where('tmopd_jenis_pendapatan.id_opd', $opd_id)
            ->get();

        // encrypt id
        if ($datas->count() == 0) {
            $response = [];
        } else {
            foreach ($datas as $key => $value) {
                $response[$key] = [
                    'id' => Crypt::encrypt($value->id),
                    'jenis_pendapatan' => $value->jenis_pendapatan
                ];
            }
        }

        return $response;
    }

    public function getKodeRekening($id_rincian_jenis_pendapatan)
    {
        $id_rincian_jenis_pendapatan = $id_rincian_jenis_pendapatan;

        $data = RincianJenisPendapatan::select('nmr_rekening')->where('id', $id_rincian_jenis_pendapatan)->first();

        return $data;
    }

    public function kelurahanByKecamatan($id)
    {
        $data = Kelurahan::where('kecamatan_id', $id)->get();

        return $data;
    }

    public function create(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id = $request->opd_id;
        $jenis_pendapatan_id = $request->jenis_pendapatan_id;


        if ($opd_id == '' || $jenis_pendapatan_id == '') {
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Semua form wajid diisi.');
        }

        $opd_id = \Crypt::decrypt($opd_id);
        $jenis_pendapatan_id = \Crypt::decrypt($jenis_pendapatan_id);

        $opd = OPD::find($opd_id);
        $jenis_pendapatan = JenisPendapatan::find($jenis_pendapatan_id);
        $kecamatans = Kecamatan::select('id', 'n_kecamatan')->get();
        $rincians = RincianJenisPendapatan::where('id_jenis_pendapatan', $jenis_pendapatan_id)->get();

        $check = TransaksiOPD::where('id_opd', $opd_id)->count();

        // Kode Dinas
        $kodeDinas = $opd->id;
        if (\strlen($kodeDinas) == 1) {
            $generateKodeDinas = '0' . $kodeDinas;
        } elseif (\strlen($kodeDinas) == 2) {
            $generateKodeDinas = $kodeDinas;
        }

        // Jenis Pendapatan
        if (\strlen($jenis_pendapatan_id) == 1) {
            $generateJenisPendapatanId = '0' . $jenis_pendapatan_id;
        } elseif (\strlen($jenis_pendapatan_id) == 2) {
            $generateJenisPendapatanId = $jenis_pendapatan_id;
        }

        // Get Time
        $date = carbon::now();
        $day = $date->day;
        $month = $date->month;
        $year = substr($date->year, 2);

        // Day
        if (\strlen($day) == 1) {
            $generateDay = '0' . $day;
        } elseif (\strlen($day) == 2) {
            $generateDay = $day;
        }

        // Month
        if (\strlen($month) == 1) {
            $generateMonth = '0' . $month;
        } elseif (\strlen($month) == 2) {
            $generateMonth = $month;
        }

        // Antrian No SKRD
        if ($check != 0) {
            $result = $check + 1;
        } else {
            $result = '1';
        }
        if (\strlen($result) == 1) {
            $generateSKRD = '000' . $result;
        } elseif (\strlen($result) == 2) {
            $generateSKRD = '00' . $result;
        } elseif (\strlen($result) == 3) {
            $generateSKRD = '0' . $result;
        } elseif (\strlen($result) == 4) {
            $generateSKRD = $result;
        }

        // Antrian No Bayar
        if ($check != 0) {
            $result = $check + 1;
        } else {
            $result = '1';
        }
        if (\strlen($result) == 1) {
            $generateNoBayar = '0000' . $result;
        } elseif (\strlen($result) == 2) {
            $generateNoBayar = '000' . $result;
        } elseif (\strlen($result) == 3) {
            $generateNoBayar = '00' . $result;
        } elseif (\strlen($result) == 4) {
            $generateNoBayar = '0' . $result;
        } elseif (\strlen($result) == 5) {
            $generateNoBayar = $result;
        }

        $no_skrd = $generateKodeDinas . '.' . $generateJenisPendapatanId . '.'  . $generateSKRD;
        $no_bayar = $generateDay . $generateMonth .  $year .  $generateKodeDinas . $generateNoBayar;

        return view($this->view . 'create', compact(
            'route',
            'title',
            'opd',
            'jenis_pendapatan',
            'no_skrd',
            'kecamatans',
            'no_bayar',
            'rincians'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_skrd' => 'required|unique:tmtransaksi_opd,no_skrd',
            'id_rincian_jenis_pendapatan' => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required'
        ]);

        // get data
        $data = [
            'id_opd'  => $request->id_opd,
            'tgl_ttd' => $request->tgl_ttd,
            'nm_ttd'  => $request->nm_ttd,
            'nip_ttd' => $request->nip_ttd,
            'id_jenis_pendapatan'      => $request->id_jenis_pendapatan,
            'rincian_jenis_pendapatan' => $request->rincian_jenis_pendapatan,
            'id_rincian_jenis_pendapatan' => $request->id_rincian_jenis_pendapatan,
            'nmr_daftar'       => $request->nmr_daftar,
            'nm_wajib_pajak'   => $request->nm_wajib_pajak,
            'alamat_wp'        => $request->alamat_wp,
            'lokasi'           => $request->lokasi,
            'kelurahan_id'     => $request->kelurahan_id,
            'kecamatan_id'     => $request->kecamatan_id,
            'uraian_retribusi' => $request->uraian_retribusi,
            'jumlah_bayar'     => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'denda'            => 0,
            'total_bayar'      => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'status_bayar'     => 0,
            'status_denda'     => 0,
            'status_diskon'    => 0,
            'no_skrd'          => $request->no_skrd,
            'tgl_skrd_awal'    => $request->tgl_skrd_awal,
            'tgl_skrd_akhir'   => $request->tgl_skrd_akhir,
            'no_bayar'         => $request->no_bayar
        ];

        TransaksiOPD::create($data);

        return response()->json([
            'message' => "Data " . $this->title . " berhasil tersimpan."
        ]);
    }

    public function edit($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        $rincians = RincianJenisPendapatan::where('id_jenis_pendapatan', $data->id_jenis_pendapatan)->get();

        return view($this->view . 'edit', compact(
            'route',
            'title',
            'data',
            'rincians'
        ));
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data'
        ));
    }

    public function update(Request $request, $id)
    {
        $data = TransaksiOPD::find($id);

        $input = $request->all();

        $data->update($input);
        $data->update([
            'total_bayar' => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'jumlah_bayar' => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar)
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function printData(Request $request, $id)
    {
        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);
        $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'report', compact(
            'data',
            'terbilang'
        ));

        $time    = Carbon::now();
        $tanggal = $time->toDateString();
        $jam     = $time->toTimeString();
        $now = $tanggal . ' ' . $jam;

        // Update Jumlah Cetak
        $data->update([
            'jumlah_cetak' => $data->jumlah_cetak + 1,
            'tgl_cetak_trkhr' => $now
        ]);

        return $pdf->download($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }

    public function destroy($id)
    {
        TransaksiOPD::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
        ]);
    }
}
