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
use DataTables;
use Carbon\Carbon;

use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;
use App\Http\Services\Iontentik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

// Models
use App\Models\OPD;
use App\Models\Utility;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;

class TandaTanganController extends Controller
{
    protected $route = 'tanda-tangan.';
    protected $title = 'Tanda Tangan';
    protected $view  = 'pages.tandaTangan.';

    // Check Permission
    public function __construct(Iontentik $iotentik)
    {
        $this->iotentik = $iotentik;

        $this->middleware(['permission:Tanda Tangan']);
    }

    public function index(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id   = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);

        $time = Carbon::now();
        $today = $time->format('Y-m-d');
        $lastWeek = $time->subDay(7)->format('Y-m-d');

        $from   = $request->tgl_skrd;
        $to     = $request->tgl_skrd1;
        $opd_id = $opd_id == 0 ? $request->opd_id : $opd_id;
        $belum_ttd  = $request->belum_ttd;
        $no_skrd    = $request->no_skrd;
        $status_ttd = $request->status_ttd;

        if ($request->ajax()) {
            return $this->dataTable($belum_ttd, $from, $to, $opd_id, $no_skrd, $status_ttd);
        }

        return view($this->view . 'index', compact(
            'route',
            'title',
            'opds',
            'opd_id',
            'today',
            'lastWeek',
            'belum_ttd'
        ));
    }

    public function dataTable($belum_ttd, $from, $to, $opd_id, $no_skrd, $status_ttd)
    {
        $data = TransaksiOPD::queryTandaTangan($belum_ttd, $from, $to, $opd_id, $no_skrd, $status_ttd);

        return DataTables::of($data)
            ->addColumn('file_ttd', function ($p) {
                $path_sftp  = 'file_ttd_skrd/';
                $fileName   = str_replace(' ', '', $p->nm_wajib_pajak) . '-' . $p->no_skrd . ".pdf";

                $status_ttd = Utility::checkStatusTTD($p->status_ttd);

                if ($status_ttd) {
                    return "<a href='" . config('app.sftp_src') . $path_sftp . $fileName . "' target='_blank' class='cyan-text'><i class='icon-document-file-pdf2'></i></a>";
                } else {
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
                if ($p->status_ttd == 3 || $p->status_ttd == 1) {
                    return "<span class='badge badge-success'>Sudah TTD</span>";
                } else {
                    return "<span class='badge badge-danger'>Belum TTD</span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['file_ttd', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_ttd'])
            ->toJson();
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

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $nip = $data->nip_ttd;
        $nik = Auth::user()->pengguna->nik;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        $status_bayar   = $data->status_bayar;
        $tgl_bayar      = $data->tgl_bayar;
        $denda          = $data->denda;
        $tgl_strd_akhir = $data->tgl_strd_akhir;
        $status_ttd     = $data->status_ttd;
        $text_qris      = $data->text_qris;

        $path_sftp   = 'file_ttd_skrd/';
        $path_local  = 'public/file_skrd/';
        $fileName    = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $file_url    = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar, $tgl_bayar);
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar);
            }
        }

        //TODO Total Bayar + Bunga
        $total_bayar = Utility::createDenda($status_bayar, $total_bayar, $denda, $jumlahBunga);

        $terbilang = Html_number::terbilang($total_bayar) . 'rupiah';
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);
        $status_ttd = Utility::checkStatusTTD($status_ttd);

        if (!$status_ttd) {
            //TODO: generate QR Code (TTD)
            $img = Utility::createQrTTD($file_url);

            //TODO: generate QR Code (QRIS)
            $imgQRIS = '';
            if ($text_qris) {
                $imgQRIS = Utility::createQrQris($text_qris);
            }

            //TODO: Check status TTD
            if ($data->status_ttd == 2) {
                $file = 'pages.tandaTangan.reportTTEskrd';
            } elseif ($data->status_ttd == 4) {
                $file = 'pages.tandaTangan.reportTTEstrd';
            }

            $pdf = app('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->setPaper('legal', 'portrait');
            $pdf->loadView($file, compact(
                'data',
                'terbilang',
                'jumlahBunga',
                'total_bayar',
                'kenaikan',
                'tgl_jatuh_tempo',
                'img',
                'imgQRIS'
            ));

            // get content PDF
            $content = $pdf->download()->getOriginalContent();

            // save PDF to sftp storage
            Storage::disk('sftp')->put($path_sftp . $fileName, $content);

            // Save PDF to local storage
            Storage::put($path_local . $fileName, $content);
        }

        return view($this->view . 'show', compact(
            'id',
            'route',
            'title',
            'data',
            'fileName',
            'path_sftp',
            'dateNow',
            'kenaikan',
            'jumlahBunga',
            'nik',
            'nip',
            'status_ttd',
            'jatuh_tempo'
        ));
    }

    public function tteBackup(Request $request)
    {
        $id = $request->id;

        $dataSKRD = TransaksiOPD::find($id);

        if ($dataSKRD->status_ttd == 2) {
            $dataSKRD->update([
                'status_ttd' => 1,
            ]);
        } else {
            $dataSKRD->update([
                'status_ttd' => 3,
            ]);
        }

        return redirect()
            ->route($this->route . 'show', \Crypt::encrypt($id))
            ->withSuccess('Berhasil melakukan tandatangan digital.');
    }

    public function tandaTangan(Request $request)
    {
        $id  = $request->id;
        $tte = $request->tte;
        $nik = $request->nik;
        $nip = $request->nip;
        $passphrase = $request->passphrase;

        $data =  TransaksiOPD::find($id);

        $fileName   = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_local = 'app/public/';
        $path_sftp  = 'file_ttd_skrd/';

        $pdf = storage_path($path_local . 'file_skrd/' . $fileName);
        $qrimage_path = storage_path($path_local . 'transparan.png');

        $file = fopen($pdf, 'r');
        $qrimage = fopen($qrimage_path, 'r');

        //* BSRE
        if ($tte == 'bsre') {
            $dataBSRE = [
                'nik'        => $nik,
                'passphrase' => $passphrase,
                'tampilan'   => 'visible',
                'xAxis'      => 177,
                'yAxis'      => 840,
                'width'      => 1,
                'height'     => 795,
                'page'       => 1,
                'image'      => 'true',
                'reason'     => 'Tanda Tangan Digital Retribusi',
                'location'   => 'Tangerang Selatan'
            ];

            $res = Http::attach('file', $file, 'myfile.pdf')
                ->attach('imageTTD', $qrimage, 'myimg.png')
                ->withBasicAuth('esign', 'qwerty')
                ->post('http://192.168.150.79/' . 'api/sign/pdf', $dataBSRE);

            if ($res->status() == 200) {
                if ($res->body()) {
                    if ($data->status_ttd == 2) {
                        $data->update([
                            'status_ttd' => 1,
                            'history_ttd' => 1
                        ]);
                    } else {
                        $data->update([
                            'status_ttd' => 3,
                            'history_ttd' => 1
                        ]);
                    }

                    file_put_contents($pdf, $res->body(), true);
                    $local_pdf = Storage::disk('local')->get('public/file_skrd/' . $fileName); // get content pdf from local

                    // Move to storage SFTP
                    Storage::disk('sftp')->put($path_sftp . $fileName, $local_pdf);
                    Storage::delete('public/file_skrd/' . $fileName); // delete pdf from local

                    //* Send Email
                    if ($data->email) {
                        $email = $data->email;
                        $mailFrom = config('app.mail_from');
                        $mailName = config('app.mail_name');

                        $dataEmail = array(
                            'nama' => $data->nm_wajib_pajak,
                            'jumlah_bayar' => 'Rp. ' . number_format($data->jumlah_bayar),
                            'tgl_jatuh_tempo' => Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d F Y'),
                            'no_bayar' => $data->no_bayar
                        );

                        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
                        $path_sftp = 'file_ttd_skrd/';
                        $file = Storage::disk('sftp')->get($path_sftp . $fileName);

                        Mail::send('layouts.mail.skrd', $dataEmail, function ($message) use ($email, $mailFrom, $mailName, $fileName, $file) {
                            $message->to($email)->subject('SKRD');
                            $message->attachData($file, $fileName);
                            $message->from($mailFrom, $mailName);
                        });
                    }

                    return redirect()
                        ->route($this->route . 'show', \Crypt::encrypt($id))
                        ->withSuccess('Berhasil melakukan tandatangan digital dengan BSRE.');
                } else {
                    return response()->json(['message' => "Gagal melakukan tandatangan digital BSRE. Silahkan dicoba lagi"], 422);
                }
            } else {
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors("Terjadi kegagalan dalam melakukan tanda tangan. Error Server");
            }
        }

        //* IOTENTIK
        if ($tte == 'iotentik') {
            list($err, $errMsg, $idCert) = $this->iotentik->getListCert($nip);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withSuccess($errMsg);
            }

            list($err, $errMsg, $tokenGodem) = $this->iotentik->getTokenGodem($nip);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withSuccess($errMsg);
            }

            $dataIotentik = [
                'username'   => $nip,
                'passphrase' => $passphrase,
                'token'      => $tokenGodem,
                'urx'  => 177,
                'ury'  => 840,
                'llx'  => 1,
                'lly'  => 795,
                'page' => 1,
                'idkeystore' => $idCert,
                'reason'     => 'Tanda Tangan Digital Retribusi',
                'location'   => 'Tangerang Selatan',
                'updated_at' => ''
            ];

            $res = Http::withToken(config('app.signapi_bearer'))
                ->attach('imageSign', $qrimage, 'myimg.png')
                ->attach('pdf', $file, 'myfile.pdf')
                ->post(config('app.signapi_ipserver') . 'signPDF', $dataIotentik);

            if ($res->successful()) {
                $r = $res->json();
                if ($r['status'] == 200) {
                    // Update status TTD
                    if ($data->status_ttd == 2) {
                        $data->update([
                            'status_ttd' => 1,
                            'history_ttd' => 1
                        ]);
                    } else {
                        $data->update([
                            'status_ttd' => 3,
                            'history_ttd' => 1
                        ]);
                    }

                    // Save to local storage (file already TTE)
                    file_put_contents($pdf, base64_decode($r['data'], true));
                    $local_pdf = Storage::disk('local')->get('public/file_skrd/' . $fileName); // get content pdf from local

                    // Move to storage SFTP
                    Storage::disk('sftp')->put($path_sftp . $fileName, $local_pdf);
                    Storage::delete('public/file_skrd/' . $fileName); // delete pdf from local

                    //* Send Email
                    if ($data->email) {
                        $email = $data->email;
                        $mailFrom = config('app.mail_from');
                        $mailName = config('app.mail_name');

                        $dataEmail = array(
                            'nama' => $data->nm_wajib_pajak,
                            'jumlah_bayar' => 'Rp. ' . number_format($data->jumlah_bayar),
                            'tgl_jatuh_tempo' => Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d F Y'),
                            'no_bayar' => $data->no_bayar
                        );

                        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
                        $path_sftp = 'file_ttd_skrd/';
                        $file = Storage::disk('sftp')->get($path_sftp . $fileName);

                        Mail::send('layouts.mail.skrd', $dataEmail, function ($message) use ($email, $mailFrom, $mailName, $fileName, $file) {
                            $message->to($email)->subject('SKRD');
                            $message->attachData($file, $fileName);
                            $message->from($mailFrom, $mailName);
                        });
                    }

                    return redirect()
                        ->route($this->route . 'show', \Crypt::encrypt($id))
                        ->withSuccess('Berhasil melakukan tandatangan digital dengan IOTENTIK.');
                } elseif ($r['status'] && $r['data']) {
                    return redirect()
                        ->route($this->route . 'show', \Crypt::encrypt($id))
                        ->withErrors('Gagal melakukan tandatangan digital IOTENTIK. Error Code: ' .  $r['status'] . ' Message: ' . $r['data']);
                } else {
                    return redirect()
                        ->route($this->route . 'show', \Crypt::encrypt($id))
                        ->withErrors('Gagal melakukan tandatangan digital IOTENTIK.');
                }
            } else {
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors("Terjadi kegagalan dalam melakukan tanda tangan. Error Server");
            }
        }
    }

    public function restoreTTD($id)
    {
        TransaksiOPD::where('id', $id)->update([
            'status_ttd' => 0
        ]);

        return redirect()
            ->route('tanda-tangan.index')
            ->withSuccess('Data berhasil dikembalikan.');
    }
}
