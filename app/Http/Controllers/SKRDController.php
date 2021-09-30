<?php

namespace App\Http\Controllers;

use Auth;
use DateTime;
use Validator;
use DataTables;
use Carbon\Carbon;
use Firebase\JWT\JWT;

use App\Http\Services\VABJB;
use App\Libraries\GenerateNumber;
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

class SKRDController extends Controller
{
    protected $route  = 'skrd.';
    protected $title  = 'SKRD';
    protected $view   = 'pages.skrd.';

    // Check Permission
    public function __construct()
    {
        $this->middleware(['permission:SKRD']);
    }

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

        $data = TransaksiOPD::querySKRD($from, $to, $opd_id, $no_skrd, $status_ttd);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                $path_sftp = 'file_ttd_skrd/';
                $fileName  = str_replace(' ', '', $p->nm_wajib_pajak) . '-' . $p->no_skrd . ".pdf";

                $report  = "<a href='" . route($this->route . 'report', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
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
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_skrd . "</a>";
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

    public function getDataSKRD($id)
    {
        $data = TransaksiOPD::select('no_skrd', 'nm_wajib_pajak', 'jumlah_bayar')->where('id', $id)->first();

        return $data;
    }

    public function getJenisPendapatanByOpd($opd_id)
    {
        $opd_id = \Crypt::decrypt($opd_id);

        $datas = OPDJenisPendapatan::getJenisPendapatanByOpd($opd_id);

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
        $id_rincian_jenis_pendapatan = \Crypt::decrypt($id_rincian_jenis_pendapatan);

        if ($id_rincian_jenis_pendapatan != 0) {
            $data = RincianJenisPendapatan::select('nmr_rekening', 'kd_jenis')->where('id', $id_rincian_jenis_pendapatan)->first();
        } else {
            $data = [
                'nmr_rekening' => "",
                'kd_jenis' => ""
            ];

            $data = json_encode($data);
        }

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

        // Get params
        $opd_id     = $request->opd_id;
        $data_wp_id = $request->data_wp_id;
        $jenis_pendapatan_id = $request->jenis_pendapatan_id;

        // Check data wajib Retribusi
        if (isset($data_wp_id)) {
            // Decrypt params
            $data_wp_id = \Crypt::decrypt($data_wp_id);
        } else {
            // Validation
            if ($opd_id == '' || $jenis_pendapatan_id == '') {
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors('Semua form wajid diisi.');
            }

            // Decrypt params
            $opd_id = \Crypt::decrypt($opd_id);
            $jenis_pendapatan_id = \Crypt::decrypt($jenis_pendapatan_id);
        }

        // Data wajib Retribusi
        $data_wp = DataWP::where('id', $data_wp_id)->first();
        if ($data_wp != null) {
            $opd_id = $data_wp->id_opd;
            $jenis_pendapatan_id = $data_wp->id_jenis_pendapatan;
        }

        $opd = OPD::find($opd_id);
        $jenis_pendapatan = JenisPendapatan::find($jenis_pendapatan_id);
        $kecamatans = Kecamatan::select('id', 'n_kecamatan')->where('kabupaten_id', 40)->get();
        $rincian_jenis_pendapatans = RincianJenisPendapatan::where('id_jenis_pendapatan', $jenis_pendapatan_id)->get();

        return view($this->view . 'create', compact(
            'route',
            'title',
            'opd',
            'jenis_pendapatan',
            'kecamatans',
            'rincian_jenis_pendapatans',
            'data_wp'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_opd'   => 'required',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'id_jenis_pendapatan' => 'required',
            'id_rincian_jenis_pendapatan' => 'required',
            'tgl_ttd' => 'required',
            'nm_ttd'  => 'required',
            'nip_ttd' => 'required'
        ]);

        /* Tahapan : 
         * 1. Generate Nomor (no_skrd & no_bayar)
         * 2. Create Virtual Account
         * 3. tmtransaksi_opd
         * 4. tmdata_wp
         * 5. Save pdf to SFTP Storage
         */

        //* Tahap 1
        $jenisGenerate = 'no_skrd';
        $no_skrd = GenerateNumber::generate($request->id_opd, $request->id_jenis_pendapatan, $jenisGenerate);

        $jenisGenerate = 'no_bayar';
        $no_bayar = GenerateNumber::generate($request->id_opd, $request->id_jenis_pendapatan, $jenisGenerate);

        //TODO: Check Duplikat (no_bayar, no_skrd)
        $checkGenerate = [
            'no_skrd'  => $no_skrd,
            'no_bayar' => $no_bayar
        ];
        Validator::make($checkGenerate, [
            'no_skrd'  => 'required|unique:tmtransaksi_opd,no_skrd',
            'no_bayar' => 'required|unique:tmtransaksi_opd,no_bayar',
        ])->validate();

        //* Tahap 2
        $VABJB   = '';
        $timeNow = Carbon::now();

        $dateTimeNow = new DateTime($timeNow);
        $expired     = new DateTime($request->tgl_skrd_akhir . ' 23:59:59');
        $interval    = $dateTimeNow->diff($expired);
        $daysDiff    = $interval->format('%r%a');

        //TODO: Check Expired Date (jika tgl_skrd_akhir kurang dari tanggal sekarang tidak bisa buat VA)
        if ($daysDiff > 0) {
            $tokenBJB     = $request->token_bjb;
            $clientRefnum = $no_bayar;
            $amount       = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar));
            $expiredDate  = $request->tgl_skrd_akhir . ' 23:59:59';
            $customerName = $request->nm_wajib_pajak;
            $productCode  = $request->kd_jenis;

            //TODO: Get Token BJB
            $resGetTokenBJB = VABJB::getTokenBJB();
            if ($resGetTokenBJB->successful()) {
                $resJson = $resGetTokenBJB->json();
                if ($resJson['rc'] != 0000)
                    return response()->json([
                        'message' => 'Terjadi kegagalan saat mengambil token. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . ''
                    ], 422);
                $tokenBJB = $resJson['data'];
            } else {
                return response()->json([
                    'message' => "Terjadi kegagalan saat mengambil token. Error Code " . $resGetTokenBJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator"
                ], 422);
            }

            //TODO: Create VA BJB
            $resGetVABJB = VABJB::createVABJB($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode);
            if ($resGetVABJB->successful()) {
                $resJson = $resGetVABJB->json();
                if (isset($resJson['rc']) != 0000)
                    return response()->json([
                        'message' => 'Terjadi kegagalan saat membuat Virtual Account. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . ''
                    ], 422);
                $VABJB = $resJson['va_number'];
            } else {
                return response()->json([
                    'message' => "Terjadi kegagalan saat membuat Virtual Account. Error Code " . $resGetVABJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator"
                ], 422);
            }
        }

