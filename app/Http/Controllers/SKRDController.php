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
use DateTime;
use Validator;
use DataTables;
use Carbon\Carbon;

use App\Http\Services\VABJB;
use App\Libraries\GenerateNumber;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\DataWP;
use App\Models\TtdOPD;
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

    public function __construct(VABJB $vabjb)
    {
        $this->vabjb = $vabjb;

        $this->middleware(['permission:SKRD']);
    }

    public function index()
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id   = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();

        if ($opd_id == 0) {
            $opds = OPD::select('id', 'n_opd')->whereIn('id', $opdArray)->get();
        } else {
            $opds = OPD::where('id', $opd_id)->whereIn('id', $opdArray)->get();
        }

        //TODO: Set filters to date now
        $time  = Carbon::now();
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

                $report  = "<a href='" . route('print.skrd', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
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

        //TODO: Encrypt id
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

        $opd_id     = $request->opd_id;
        $data_wp_id = $request->data_wp_id;
        $jenis_pendapatan_id = $request->jenis_pendapatan_id;

        //TODO: Check data wajib Retribusi
        if (isset($data_wp_id)) {
            $data_wp_id = \Crypt::decrypt($data_wp_id);
        } else {
            //TODO: Validation
            if ($opd_id == '' || $jenis_pendapatan_id == '')
                return redirect()
                    ->route($this->route . 'index')
                    ->withErrors('Semua form wajid diisi.');

            $opd_id = \Crypt::decrypt($opd_id);
            $jenis_pendapatan_id = \Crypt::decrypt($jenis_pendapatan_id);
        }

        //TODO: Get data wajib Retribusi
        $data_wp = DataWP::where('id', $data_wp_id)->first();
        if ($data_wp != null) {
            $opd_id = $data_wp->id_opd;
            $jenis_pendapatan_id = $data_wp->id_jenis_pendapatan;
        }

        $opd = OPD::find($opd_id);
        $jenis_pendapatan = JenisPendapatan::find($jenis_pendapatan_id);
        $kecamatans = Kecamatan::select('id', 'n_kecamatan')->where('kabupaten_id', 40)->get();
        $rincian_jenis_pendapatans = RincianJenisPendapatan::where('id_jenis_pendapatan', $jenis_pendapatan_id)->get();
        $penanda_tangans = TtdOPD::where('id_opd', $opd_id)->get();

        return view($this->view . 'create', compact(
            'route',
            'title',
            'opd',
            'jenis_pendapatan',
            'kecamatans',
            'rincian_jenis_pendapatans',
            'data_wp',
            'penanda_tangans'
        ));
    }

    public function getDiffDays($tgl_skrd_akhir)
    {
        $timeNow = Carbon::now();

        $dateTimeNow = new DateTime($timeNow);
        $expired     = new DateTime($tgl_skrd_akhir . ' 23:59:59');
        $interval    = $dateTimeNow->diff($expired);
        $daysDiff    = $interval->format('%r%a');

        return $daysDiff;
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_opd'  => 'required',
            'tgl_ttd' => 'required',
            'penanda_tangan_id' => 'required',
            'alamat_wp'      => 'required',
            'nmr_daftar'     => 'required|unique:tmtransaksi_opd,nmr_daftar',
            'kecamatan_id'   => 'required',
            'kelurahan_id'   => 'required',
            'kode_rekening'  => 'required',
            'nm_wajib_pajak' => 'required',
            'tgl_skrd_awal'  => 'required|date_format:Y-m-d',
            'tgl_skrd_akhir' => 'required|date_format:Y-m-d',
            'jumlah_bayar'   => 'required',
            'uraian_retribusi'    => 'required',
            'id_jenis_pendapatan' => 'required',
            'id_rincian_jenis_pendapatan' => 'required',
        ]);

        /* Tahapan : 
         * 1. Generate Nomor (no_skrd & no_bayar)
         * 2. Create Virtual Account
         * 3. tmtransaksi_opd
         * 4. tmdata_wp
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
        $daysDiff = $this->getDiffDays($request->tgl_skrd_akhir);

        //TODO: Check Expired Date (jika tgl_skrd_akhir kurang dari tanggal sekarang maka VA tidak terbuat)
        $VABJB   = '';
        if ($daysDiff > 0) {
            $clientRefnum = $no_bayar;
            $amount       = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar));
            $expiredDate  = $request->tgl_skrd_akhir . ' 23:59:59';
            $customerName = $request->nm_wajib_pajak;
            $productCode  = $request->kd_jenis;

            //TODO: Get Token BJB
            $resGetTokenBJB = $this->vabjb->getTokenBJB();
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
            $resGetVABJB = $this->vabjb->createVABJB($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode);
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
        $penanda_tangan = TtdOPD::where('id', $request->penanda_tangan_id)->first();

        $data = [
            'id_opd'  => $request->id_opd,
            'tgl_ttd' => $request->tgl_ttd,
            'nm_ttd'  => $penanda_tangan->user->pengguna->full_name,
            'nip_ttd' => $penanda_tangan->user->pengguna->nik,
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
            'diskon'           => 0,
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
        TransaksiOPD::create($data);

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

        //TODO: Check existed data wajib Retribusi (menyimpan data wp jika belum pernah dibuat)
        $check = DataWP::where($where)->count();
        if ($check == 0)
            DataWP::create($data);

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
        $penanda_tangans = TtdOPD::where('id_opd', $data->id_opd)->get();

        return view($this->view . 'edit', compact(
            'route',
            'title',
            'data',
            'rincian_jenis_pendapatans',
            'penanda_tangans'
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
        $request->validate([
            'tgl_ttd' => 'required',
            'alamat_wp'      => 'required',
            'nmr_daftar'     => 'required|unique:tmtransaksi_opd,nmr_daftar,' . $id,
            'kode_rekening'  => 'required',
            'nm_wajib_pajak' => 'required',
            'tgl_skrd_awal'  => 'required|date_format:Y-m-d',
            'tgl_skrd_akhir' => 'required|date_format:Y-m-d',
            'jumlah_bayar'   => 'required',
            'uraian_retribusi'  => 'required',
            'penanda_tangan_id' => 'required',
            'id_rincian_jenis_pendapatan' => 'required',
        ]);

        /* Tahapan : 
         * 1. Update VA BJB / Create VA BJB
         * 2. tmtransaksi_opd
         */

        //* Tahap 1
        $amount = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar));
        $expiredDate  = $request->tgl_skrd_akhir . ' 23:59:59';
        $customerName = $request->nm_wajib_pajak;
        $va_number    = (int) $data->nomor_va_bjb;
        $VABJB        = $data->nomor_va_bjb;
        $clientRefnum = $data->no_bayar;
        $productCode  = $request->kd_jenis;

        $daysDiff = $this->getDiffDays($request->tgl_skrd_akhir);

        //TODO: Check Expired Date (jika tgl_skrd_akhir kurang dari tanggal sekarang maka VA tidak terbuat)
        if ($daysDiff > 0) {
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

            if ($VABJB == null) {
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
            } else {
                if ($amount != $data->total_bayar || $customerName != $data->nm_wajib_pajak || $data->tgl_skrd_akhir != $request->tgl_skrd_akhir) {
                    //TODO: Update VA BJB
                    $resUpdateVABJB = VABJB::updateVaBJB($tokenBJB, $amount, $expiredDate, $customerName, $va_number);
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
            }
        } else {
            $VABJB = null;
        }

        //* Tahap 2
        $penanda_tangan = TtdOPD::where('id', $request->penanda_tangan_id)->first();

        $input = $request->all();
        $input = $request->except(['kode_rekening', 'kd_jenis', 'penanda_tangan_id']);
        $data->update($input);
        $data->update([
            'nm_ttd'  => $penanda_tangan->user->pengguna->full_name,
            'nip_ttd' => $penanda_tangan->user->pengguna->nik,
            'nomor_va_bjb'  => $VABJB,
            'status_diskon' => 0,
            'diskon'        => 0,
            'total_bayar'   => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'jumlah_bayar'  => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'updated_by'    => Auth::user()->pengguna->full_name . ' | Update data menu SKRD',
            'id_rincian_jenis_pendapatan' => \Crypt::decrypt($request->id_rincian_jenis_pendapatan)
        ]);

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
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

    //* Sedang dihide
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
