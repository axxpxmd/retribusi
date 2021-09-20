<?php

namespace App\Http\Controllers;

use Auth;
use DataTables;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;
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
        if ($checkOPD == 0 || $checkOPD == 99999) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::queryTandaTangan($from, $to, $opd_id, $no_skrd, $status_ttd);

        return DataTables::of($data)
            ->addColumn('file_ttd', function ($p) {
                $path_sftp = 'file_ttd_skrd/';
                $fileName  =  $p->nm_wajib_pajak . ' - ' . $p->no_skrd . ".pdf";

                if ($p->status_ttd == 0 || $p->status_ttd == 2) {
                    return '-';
                } else {
                    return "<a href='" . config('app.sftp_src') . $path_sftp . $fileName . "' target='_blank' class='cyan-text'><i class='icon-document-file-pdf2'></i></a>";
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
                if ($p->status_ttd == 0 || $p->status_ttd == 2) {
                    return 'Belum TTD';
                } else {
                    return "Sudah TTD";
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

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);
        $nip_ttd = $data->nip_ttd;

        $token_godem = '';
        $id_cert     = '';
        $fileName    =  $data->nm_wajib_pajak . ' - ' . $data->no_skrd . ".pdf";
        $path_sftp   = 'file_ttd_skrd/';
        $path_local  = 'public/file_skrd/';

        /* Check Status TTD
         * 0 = Belum
         * 1 = Sudah 
         * 2 = Proses
         */
        if ($data->status_ttd == 0 || $data->status_ttd == 2) {
            $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

            // generate QR Code
            $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;
            $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->merge(public_path('images/logo-png.png'), 0.2, true)->size(900)->errorCorrection('H')->margin(0)->generate($file_url));
            $img = '<img width="60" height="61" src="data:image/png;base64, ' . $b . '" alt="qr code" />';

            // generate PDF
            $pdf = app('dompdf.wrapper');
            $pdf->getDomPDF()->set_option("enable_php", true);
            $pdf->loadView($this->view . 'report', compact(
                'data',
                'terbilang',
                'img'
            ));

            // get content PDF
            $content  = $pdf->download()->getOriginalContent();

            // save PDF to sftp storage
            Storage::disk('sftp')->put($path_sftp . $fileName, $content);

            // Save PDF to local storage
            Storage::put($path_local . $fileName, $content);

            // Token Godem
            $token_godem = $this->getTokenGodam($id, $nip_ttd);

            // Sertifikat 
            $id_cert = $this->getListCert($id, $nip_ttd);
        }

        return view($this->view . 'show', compact(
            'id',
            'route',
            'title',
            'data',
            'token_godem',
            'id_cert',
            'fileName',
            'path_sftp'
        ));
    }

    public function tte(Request $request)
    {
        $id = $request->id;
        $dataSKRD = TransaksiOPD::find($id);

        $fileName   =  $dataSKRD->nm_wajib_pajak . ' - ' . $dataSKRD->no_skrd . ".pdf";
        $path_local = 'app/public/';
        $path_sftp  = 'file_ttd_skrd/';

        /**
         * Process TTE
         */
        $pdf = storage_path($path_local . 'file_skrd/' . $fileName);
        $qrimage_path = storage_path($path_local . 'transparan.png');

        // Data / Payload
        $data = [
            'username'   => $request->nip_ttd,
            'passphrase' => $request->passphrase,
            'token'      => $request->token_godem,
            'urx'  => 177,
            'ury'  => 840,
            'llx'  => 1,
            'lly'  => 795,
            'page' => 1,
            'idkeystore' => $request->id_cert,
            'reason'     => 'Tanda Tangan DigitallmnRetribusi',
            'location'   => 'Tangerang Selatan',
            'updated_at' => ''
        ];

        $file = fopen($pdf, 'r');
        $qrimage = fopen($qrimage_path, 'r');

        $res = Http::withToken(config('app.signapi_bearer'))
            ->attach('imageSign', $qrimage, 'myimg.png')
            ->attach('pdf', $file, 'myfile.pdf')
            ->post(config('app.signapi_ipserver') . 'signPDF', $data);

        if ($res->successful()) {
            $r = $res->json();
            if ($r['status'] == 200) {
                if ($res['data']) {
                    // Update status TTD
                    $dataSKRD->update([
                        'status_ttd' => 1,
                    ]);

                    // Save to local storage (file already TTE)
                    file_put_contents($pdf, base64_decode($r['data'], true));
                    $local_pdf = Storage::disk('local')->get('public/file_skrd/' . $fileName); // get content pdf from local

                    // Move to storage SFTP
                    Storage::disk('sftp')->put($path_sftp . $fileName, $local_pdf);
                    Storage::delete('public/file_skrd/' . $fileName); // delete pdf from local

                    return redirect()
                        ->route($this->route . 'show', \Crypt::encrypt($id))
                        ->withSuccess('Berhasil melakukan tandatangan digital.');
                }
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors('Gagal melakukan tandatangan digital. Aplikasi tidak mendapatkan balikan data .pdf.');
            }
            if (isset($r))
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors('Gagal melakukan tandatangan digital, Silahkan refresh halaman ini. Error Code: ' .  $r['status'] . ' Message: ' . $r['message']);
        }
        return redirect()
            ->route($this->route . 'show', \Crypt::encrypt($id))
            ->withErrors("Terjadi kegagalan dalam memuat tandatangan digital. Error Code " . $res->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator");
    }

    public function printData(Request $request, $id)
    {
        $id = \Crypt::decrypt($id);

        $data = TransaksiOPD::find($id);
        $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView('pages.skrd.report', compact(
            'data',
            'terbilang'
        ));

        return $pdf->stream($data->nm_wajib_pajak . '-' . $data->no_skrd . ".pdf");
    }
}
