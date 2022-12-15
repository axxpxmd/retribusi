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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

// Models
use App\Models\OPD;
use App\Models\TransaksiOPD;
use App\Models\OPDJenisPendapatan;

class TandaTanganController extends Controller
{
    protected $route = 'tanda-tangan.';
    protected $title = 'Tanda Tangan';
    protected $view  = 'pages.tandaTangan.';

    // Check Permission
    public function __construct()
    {
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
                $path_sftp = 'file_ttd_skrd/';
                $fileName  = str_replace(' ', '', $p->nm_wajib_pajak) . '-' . $p->no_skrd . ".pdf";

                if ($p->status_ttd == 3 || $p->status_ttd == 1) {
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
                    return 'Sudah TTD';
                } else {
                    return "Belum TTD";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['file_ttd', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_ttd'])
            ->toJson();
    }

    public function getListCert($id, $nip_ttd)
    {
        if ($nip_ttd != null) {
            $res = Http::withToken(config('app.signapi_bearer'))->post(config('app.signapi_ipserver') . 'listCert', ['username' => $nip_ttd]);

            // Check
            if ($res->successful()) {
                if ($res['code'] == 200) {
                    $arr = json_decode($res, true);
                    $idCert = end($arr);
                    $idCert = end($idCert);
                    return $idCert['id'];
                } else {
                    return redirect()
                        ->route($this->route . 'show', \Crypt::encrypt($id))
                        ->withErrors('TTE Gagal, Tidak memiliki sertifikat terdaftar.');
                }
            } else {
                $msg = '';
                if (isset($res['error']))
                    $msg .= $res['error']['code'] . ' - ' . $res['error']['message'];
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors("Terjadi kegagalan dalam memuat sertifikat digital. Error Code " . $res->getStatusCode() . ". \n " . $msg . ".\n Silahkan laporkan masalah ini pada administrator.");
            }
        } else {
            return redirect()
                ->route($this->route . 'show', \Crypt::encrypt($id))
                ->withErrors('TTE Gagal. NIP kosong, Silahkan edit data SKRD.');
        }
    }

    public function getTokenGodam($id, $nip_ttd)
    {
        if ($nip_ttd != null) {
            $res = Http::withToken(config('app.signapi_bearer'))
                ->post(config('app.signapi_ipserver') . 'getToken', ['username' => $nip_ttd]);

            // Check
            if ($res->successful()) {
                if ($res['code'] == 200) {
                    $arr = json_decode($res, true);
                    $token_story = substr($arr['message'], 0, 6);
                    $this->my_token = $token_story;
                    return $token_story;
                }
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors('TTE Gagal, Gagal membuat token godam. refresh halaman atau silahkan hubungi administrator');
            } else {
                $msg = '';
                if (isset($res['error']))
                    $msg .= $res['error']['code'] . ' - ' . $res['error']['message'];
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors("Terjadi kegagalan dalam memuat Token Godem. Error Code " . $res->getStatusCode() . ". \n " . $msg . ".\n Silahkan laporkan masalah ini pada administrator.");
            }
        } else {
            return redirect()
                ->route($this->route . 'show', \Crypt::encrypt($id))
                ->withErrors('TTE Gagal. NIP kosong, Silahkan edit data SKRD.');
        }
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
        $nip = $data->nip_ttd;
        $dateNow = Carbon::now()->format('Y-m-d');

        // Get NIK
        $nik = Auth::user()->pengguna->nik;

        $token_godem = '';
        $id_cert     = '';
        $fileName    = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_sftp   = 'file_ttd_skrd/';
        $path_local  = 'public/file_skrd/';

        /* Check Status TTD
         * 0 = Belum
         * 1 = Sudah 
         * 2 = Proses
         */

        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $total_bayar    = $data->jumlah_bayar;
        $daysDiff = $this->getDiffDays($tgl_skrd_akhir);

        //TODO: Check bunga (STRD)
        $jumlahBunga = 0;
        $kenaikan = 0;
        if ($data->status_bayar == 0) {
            if ($daysDiff > 0) {
                $jumlahBunga = 0;
                $kenaikan = 0;
            } else {
                //* Bunga
                list($jumlahBunga, $kenaikan) = PrintController::createBunga($tgl_skrd_akhir, $total_bayar);
            }
        }

        //* Total Bayar + Bunga
        $total_bayar = $data->total_bayar + $jumlahBunga;
        $terbilang   = Html_number::terbilang($total_bayar) . 'rupiah';

        //* Tanggal Jatuh Tempo STRD
        if ($data->tgl_strd_akhir == null) {
            $tgl_jatuh_tempo = $data->tgl_skrd_akhir;
        } else {
            $tgl_jatuh_tempo = $data->tgl_strd_akhir;
        }

        if ($data->status_ttd == 0 || $data->status_ttd == 2 || $data->status_ttd == 4) {

            //TODO: generate QR Code TTD
            $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
            $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
            $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->merge(public_path('images/logo-png.png'), 0.2, true)->size(900)->errorCorrection('H')->margin(0)->generate($file_url));
            $img = '<img width="60" height="61" src="data:image/png;base64, ' . $b . '" alt="qr code" />';

            //TODO: generate QR Code QRIS
            $imgQRIS = '';
            if ($data->text_qris) {
                $fileName = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
                $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
                $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(1000)->errorCorrection('H')->margin(0)->generate($data->text_qris));
                $imgQRIS = '<img width="150" src="data:image/png;base64, ' . $b . '" alt="qr code" />';
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
            $content  = $pdf->download()->getOriginalContent();

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
            'nip'
        ));
    }

