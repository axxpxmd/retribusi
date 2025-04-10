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

use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

use App\Http\Services\BSRE;
use App\Http\Services\Email;
use App\Http\Services\AUROGRAF;
use App\Http\Services\WhatsApp;
use App\Http\Services\Iontentik;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    protected $email;
    protected $bsre;
    protected $iotentik;
    protected $whatsapp;
    protected $aurograf;

    // Check Permission
    public function __construct(Iontentik $iotentik, BSRE $bsre, WhatsApp $whatsapp, AUROGRAF $aurograf, Email $email)
    {
        $this->email = $email;
        $this->bsre  = $bsre;
        $this->iotentik = $iotentik;
        $this->whatsapp = $whatsapp;
        $this->aurograf = $aurograf;

        $this->middleware(['permission:Tanda Tangan']);
    }

    public function index(Request $request)
    {
        $route = $this->route;
        $title = $this->title;

        $opd_id   = Auth::user()->pengguna->opd_id;
        $opdArray = OPDJenisPendapatan::select('id_opd')->get()->toArray();
        $opds     = OPD::getAll($opdArray, $opd_id);
        $nip      = Auth::user()->pengguna->nip;

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
            return $this->dataTable($belum_ttd, $from, $to, $opd_id, $no_skrd, $status_ttd, $nip);
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

    public function dataTable($belum_ttd, $from, $to, $opd_id, $no_skrd, $status_ttd, $nip)
    {
        $data = TransaksiOPD::queryTandaTangan($belum_ttd, $from, $to, $opd_id, $no_skrd, $status_ttd, $nip);

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

        $id   = Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');
        $role_id = Auth::user()->pengguna->modelHasRole->role_id;

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
        $tte_backup     = config('app.tte_backup');

        //* TTD
        $iotentik_status = config('app.iotentik_status');
        $aurograf_status = config('app.aurograf_status');
        $bsre_status = config('app.bsre_status');

        $path_sftp   = 'file_ttd_skrd/';
        $path_local  = 'public/file_skrd/';
        $fileName    = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $file_url    = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($jatuh_tempo) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $total_bayar);
        }

        //TODO Total Bayar + Bunga
        $total_bayar = Utility::createDenda($status_bayar, $total_bayar, $denda, $jumlahBunga);

        $terbilang = Html_number::terbilang($total_bayar) . 'rupiah';
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);
        $status_ttd = Utility::checkStatusTTD($status_ttd);

        //* Get Sertifikat Aurograf
        $aurograf_status = config('app.aurograf_status');
        $aurografCerts = [];
        if ($aurograf_status == 'ON') {
            list($err, $errMsg, $aurografCerts) = $this->aurograf->getListCert($nik);
        }

        if (!$status_ttd) {
            //TODO: generate QR Code (TTD)
            $img = Utility::createQrTTD($file_url);

            // Generate QR Code (QRIS) if text_qris is available
            $imgQRIS = $text_qris ? Utility::createQrQris($text_qris) : '';

            // Determine the file based on status_ttd
            $file = $data->status_ttd == 2 ? 'pages.tandaTangan.reportTTEskrd' : ($data->status_ttd == 4 ? 'pages.tandaTangan.reportTTEstrd' : '');

            if ($file) {
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

                // Get content PDF
                $content = $pdf->download()->getOriginalContent();

                // Save PDF to sftp storage and local storage concurrently
                Storage::put($path_local . $fileName, $content);
            }
        }

        return view($this->view . 'show', compact(
            'role_id',
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
            'jatuh_tempo',
            'tte_backup',
            'aurografCerts',
            'iotentik_status',
            'aurograf_status',
            'bsre_status'
        ));
    }

    public function tteBackup(Request $request)
    {
        $id = $request->id;

        $data = TransaksiOPD::find($id);

        $no_telp = $data->no_telp;
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($data->tgl_strd_akhir, $data->tgl_skrd_akhir);

        //* Update status_ttd
        $update_ttd = $data->status_ttd == 2 ? 1 : 3;
        $data->update([
            'status_ttd'  => $update_ttd,
            'history_ttd' => 1
        ]);

        //* Send Email
        if ($data->email) {
            $this->email->sendSKRD($data, $tgl_jatuh_tempo);
        }

        //* Send WA
        if ($no_telp) {
            $this->whatsapp->sendSKRD($data, $tgl_jatuh_tempo);
        }

        return redirect()
            ->route($this->route . 'show', Crypt::encrypt($id))
            ->withSuccess('Berhasil melakukan tandatangan digital.');
    }

    public function tandaTangan(Request $request)
    {
        $id  = $request->id;
        $tte = $request->tte;
        $nik = $request->nik;

        $nip = $request->nip;
        $passphrase = $request->passphrase;
        $aurograf_cert_id = $request->aurograf_cert_id;

        $data =  TransaksiOPD::find($id);

        $fileName   = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $path_local = 'app/public/';
        $path_sftp  = 'file_ttd_skrd/';
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($data->tgl_strd_akhir, $data->tgl_skrd_akhir);

        $pdf = storage_path($path_local . 'file_skrd/' . $fileName);
        $qrimage_path = storage_path($path_local . 'transparan.png');

        $file    = fopen($pdf, 'r');
        $qrimage = fopen($qrimage_path, 'r');

        if (!$tte) {
            return redirect()
                ->route($this->route . 'show', Crypt::encrypt($id))
                ->withErrors('Silahkan pilih jenis TTE');
        }

        //* IOTENTIK
        if ($tte == 'iotentik') {
            //TODO: Get Certificat
            list($err, $errMsg, $idCert) = $this->iotentik->getListCert($nip);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }

            //TODO: Get token godem
            list($err, $errMsg, $tokenGodem) = $this->iotentik->getTokenGodem($nip);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }

            list($err, $errMsg, $fileTTD) = $this->iotentik->iotentikRes($nip, $passphrase, $tokenGodem, $idCert, $qrimage, $file);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }
        }

        //* BSRE
        if ($tte == 'bsre') {
            list($err, $errMsg, $fileTTD) = $this->bsre->bsreRes($nik, $passphrase, $file, $qrimage);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }
        }

        //* AUROGRAF
        if ($tte == 'aurograf') {
            list($err, $errMsg, $fileTTD) = $this->aurograf->sign($aurograf_cert_id, $nik, $passphrase, $file, $qrimage);
            if ($err) {
                return redirect()
                    ->route($this->route . 'show', Crypt::encrypt($id))
                    ->withErrors($errMsg);
            }
        }

        //* Update status_ttd
        $update_ttd = $data->status_ttd == 2 ? 1 : 3;
        $data->update([
            'status_ttd' => $update_ttd,
            'history_ttd' => 1
        ]);

        //* Save to local estorage (file already TTE)
        file_put_contents($pdf, $fileTTD);
        $local_pdf = Storage::disk('local')->get('public/file_skrd/' . $fileName); // get content pdf from local

        //* Move to storage SFTP
        Storage::disk('sftp')->put($path_sftp . $fileName, $local_pdf);
        Storage::delete('public/file_skrd/' . $fileName); // delete pdf from local

        //* Send Email
        if ($data->email) {
            $this->email->sendSKRD($data, $tgl_jatuh_tempo);
        }

        //* Send WA
        if ($data->no_telp) {
            $this->whatsapp->sendSKRD($data, $tgl_jatuh_tempo);
        }

        return redirect()
            ->route($this->route . 'show', Crypt::encrypt($id))
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

    public function batalkanTTD($id)
    {
        $status_ttd_sebelum = TransaksiOPD::where('id', $id)->first();

        TransaksiOPD::where('id', $id)->update([
            'status_ttd' => $status_ttd_sebelum->status_ttd == 1 ? 2 : 4
        ]);

        return redirect()
            ->back()
            ->withSuccess('Tanda tangan berhasil dibatalkan');
    }
}