        //* Tahap 3
        $data = [
            'id_opd'  => $request->id_opd,
            'tgl_ttd' => $request->tgl_ttd,
            'nm_ttd'  => $request->nm_ttd,
            'nip_ttd' => $request->nip_ttd,
            'id_jenis_pendapatan'      => $request->id_jenis_pendapatan,
            'rincian_jenis_pendapatan' => $request->rincian_jenis_pendapatan,
            'id_rincian_jenis_pendapatan' => \Crypt::decrypt($request->id_rincian_jenis_pendapatan),
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
            'nomor_va_bjb'     => $VABJB,
            'status_bayar'     => 0,
            'status_denda'     => 0,
            'status_diskon'    => 0,
            'status_ttd'       => 0,
            'no_skrd'          => $no_skrd,
            'tgl_skrd_awal'    => $request->tgl_skrd_awal,
            'tgl_skrd_akhir'   => $request->tgl_skrd_akhir,
            'no_bayar'         => $no_bayar,
            'created_by'       => Auth::user()->pengguna->full_name
        ];

        $dataSKRD = TransaksiOPD::create($data);

        //* Tahap 4
        $data = [
            'id_opd'  => $request->id_opd,
            'id_jenis_pendapatan'         => $request->id_jenis_pendapatan,
            'id_rincian_jenis_pendapatan' => \Crypt::decrypt($request->id_rincian_jenis_pendapatan),
            'nm_wajib_pajak'   => $request->nm_wajib_pajak,
            'alamat_wp'        => $request->alamat_wp,
            'lokasi'           => $request->lokasi,
            'kelurahan_id'     => $request->kelurahan_id,
            'kecamatan_id'     => $request->kecamatan_id
        ];

        $where = [
            'id_opd' => $request->id_opd,
            'nm_wajib_pajak' => $request->nm_wajib_pajak,
            'id_jenis_pendapatan' => $request->id_jenis_pendapatan,
            'id_rincian_jenis_pendapatan' => \Crypt::decrypt($request->id_rincian_jenis_pendapatan)
        ];

