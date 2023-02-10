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
use DataTables;
use Carbon\Carbon;

use App\Http\Services\BSRE;
use App\Http\Services\WhatsApp;
use App\Http\Services\Iontentik;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
    public function __construct(Iontentik $iotentik, BSRE $bsre, WhatsApp $whatsapp)
    {
        $this->bsre = $bsre;
        $this->iotentik = $iotentik;
        $this->whatsapp = $whatsapp;

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
                $tgl_jatuh_tempo = $p->tgl_strd_akhir ? $p->tgl_strd_akhir : $p->tgl_skrd_akhir;
                return Carbon::createFromFormat('Y-m-d', $tgl_jatuh_tempo)->format('d M Y');
            })
            ->editColumn('jumlah_bayar', function ($p) {
                return 'Rp. ' . number_format($p->jumlah_bayar);
            })
            ->addColumn('status_ttd', function ($p) {
                $status_ttd = Utility::checkStatusTTD($p->status_ttd);

                if ($status_ttd) {
                    return "<span class='badge badge-success'>Sudah TTD</span>";
                } else {
                    return "<span class='badge badge-danger'>Belum TTD</span>";
                }
            })
            ->addIndexColumn()
            ->rawColumns(['file_ttd', 'no_skrd', 'id_opd', 'id_jenis_pendapatan', 'tgl_skrd', 'masa_berlaku', 'status_ttd'])
            ->toJson();
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

        $file    = fopen($pdf, 'r');
        $qrimage = fopen($qrimage_path, 'r');

        //* IOTENTIK
        if ($tte == 'iotentik') {
            //TODO: Get Certificat
            list($err, $errMsg, $idCert) = $this->iotentik->getListCert($nip);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }

            //TODO: Get token godem
            list($err, $errMsg, $tokenGodem) = $this->iotentik->getTokenGodem($nip);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }

            list($err, $errMsg, $fileTTD) = $this->iotentik->iotentikRes($nip, $passphrase, $tokenGodem, $idCert, $qrimage, $file);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }
        }

        //* BSRE
        if ($tte == 'bsre') {
            list($err, $errMsg, $fileTTD) = $this->bsre->bsreRes($nik, $passphrase, $file, $qrimage);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }
        }

        //* Update status_ttd
        $update_ttd = $data->status_ttd == 2 ? 1 : 3;
        $data->update([
            'status_ttd' => $update_ttd,
            'history_ttd' => 1
        ]);

        //* Save to local storage (file already TTE)
        file_put_contents($pdf, $fileTTD);
        $local_pdf = Storage::disk('local')->get('public/file_skrd/' . $fileName); // get content pdf from local

        //* Move to storage SFTP
        Storage::disk('sftp')->put($path_sftp . $fileName, $local_pdf);
        Storage::delete('public/file_skrd/' . $fileName); // delete pdf from local

        //* Send Email
        if ($data->email) {
            $email    = $data->email;
            $mailFrom = config('app.mail_from');
            $mailName = config('app.mail_name');

            $dataEmail = array(
                'nama'     => $data->nm_wajib_pajak,
                'no_bayar' => $data->no_bayar,
                'jumlah_bayar'    => 'Rp. ' . number_format($data->jumlah_bayar),
                'tgl_jatuh_tempo' => Carbon::createFromFormat('Y-m-d', $data->tgl_skrd_akhir)->format('d F Y')
            );

            $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
            $path_sftp = 'file_ttd_skrd/';
            $file      = Storage::disk('sftp')->get($path_sftp . $fileName);

            Mail::send('layouts.mail.skrd', $dataEmail, function ($message) use ($email, $mailFrom, $mailName, $fileName, $file) {
                $message->to($email)->subject('SKRD');
                $message->attachData($file, $fileName);
                $message->from($mailFrom, $mailName);
            });
        }

        //* Send WA
        if ($data->no_telp) {
            $tgl_jatuh_tempo = Utility::tglJatuhTempo($data->tgl_strd_akhir, $data->tgl_skrd_akhir);

            $this->whatsapp->sendSKRD($data, $tgl_jatuh_tempo);
        }

        return redirect()
            ->route($this->route . 'show', \Crypt::encrypt($id))
            ->withSuccess('Berhasil melakukan tandatangan digital dengan ' . $tte);
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
