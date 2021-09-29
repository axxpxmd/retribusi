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

    public function getDataSKRD($id)
    {
        $data = TransaksiOPD::select('no_skrd', 'nm_wajib_pajak')->where('id', $id)->first();

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

    public function getTokenBJB()
    {
        /* Get Token From Bank BJB
         * TOKEN REQUEST (POST /oauth/client/token)
         */
        $timestamp_now   = Carbon::now()->timestamp;
        $timestamp_1hour = Carbon::now()->addHour()->timestamp;

        $url = config('app.ip_api_bjb');
        $client_id = config('app.client_id_bjb');
        $key = config('app.key_bjb');

        $payload   = array(
            "sub" => "va-online",
            "aud" => "access-token",
            "iat" => $timestamp_now,
            "exp" => $timestamp_1hour
        );

        $jwt = JWT::encode($payload, $key, 'HS256', $client_id); // Create JWT Signature (HMACSHA256)
        $res = Http::contentType("text/plain")->send('POST', $url . 'oauth/client/token', [
            'body' => $jwt
        ]);

        return $res;
    }

    public function create(Request $request)
    {

        $route = $this->route;
        $title = $this->title;

        // Get params
        $opd_id = $request->opd_id;
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

        // Get Token BJB
        $resGetTokenBJB = $this->getTokenBJB();
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

        // Get Time
        $date  = carbon::now();
        $day   = $date->day;
        $month = $date->month;
        $year  = substr($date->year, 2);

        // Check amount OPD by year
        $check = TransaksiOPD::where('id_opd', $opd_id)->where(DB::raw('YEAR(created_at)'), '=', $date->year)->count();
        if ($check != 0) {
            $result = $check + 1;
        } else {
            $result = '1';
        }

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
        $no_bayar = $generateDay . $generateMonth .  $year .  $generateKodeDinas . $generateNoBayar; // tanggal, bulan, tahun, kode dinas, no urut

        return view($this->view . 'create', compact(
            'route',
            'title',
            'opd',
            'jenis_pendapatan',
            'no_skrd',
            'kecamatans',
            'no_bayar',
            'rincian_jenis_pendapatans',
            'data_wp',
            'tokenBJB'
        ));
    }

    public function getVaBJB($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode)
    {
        /* Create Virtual Account from Bank BJB
         * CREATE BILLING REQUEST (POST /billing)
         */

        $url = config('app.ip_api_bjb');
        $key = config('app.key_bjb');
        $timestamp_now = Carbon::now()->timestamp;

        $cin         = config('app.cin_bjb');
        $clientType  = "1";
        $productCode = "01";
        $billingType = "f";
        $vaType      = "a";
        $currency    = "360";
        $description = "Pembayaran Retribusi";

        // Base Signature
        $bodySignature = '{"cin":"' . $cin . '","client_type":"' . $clientType . '","product_code":"' . $productCode . '","billing_type":"' . $billingType . '","va_type":"' . $vaType . '","client_refnum":"' . $clientRefnum . '","amount":"' . $amount . '","currency":"' . $currency . '","expired_date":"' . $expiredDate . '","customer_name":"' . $customerName . '","description":"' . $description . '"}';
        $signature = 'path=/billing&method=POST&token=' . $tokenBJB . '&timestamp=' . $timestamp_now . '&body=' . $bodySignature . '';
        $sha256    = hash_hmac('sha256', $signature, $key);

        // Body / Payload
        $reqBody = [
            "cin"           => $cin,
            "client_type"   => $clientType,
            "product_code"  => $productCode,
            "billing_type"  => $billingType,
            "va_type"       => $vaType,
            "client_refnum" => $clientRefnum,
            "amount"   => $amount,
            "currency" => $currency,
            "expired_date"  => $expiredDate,
            "customer_name" => $customerName,
            "description"   => $description,
        ];

        $res = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenBJB,
            'BJB-Timestamp' => $timestamp_now,
            'BJB-Signature' => $sha256,
            'Content-Type'  => 'application/json'
        ])->post($url . 'billing', $reqBody);

        return $res;
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_opd'   => 'required',
            'no_skrd'  => 'required',
            'no_bayar' => 'required|unique:tmtransaksi_opd,no_bayar',
            'kecamatan_id' => 'required',
            'kelurahan_id' => 'required',
            'id_jenis_pendapatan'         => 'required',
            'id_rincian_jenis_pendapatan' => 'required',
            'tgl_ttd' => 'required',
            'nm_ttd'  => 'required',
            'nip_ttd' => 'required'
        ]);

        /* Tahapan : 
         * 1. Create VA
         * 2. tmtransaksi_opd
         * 3. tmdata_wp
         * 4. Save pdf to SFTP Storage
         */

        // Tahap 1
        $tokenBJB     = $request->token_bjb;
        $clientRefnum = $request->no_bayar;
        $amount       = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar));
        $expiredDate  = $request->tgl_skrd_akhir . ' 23:59:59';
        $customerName = $request->nm_wajib_pajak;
        $productCode  = $request->kd_jenis;

        $resGetVABJB = $this->getVaBJB($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode);

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

        // Tahap 2
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
            'no_skrd'          => $request->no_skrd,
            'tgl_skrd_awal'    => $request->tgl_skrd_awal,
            'tgl_skrd_akhir'   => $request->tgl_skrd_akhir,
            'no_bayar'         => $request->no_bayar,
            'created_by'       => Auth::user()->pengguna->full_name
        ];

        $dataSKRD = TransaksiOPD::create($data);

        // Tahap 3
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

        $check = DataWP::where($where)->count();
        if ($check == 0)
            DataWP::create($data);


        // Tahap 4
        $data = TransaksiOPD::find($dataSKRD->id);
        $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'report', compact(
            'data',
            'terbilang'
        ));

        // get content PDF
        $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-'  . $data->no_skrd . ".pdf";
        $content = $pdf->download()->getOriginalContent();

        // save PDF to sftp storage
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

    public function updateVaBJB($tokenBJB, $amount, $expiredDate, $customer_name, $va_number)
    {

        /* Update Virtual Account from Bank BJB
         * UPDATE BILLING REQUEST (POST /billing/<cin>/<va_number>)
         */

        $url = config('app.ip_api_bjb');
        $key = config('app.key_bjb');
        $timestamp_now = Carbon::now()->timestamp;

        $cin      = config('app.cin_bjb');
        $currency = "360";

        // Base Signature
        $bodySignature = '{"amount":"' . $amount . '","currency":"' . $currency . '","expired_date":"' . $expiredDate . '","customer_name":"' . $customer_name . '"}';
        $signature = 'path=/billing/' . $cin . '/' . $va_number . '&method=POST&token=' . $tokenBJB . '&timestamp=' . $timestamp_now . '&body=' . $bodySignature . '';
        $sha256    = hash_hmac('sha256', $signature, $key);

        // Body / Payload
        $reqBody = [
            "amount"   => $amount,
            "currency" => $currency,
            "expired_date"  => $expiredDate,
            "customer_name" => $customer_name
        ];

        $path = 'billing/' . $cin . '/' . $va_number . '';
        $res  = Http::withHeaders([
            'Authorization' => 'Bearer ' . $tokenBJB,
            'BJB-Timestamp' => $timestamp_now,
            'BJB-Signature' => $sha256,
            'Content-Type'  => 'application/json'
        ])->post($url . $path, $reqBody);

        return $res;
    }

    public function update(Request $request, $id)
    {
        $data = TransaksiOPD::find($id);

        /* Tahapan : 
         * 1. Update VA BJB
         * 2. tmtransaksi_opd
         */

        // Tahap 1
        $amount = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar));
        $expiredDate   = $request->tgl_skrd_akhir . ' 23:59:59';
        $customer_name = $request->nm_wajib_pajak;
        $va_number     = (int) $data->nomor_va_bjb;

        $VABJB = $data->nomor_va_bjb;
        if ($amount != $data->jumlah_bayar) {
            // Get Token BJB
            $resGetTokenBJB = $this->getTokenBJB();
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

            // update VA BJB
            $resUpdateVABJB = $this->updateVaBJB($tokenBJB, $amount, $expiredDate, $customer_name, $va_number);
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

        // Tahap 2
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
