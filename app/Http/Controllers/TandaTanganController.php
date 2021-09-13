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
        $no_skrd = $request->no_skrd;

        $checkOPD = Auth::user()->pengguna->opd_id;
        if ($checkOPD == 0 || $checkOPD == 99999) {
            $opd_id = $request->opd_id;
        } else {
            $opd_id = $checkOPD;
        }

        $data = TransaksiOPD::queryTandaTangan($from, $to, $opd_id, $no_skrd);

        return DataTables::of($data)
            ->addColumn('action', function ($p) {
                if ($p->status_ttd == 1) {
                    return "<a href='" . route($this->route . 'report', Crypt::encrypt($p->id)) . "' target='blank' title='Print Data' class='text-success'><i class='icon icon-printer2 mr-1'></i></a>";
                } else {
                    return "";
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
                        ->withErrors('Tidak memiliki sertifikat terdaftar.');
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
                ->withErrors('NIP kosong, Silahkan isi pada menu OPD.');
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
                    ->withErrors('Gagal membuat token godam.');
            } else {
                $msg = '';
                if (isset($res['error']))
                    $msg .= $res['error']['code'] . ' - ' . $res['error']['message'];
                return redirect()
                    ->route($this->route . 'show', \Crypt::encrypt($id))
                    ->withErrors('Terjadi kegagalan dalam memuat token godam. Error Code " . $res->getStatusCode() . ". \n " . $msg . ".\n Silahkan laporkan masalah ini pada administrator.');
            }
        } else {
            return redirect()
                ->route($this->route . 'show', \Crypt::encrypt($id))
                ->withErrors('NIP kosong, Silahkan isi pada menu OPD.');
        }
    }

    public function show($id)
    {
        $route = $this->route;
        $title = $this->title;

        $id   = \Crypt::decrypt($id);
        $data = TransaksiOPD::find($id);
        $nip_ttd = $data->nip_ttd;

        // Save PDF to Storage SFTP
        $terbilang = Html_number::terbilang($data->total_bayar) . 'rupiah';

        // generate QR Code
        $currentURL = url()->current();
        $b   = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->merge(public_path('images/logo-png.png'), 0.2, true)->size(900)->errorCorrection('H')->margin(0)->generate($currentURL));
        $img = '<img width="60" height="61" src="data:image/png;base64, ' . $b . '" alt="qr code" />';

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->loadView($this->view . 'report', compact(
            'data',
            'terbilang',
            'img'
        ));

        // get content PDF
        $fileName =  $data->nm_wajib_pajak . ' - ' . $data->no_skrd . ".pdf";
        $content = $pdf->download()->getOriginalContent();

        // save PDF to sftp storage
        $path_sftp = 'file_ttd_skrd/';
        Storage::disk('sftp')->put($path_sftp . $fileName, $content);

        // Token Godem
        $token_godem = $this->getTokenGodam($id, $nip_ttd);

        // Sertifikat
        $id_cert = $this->getListCert($id, $nip_ttd);

        return view($this->view . 'show', compact(
            'id',
            'route',
            'title',
            'data',
            'token_godem',
            'id_cert'
        ));
    }

    public function tte(Request $request)
    {
        $data = TransaksiOPD::find($request->id);

        $fileName  =  $data->nm_wajib_pajak . ' - ' . $data->no_skrd . ".pdf";
        $path_sftp = 'file_ttd_skrd/';

        // TTE
        $pdf          = config('app.sftp_src') . $path_sftp . $fileName;
        $qrimage_path = asset('images/transparan.png');

        $data = [
            'username'   => $request->nip_ttd,
            'passphrase' => $request->passphrase,
            'token'      => $request->token_godem,
            'urx'  => 321,
            'ury'  => 935,
            'llx'  => 1,
            'lly'  => 855,
            'page' => 1,
            'idkeystore' => $request->id_cert,
            'reason'     => 'Tanda Tangan Digital Retribusi',
            'location'   => 'Tangerang Selatan'
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
                    file_put_contents($pdf, base64_decode($r['data'], true));
                    return response()->json(['message' => "Berhasil melakukan tandatangan digital."]);
                }
                return response()->json(['message' => "Gagal melakukan tandatangan digital. Aplikasi tidak mendapatkan balikan data .pdf."], 422);
            }
            $msg = 'Gagal melakukan tandatangan digital';
            if (isset($r['data'])) $msg .= ', ' . $r['data'] . '.';
            return response()->json(['message' => $msg], 422);
        }
        return response()->json(['message' => "Terjadi kegagalan dalam memuat tandatangan digital. Error Code " . $res->getStatusCode() . ". Silahkan laporkan masalah ini pada administrator"], 422);
    }
}