    public function tteBackup(Request $request)
    {
        $id = $request->id;

        $dataSKRD = TransaksiOPD::find($id);

        // Update status TTD
        if ($dataSKRD->status_ttd == 2) {
            // SKRD
            $dataSKRD->update([
                'status_ttd' => 1,
            ]);
        } else {
            // STRD
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
                }
                return response()->json(['message' => "Gagal melakukan tandatangan digital BSRE. Silahkan dicoba lagi"], 422);
            }
            return redirect()
                ->route($this->route . 'show', \Crypt::encrypt($id))
                ->withErrors("Terjadi kegagalan dalam memuat tandatangan digital. Error Code " . $res->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
        }

        //* IOTENTIK
        if ($tte == 'iotentik') {
            $id_cert     = $this->getListCert($id, $nip);
            $token_godem = $this->getTokenGodam($id, $nip);

            $dataIotentik = [
                'username'   => $nip,
                'passphrase' => $passphrase,
                'token'      => $token_godem,
                'urx'  => 177,
                'ury'  => 840,
                'llx'  => 1,
                'lly'  => 795,
                'page' => 1,
                'idkeystore' => $id_cert,
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
                    if ($res['data']) {
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
                    }
                    return redirect()
                        ->route($this->route . 'show', \Crypt::encrypt($id))
                        ->withErrors('Gagal melakukan tandatangan digital IOTENTIK. Silahkan dicoba lagi');
                }
                if (isset($r))
                    if ($r['status'] == 201) {
                        return redirect()
                            ->route($this->route . 'show', \Crypt::encrypt($id))
                            ->withErrors('Gagal melakukan tandatangan digital IOTENTIK. Error Code: ' .  $r['status'] . ' Message: Passphrase Salah');
                    } else {
                        return redirect()
                            ->route($this->route . 'show', \Crypt::encrypt($id))
                            ->withErrors('Gagal melakukan tandatangan digital, Silahkan refresh halaman ini. Error Code: ' .  $r['status'] . ' Message: ' . $r['message']);
                    }
            }
            return redirect()
                ->route($this->route . 'show', \Crypt::encrypt($id))
                ->withErrors("Terjadi kegagalan dalam memuat tandatangan digital. Error Code " . $res->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
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
