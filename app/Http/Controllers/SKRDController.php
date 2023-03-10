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
use Validator;
use DataTables;
use Carbon\Carbon;

use App\Libraries\VABJBRes;
use App\Libraries\QRISBJBRes;
use App\Libraries\GenerateNumber;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

// Models
use App\Models\OPD;
use App\Models\DataWP;
use App\Models\TtdOPD;
use App\Models\Utility;
use App\Models\Kecamatan;
use App\Models\Kelurahan;
use App\Models\TransaksiOPD;
use App\Models\TransaksiDelete;
use App\Models\JenisPendapatan;
use App\Models\OPDJenisPendapatan;
use App\Models\RincianJenisPendapatan;

class SKRDController extends Controller
{
    protected $route  = 'skrd.';
    protected $title  = 'SKRD';
    protected $view   = 'pages.skrd.';

    public function __construct(GenerateNumber $generateNumber, VABJBRes $vabjbres, QRISBJBRes $qrisbjbres)
    {
        $this->vabjbres   = $vabjbres;
        $this->qrisbjbres = $qrisbjbres;
        $this->generateNumber = $generateNumber;

        $this->middleware(['permission:SKRD']);
    }

    public function index(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $today = Carbon::now()->format('Y-m-d');
        $role  = Auth::user()->pengguna->modelHasRole->role->name;
        $opd_id   = Auth::user()->pengguna->opd_id == 0 ? $request->opd_id : Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        $from   = $request->from;
        $to     = $request->to;
        $no_skrd    = $request->no_skrd;
        $status_ttd = $request->status_ttd;

        //* Check Duplicate
        $date = Carbon::now();
        $status_duplicate = $request->status_duplicate;
        list($getDuplicate, $data) = TransaksiOPD::checkDuplicateNoBayar($date, $opd_id, $from, $to);

        if ($request->ajax()) {
            return $this->dataTable($from, $to, $opd_id, $no_skrd, $status_ttd, $status_duplicate, $date, $getDuplicate);
        }

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'opd_id',
            'today',
            'opd_id',
            'role',
            'getDuplicate',
            'status_duplicate'
        ));
    }

    public function checkDuplicate(Request $request)
    {
        $from   = $request->from;
        $to     = $request->to;
        $date   = Carbon::now();
        $opd_id = Auth::user()->pengguna->opd_id == 0 ? $request->opd_id : Auth::user()->pengguna->opd_id;

        //* Check Duplicate
        list($getDuplicate, $data) = TransaksiOPD::checkDuplicateNoBayar($date, $opd_id, $from, $to);

        return response()->json([
            'dataDuplicate' => $getDuplicate ? count($getDuplicate) : 0
        ]);
    }

    public function dataTable($from, $to, $opd_id, $no_skrd, $status_ttd, $status_duplicate, $date, $getDuplicate)
    {
        if ($status_duplicate) {
            list($getDuplicate, $data) = TransaksiOPD::checkDuplicateNoBayar($date, $opd_id, $from, $to);
        } else {
            $data = TransaksiOPD::querySKRD($from, $to, $opd_id, $no_skrd, $status_ttd);
        }

        return DataTables::of($data)
            ->addColumn('action', function ($p) use ($getDuplicate) {
                $filettd = "<a href='" . route('print.download', $p->id) . "' target='_blank' class='cyan-text' title='File TTD'><i class='icon-document-file-pdf2'></i></a>";
                $sendttd = "<a href='#' onclick='updateStatusTTD(" . $p->id . ")' class='amber-text' title='Kirim Untuk TTD'><i class='icon icon-send'></i></a>";
                $edit    = "<a href='" . route($this->route . 'edit', Crypt::encrypt($p->id)) . "' class='text-primary mr-2' title='Edit Data'><i class='icon icon-edit'></i></a>";
                $delete  = "<a href='#' onclick='remove(" . $p->id . ")' class='text-danger mr-2' title='Hapus Data'><i class='icon icon-remove'></i></a>";

                //* Sudah TTD
                if ($p->status_ttd == 1) { 
                    return $filettd;
                } else {
                    //* Proses TTD
                    if ($p->status_ttd != 2) {
                        if ($getDuplicate) {
                            foreach ($getDuplicate as $value) {
                                if ($value['no_bayar'] == $p->no_bayar) {
                                    return $edit . $delete;
                                }else{
                                    if ($p->history_ttd == 1) {
                                        return $edit . $sendttd;
                                    } else {
                                        return $edit . $delete . $sendttd;
                                    }
                                }
                            }
                            return $edit . $delete;
                        } else {
                            if ($p->history_ttd == 1) {
                                return $edit . $sendttd;
                            } else {
                                return $edit . $delete . $sendttd;
                            }
                        }
                    } else {
                        return '-';
                    }
                }
            })
            ->editColumn('no_skrd', function ($p) {
                return "<a href='" . route($this->route . 'show', Crypt::encrypt($p->id)) . "' class='text-primary' title='Menampilkan Data'>" . $p->no_skrd . "</a>";
            })
            ->editColumn('no_bayar', function ($p) use ($getDuplicate) {
                $status_ttd = Utility::checkStatusTTD($p->status_ttd);

                $no_bayar = $status_ttd ? $p->no_bayar : substr($p->no_bayar, 0, 6) . 'xxxxxxxx';

                if ($getDuplicate) {
                    foreach ($getDuplicate as $value) {
                        if ($value['no_bayar'] == $p->no_bayar) {
                            return "<span class='text-danger font-weight-bold'>" . $no_bayar . "</span>";
                        } else {
                            return $no_bayar;
                        }
                    }
                } else {
                    return $no_bayar;
                }
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
            ->rawColumns(['action', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_ttd', 'no_bayar'])
            ->toJson();
    }

    //* Get data SKRD for detail TTD
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
            $data = RincianJenisPendapatan::select('nmr_rekening', 'kd_jenis', 'no_hp')->where('id', $id_rincian_jenis_pendapatan)->first();
        } else {
            $data = [
                'nmr_rekening' => "",
                'kd_jenis' => "",
                'no_hp' => ""
            ];

            $data = json_encode($data);
        }

        return $data;
    }

    public function kelurahanByKecamatan($id)
    {
        $data = Kelurahan::select('id', 'kecamatan_id', 'n_kelurahan')->where('kecamatan_id', $id)->get();

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
        $kecamatans       = Kecamatan::select('id', 'n_kecamatan')->where('kabupaten_id', 40)->get();
        $penanda_tangans  = TtdOPD::where('id_opd', $opd_id)->get();
        $jenis_pendapatan = JenisPendapatan::find($jenis_pendapatan_id);
        $rincian_jenis_pendapatans = RincianJenisPendapatan::where('id_jenis_pendapatan', $jenis_pendapatan_id)->get();

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

    public function store(Request $request)
    {
        $request->validate([
            'id_opd'  => 'required',
            'tgl_ttd' => 'required',
            'no_telp' => 'required',
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
            'id_rincian_jenis_pendapatan' => 'required'
        ]);

        /* Tahapan : 
         * 1. Generate Nomor (no_skrd & no_bayar)
         * 2. tmtransaksi_opd (store)
         * 3. Create Virtual Account
         * 4. Create QRIS
         * 5. tmdata_wp (store)
         */

        //* Tahap 1
        $jenisGenerate = 'no_skrd';
        $no_skrd = $this->generateNumber->generate($request->id_opd, $request->id_jenis_pendapatan, $jenisGenerate);

        $jenisGenerate = 'no_bayar';
        $no_bayar = $this->generateNumber->generate($request->id_opd, $request->id_jenis_pendapatan, $jenisGenerate);

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
        DB::beginTransaction(); //* DB Transaction Begin

        $penanda_tangan = TtdOPD::where('id', $request->penanda_tangan_id)->first();

        $data = [
            'id_opd'  => $request->id_opd,
            'tgl_ttd' => $request->tgl_ttd,
            'nm_ttd'  => $penanda_tangan->user->pengguna->full_name,
            'nip_ttd' => $penanda_tangan->user->pengguna->nip,
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
            'nomor_va_bjb'     => null,
            'invoice_id'       => null,
            'text_qris'        => null,
            'status_bayar'     => 0,
            'status_denda'     => 0,
            'status_diskon'    => 0,
            'status_ttd'       => 0,
            'no_skrd'          => $no_skrd,
            'tgl_skrd_awal'    => $request->tgl_skrd_awal,
            'tgl_skrd_akhir'   => $request->tgl_skrd_akhir,
            'no_bayar'         => $no_bayar,
            'created_by'       => Auth::user()->pengguna->full_name,
            'email'            => $request->email,
            'no_telp'          => $request->no_telp
        ];
        $dataSKRD = TransaksiOPD::create($data);

        $clientRefnum = $no_bayar;
        $amount       = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar));
        $expiredDate  = $request->tgl_skrd_akhir . ' 23:59:59';
        $customerName = $request->nm_wajib_pajak;
        $productCode  = $request->kd_jenis;
        $no_hp = $request->no_hp;

        //*: Check Expired Date (jika tgl_skrd_akhir kurang dari tanggal sekarang maka VA dan QRIS tidak terbuat)
        //*: Check Amount (jika nominal 0 rupiah makan VA dan QRIS tidak terbuat)
        list($dayDiff, $monthDiff) = Utility::getDiffDate($request->tgl_skrd_akhir);
        if ($dayDiff > 0 && $amount != 0) {
            //* Tahap 3
            //TODO: Get Token VA
            list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
            if ($err) {
                DB::rollback(); //* DB Transaction Failed
                return response()->json([
                    'message' => $errMsg
                ], 500);
            }

            //TODO: Create VA
            list($err, $errMsg, $VABJB) = $this->vabjbres->createVABJBres($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode, 1, $no_bayar);
            if ($err) {
                DB::rollback(); //* DB Transaction Failed
                return response()->json([
                    'message' => $errMsg
                ], 500);
            } else {
                //* Update data SKRD
                $dataSKRD->update([
                    'nomor_va_bjb' => $VABJB
                ]);
            }

            //* Tahap 4
            if ($amount <= 10000000) { //* Nominal QRIS maksimal 10 juta, jika lebih maka tidak terbuat
                //TODO: Get Token QRIS
                list($err, $errMsg, $tokenQRISBJB) = $this->qrisbjbres->getTokenQrisres();
                if ($err) {
                    DB::rollback(); //* DB Transaction Failed
                    return response()->json([
                        'message' => $errMsg
                    ], 500);
                }

                //TODO: Create QRIS
                list($err, $errMsg, $invoiceId, $textQRIS) = $this->qrisbjbres->createQRISres($tokenQRISBJB, $amount, $no_hp, 1, $no_bayar);
                if ($err) {
                    DB::rollback(); //* DB Transaction Failed
                    return response()->json([
                        'message' => $errMsg
                    ], 500);
                } else {
                    //* Update data SKRD
                    $dataSKRD->update([
                        'invoice_id' => $invoiceId,
                        'text_qris' => $textQRIS
                    ]);
                }
            }
        }

        DB::commit(); //* DB Transaction Success

        //* LOG
        Log::channel('skrd_create')->info('Create Data SKRD', $dataSKRD->toArray());

        //* Tahap 5
        $data = [
            'email'   => $request->email,
            'id_opd'  => $request->id_opd,
            'lokasi'  => $request->lokasi,
            'no_telp' => $request->no_telp,
            'alamat_wp'      => $request->alamat_wp,
            'kelurahan_id'   => $request->kelurahan_id,
            'kecamatan_id'   => $request->kecamatan_id,
            'nm_wajib_pajak' => $request->nm_wajib_pajak,
            'id_jenis_pendapatan'         => $request->id_jenis_pendapatan,
            'id_rincian_jenis_pendapatan' => \Crypt::decrypt($request->id_rincian_jenis_pendapatan),
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
        $penanda_tangans = TtdOPD::where('id_opd', $data->id_opd)->get();
        $rincian_jenis_pendapatans = RincianJenisPendapatan::where('id_jenis_pendapatan', $data->id_jenis_pendapatan)->get();

        // Check Duplicate
        $checkDuplicate = TransaksiOPD::where('no_bayar', $data->no_bayar)->count();

        return view($this->view . 'edit', compact(
            'route',
            'title',
            'data',
            'rincian_jenis_pendapatans',
            'penanda_tangans',
            'checkDuplicate'
        ));
    }

    public function generate($id)
    {
        $data = TransaksiOPD::find($id);
        $id_opd = $data->id_opd;
        $id_jenis_pendapatan = $data->id_jenis_pendapatan;

        $jenisGenerate = 'no_skrd';
        $no_skrd = $this->generateNumber->generate($id_opd, $id_jenis_pendapatan, $jenisGenerate);

        $jenisGenerate = 'no_bayar';
        $no_bayar = $this->generateNumber->generate($id_opd, $id_jenis_pendapatan, $jenisGenerate);

        //TODO: Check Duplikat (no_bayar, no_skrd)
        $checkGenerate = [
            'no_skrd'  => $no_skrd,
            'no_bayar' => $no_bayar
        ];
        Validator::make($checkGenerate, [
            'no_skrd'  => 'required|unique:tmtransaksi_opd,no_skrd',
            'no_bayar' => 'required|unique:tmtransaksi_opd,no_bayar',
        ])->validate();

        $data->update([
            'no_bayar' => $no_bayar,
            'no_skrd' => $no_skrd
        ]);

        return redirect()
            ->route($this->route . 'edit', Crypt::encrypt($id))
            ->withSuccess('No Bayar dan No SKRD berhasil diperbaharui.');
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);

        $fileName   = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp  = 'file_ttd_skrd/';
        $status_ttd = $data->status_ttd;

        $status_ttd = Utility::checkStatusTTD($status_ttd);

        return view($this->view . 'show', compact(
            'route',
            'title',
            'data',
            'path_sftp',
            'fileName',
            'status_ttd'
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
         * 1. VA
         * 2. QRIS
         * 3. tmtransaksi_opd
         */
        $no_hp   = $request->no_hp;
        $no_telp = $request->no_telp;
        $email   = $request->email;
        $amount  = \strval((int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar));
        $expiredDate  = $request->tgl_skrd_akhir . ' 23:59:59';
        $customerName = $request->nm_wajib_pajak;
        $va_number    = (int) $data->nomor_va_bjb;
        $VABJB        = $data->nomor_va_bjb;
        $invoiceId    = $data->invoice_id;
        $textQRIS     = $data->text_qris;
        $clientRefnum = $data->no_bayar;
        $productCode  = $request->kd_jenis;

        //*: Check Expired Date (jika tgl_skrd_akhir kurang dari tanggal sekarang maka VA dan QRIS tidak terbuat)
        //*: Check Amount (jika nominal 0 rupiah makan VA dan QRIS tidak terbuat)
        list($dayDiff, $monthDiff) = Utility::getDiffDate($request->tgl_skrd_akhir);
        if ($dayDiff > 0 && $amount != 0) {
            //* Tahap 1
            //TODO: Get Token BJB
            list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
            if ($err) {
                DB::rollback(); //* DB Transaction Failed
                return response()->json([
                    'message' => $errMsg
                ], 500);
            }

            if ($VABJB == null) {
                //TODO: Create VA BJB
                list($err, $errMsg, $VABJB) = $this->vabjbres->createVABJBres($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode, 2, $clientRefnum);
                if ($err) {
                    DB::rollback(); //* DB Transaction Failed
                    return response()->json([
                        'message' => $errMsg
                    ], 500);
                }
            } else {
                if ($amount != $data->total_bayar || $customerName != $data->nm_wajib_pajak || $data->tgl_skrd_akhir != $request->tgl_skrd_akhir) {
                    //TODO: Update VA BJB
                    list($err, $errMsg, $VABJB) = $this->vabjbres->updateVABJBres($tokenBJB, $amount, $expiredDate, $customerName, $va_number, 1, $clientRefnum);
                    if ($err) {
                        DB::rollback(); //* DB Transaction Failed
                        return response()->json([
                            'message' => $errMsg
                        ], 500);
                    }
                }
            }

            //* Tahap 2
            if ($amount <= 10000000) { //* Nominal QRIS maksimal 10 juta, jika lebih maka tidak terbuat
                if ($data->total_bayar != $amount) {
                    //TODO: Get Token QRIS
                    list($err, $errMsg, $tokenQRISBJB) = $this->qrisbjbres->getTokenQrisres();
                    if ($err) {
                        DB::rollback(); //* DB Transaction Failed
                        return response()->json([
                            'message' => $errMsg
                        ], 500);
                    }

                    // TODO: Create QRIS
                    list($err, $errMsg, $invoiceId, $textQRIS) = $this->qrisbjbres->createQRISres($tokenQRISBJB, $amount, $no_hp, 2, $clientRefnum);
                    if ($err) {
                        DB::rollback(); //* DB Transaction Failed
                        return response()->json([
                            'message' => $errMsg
                        ], 500);
                    }
                } else {
                    $invoiceId = $data->invoice_id;
                    $textQRIS = $data->text_qris;
                }
            } else {
                $invoiceId = null;
                $textQRIS = null;
            }
        } else {
            $VABJB = null;
            $invoiceId = null;
            $textQRIS = null;
        }

        //* Tahap 3
        $penanda_tangan = TtdOPD::where('id', $request->penanda_tangan_id)->first();

        $input = $request->all();
        $input = $request->except(['kode_rekening', 'kd_jenis', 'penanda_tangan_id', 'no_hp']);
        $data->update($input);
        $data->update([
            'nm_ttd'  => $penanda_tangan->user->pengguna->full_name,
            'nip_ttd' => $penanda_tangan->user->pengguna->nip,
            'nomor_va_bjb'  => $VABJB,
            'invoice_id'    => $invoiceId,
            'text_qris'     => $textQRIS,
            'status_diskon' => 0,
            'diskon'        => 0,
            'total_bayar'   => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'jumlah_bayar'  => (int) str_replace(['.', 'Rp', ' '], '', $request->jumlah_bayar),
            'updated_by'    => Auth::user()->pengguna->full_name . ' | Update data menu SKRD',
            'id_rincian_jenis_pendapatan' => \Crypt::decrypt($request->id_rincian_jenis_pendapatan),
            'email'   => $email,
            'no_telp' => $no_telp
        ]);

        //* LOG
        Log::channel('skrd_edit')->info('Edit Data SRKD | ' . 'Oleh:' . Auth::user()->pengguna->full_name, $data->toArray());

        return response()->json([
            'message' => 'Data ' . $this->title . ' berhasil diperbaharui.'
        ]);
    }

    public function destroy($id)
    {
        /* Tahapan :
         * 1. tmtransaksi_opd (delete)
         * 2. Backup Data (Store)
         * 3. Update VA (make va expired)
         */

        //* Tahap 1
        $data = TransaksiOPD::where('id', $id)->first();

        //* Tahap 2
        $dataBackup = $data->toArray();
        TransaksiDelete::create(array_merge($dataBackup, ['updated_by' => Auth::user()->pengguna->full_name . ' | Hapus']));

        //* Tahap 3
        $amount = \strval((int) str_replace(['.', 'Rp', ' '], '', $data->jumlah_bayar));
        $customerName = $data->nm_wajib_pajak;
        $va_number    = (int) $data->nomor_va_bjb;
        $clientRefnum = $data->no_bayar;

        if ($va_number) {
            $expiredDate = Carbon::now()->addMinutes(20)->format('Y-m-d H:i:s');

            //TODO: Get Token VA
            list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
            if ($err) {
                DB::rollback(); //* DB Transaction Failed
                return response()->json([
                    'message' => $errMsg
                ], 500);
            }

            //TODO: Update VA BJB (make Va expired)
            list($err, $errMsg, $VABJB) = $this->vabjbres->updateVABJBres($tokenBJB, $amount, $expiredDate, $customerName, $va_number, 2, $clientRefnum);
            if ($err) {
                DB::rollback(); //* DB Transaction Failed
                return response()->json([
                    'message' => $errMsg
                ], 500);
            }
        }

        //* LOG
        Log::channel('skrd_delete')->info('Hapus Data SRKD | ' . 'Oleh:' . Auth::user()->pengguna->full_name, $data->toArray());

        $data->delete();

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
}