        //TODO: Check existed data WP(wajid pajak) (menyimpan data wp jika belum pernah dibuat)
        $check = DataWP::where($where)->count();
        if ($check == 0)
            DataWP::create($data);


        //* Tahap 5
        $data = TransaksiOPD::find($dataSKRD->id);
        $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'report', compact(
            'data',
            'terbilang'
        ));

        //TODO: get content PDF
        $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-'  . $data->no_skrd . ".pdf";
        $content = $pdf->download()->getOriginalContent();

        //TODO: save PDF to sftp storage
        $path_sftp = 'file_ttd_skrd/';
        Storage::disk('sftp')->put($path_sftp . $fileName, $content);

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
        $rincian_jenis_pendapatans = RincianJenisPendapatan::where('id_jenis_pendapatan', $data->id_jenis_pendapatan)->get();

        return view($this->view . 'edit', compact(
            'route',
            'title',
            'data',
            'rincian_jenis_pendapatans'
        ));
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

    public function update(Request $request, $id)
    {
        $data = TransaksiOPD::find($id);

        /* Tahapan : 
         * 1. Update VA BJB
         * 2. tmtransaksi_opd
         */

        //* Tahap 1
        $amount = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar));
        $expiredDate   = $request->tgl_skrd_akhir . ' 23:59:59';
        $customer_name = $request->nm_wajib_pajak;
        $va_number = (int) $data->nomor_va_bjb;
        $VABJB     = $data->nomor_va_bjb;

        if ($amount != $data->jumlah_bayar || $customer_name != $data->nm_wajib_pajak || $data->tgl_skrd_akhir != $request->tgl_skrd_akhir) {
            //TODO: Get Token BJB
            $resGetTokenBJB = VABJB::getTokenBJB();
            if ($resGetTokenBJB->successful()) {
                $resJson = $resGetTokenBJB->json();
                if ($resJson['rc'] != 0000)
                    return response()->json([
                        'message' => 'Terjadi kegagalan saat mengambil token. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . ''
                    ], 422);
                $tokenBJB = $resJson['data'];
            } else {
                return response()->json([
                    'message' => "Terjadi kegagalan saat mengambil token. Error Code " . $resGetTokenBJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator"
                ], 422);
            }

            //TODO: Update Va BJB
            $resUpdateVABJB = VABJB::updateVaBJB($tokenBJB, $amount, $expiredDate, $customer_name, $va_number);
            if ($resUpdateVABJB->successful()) {
                $resJson = $resUpdateVABJB->json();
                if (isset($resJson['rc']) != 0000)
                    return response()->json([
                        'message' => 'Terjadi kegagalan saat memperbarui Virtual Account. Error Code : ' . $resJson['rc'] . '. Message : ' . $resJson['message'] . ''
                    ], 422);
                $VABJB = $resJson['va_number'];
            } else {
                return response()->json([
                    'message' => "Terjadi kegagalan saat memperbarui Virtual Account. Error Code " . $resUpdateVABJB->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator"
                ], 422);
            }
        }

        //* Tahap 2
        $input = $request->all();
        $input = $request->except('token_bjb');
        $data->update($input);
        $data->update([
            'nomor_va_bjb' => $VABJB,
            'total_bayar'  => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'jumlah_bayar' => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'updated_by'   => Auth::user()->pengguna->full_name,
            'id_rincian_jenis_pendapatan' => \Crypt::decrypt($request->id_rincian_jenis_pendapatan)
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

    public function destroy($id)
    {
        TransaksiOPD::where('id', $id)->delete();

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil dihapus.'
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

    public function updateStatusKirimTTDs(Request $request)
    {
        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        // For Filter
        $from = $request->tgl_skrd;
        $to   = $request->tgl_skrd1;
        $no_skrd    = $request->no_skrd;
        $status_ttd = $request->status_ttd;

        $datas = TransaksiOPD::querySKRD($from, $to, $opd_id, $no_skrd, $status_ttd);
        $dataLength = count($datas);

        // check data if empty
        if ($dataLength == 0)
            return redirect()
                ->route($this->route . 'index')
                ->withErrors('Tidak ada data yang dikirim, pastikan filter data sudah sesuai.');

        // process kirim TTD
        for ($i = 0; $i < $dataLength; $i++) {
            $datas[$i]->update([
                'status_ttd' => 2
            ]);
        }

        return redirect()
            ->route($this->route . 'index')
            ->withSuccess('Selamat! ' . $dataLength . ' Data berhasil dikirim untuk ditandatangan.');
    }
}
