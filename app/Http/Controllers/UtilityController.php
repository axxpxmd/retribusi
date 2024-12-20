<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Libraries\VABJBRes;
use App\Libraries\QRISBJBRes;
use App\Libraries\GenerateNumber;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Libraries\Html\Html_number;
use App\Http\Controllers\Controller;

// Models
use App\Models\Utility;
use App\Models\TransaksiOPD;
use App\Models\RincianJenisPendapatan;

class UtilityController extends Controller
{
    public function __construct(GenerateNumber $generateNumber, VABJBRes $vabjbres, QRISBJBRes $qrisbjbres)
    {
        $this->vabjbres   = $vabjbres;
        $this->qrisbjbres = $qrisbjbres;
        $this->generateNumber = $generateNumber;

        $this->middleware(['permission:SKRD']);
    }

    public function printDataTTD(Request $request, $id)
    {
        $id      = base64_decode($id);
        $data    = TransaksiOPD::find($id);
        $dateNow = Carbon::now()->format('Y-m-d');

        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $jumlah_bayar   = $data->jumlah_bayar;
        $status_bayar   = $data->status_bayar;
        $denda          = $data->denda;
        $text_qris      = $data->text_qris;
        $nm_wajib_pajak = $data->nm_wajib_pajak;
        $no_skrd        = $data->no_skrd;
        $tgl_strd_akhir = $data->tgl_strd_akhir;
        $tgl_skrd_akhir = $data->tgl_skrd_akhir;
        $tgl_skrd_awal  = $data->tgl_skrd_awal;
        $tgl_bayar      = $data->tgl_bayar;
        $send_sts       = $request->send_sts;

        $fileName = str_replace(' ', '', $nm_wajib_pajak) . '-' . $no_skrd . ".pdf";
        $file_url = config('app.sftp_src') . 'file_ttd_skrd/' . $fileName;

        $jatuh_tempo = Utility::isJatuhTempo($tgl_skrd_akhir, $dateNow);

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar);
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
            }
        }

        //TODO: Total Bayar + Bunga
        $total_bayar = Utility::createDenda($status_bayar, $jumlah_bayar, $denda, $jumlahBunga);

        $terbilang = Html_number::terbilang($total_bayar) . 'rupiah';
        $tgl_jatuh_tempo = Utility::tglJatuhTempo($tgl_strd_akhir, $tgl_skrd_akhir);

        //TODO: generate QR Code (QRIS)
        $imgQRIS = '';
        if ($text_qris) {
            $imgQRIS = Utility::createQrQris($text_qris);
        }

        //TODO: generate QR Code (TTD)
        $img = Utility::createQrTTD($file_url);

        $pdf = app('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('legal', 'portrait');

        $pdf->loadView('pages.sts.reportTTE', compact(
            'data',
            'terbilang',
            'jumlahBunga',
            'total_bayar',
            'kenaikan',
            'tgl_jatuh_tempo',
            'img',
            'imgQRIS',
            'send_sts'
        ));

        return $pdf->stream($data->nm_wajib_pajak . ' - ' . $data->no_skrd . ".pdf");
    }

    public function getDataSKRD($id)
    {
        $id = base64_decode($id);

        $data = TransaksiOPD::find($id);

        $path_sftp = 'file_ttd_skrd/';
        $fileName  = str_replace(' ', '', $data->nm_wajib_pajak) . '-' . $data->no_skrd . ".pdf";
        $timeNow   = Carbon::now()->format('Y-m-d H:i:s');

        //TODO: Update Jumlah Cetak
        $jumlah_cetak = $data->jumlah_cetak + 1;
        $data->update([
            'jumlah_cetak'    => $jumlah_cetak,
            'tgl_cetak_trkhr' => $timeNow
        ]);

        return storage::disk('sftp')->download($path_sftp . $fileName);
    }

    public function checkDenda($no_bayar)
    {
        $data = TransaksiOPD::where('no_bayar', $no_bayar)->first();

        $tgl_skrd_akhir  = $data->tgl_skrd_akhir;
        $status_bayar    = $data->status_bayar;
        $tgl_bayar       = $data->tgl_bayar;
        $jumlah_bayar    = $data->jumlah_bayar;
        $jatuh_tempo     = Utility::isJatuhTempo($tgl_skrd_akhir);
        $total_bayar_bjb = $data->total_bayar_bjb;

        //TODO: Get bunga
        $kenaikan    = 0;
        $jumlahBunga = 0;
        if ($status_bayar == 1) {
            if (Carbon::parse($tgl_bayar)->format('Y-m-d') > $tgl_skrd_akhir) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar);
            }
        } else {
            if ($jatuh_tempo) {
                list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar);
            }
        }

        //* check percent
        if ($jumlah_bayar + $jumlahBunga != $total_bayar_bjb) {
            if ($status_bayar == 1) {
                if (Carbon::parse($tgl_bayar)->format('Y-m-d') > $tgl_skrd_akhir) {
                    list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $tgl_bayar, $percent = 2);
                }
            } else {
                if ($jatuh_tempo) {
                    list($jumlahBunga, $kenaikan) = Utility::createBunga($tgl_skrd_akhir, $jumlah_bayar, $percent = 2);
                }
            }
        }

        //TODO: Generate New Jatuh Tempo
        $tgl_jatuh_tempo_baru = Utility::generateNewJatuhTempo($tgl_skrd_akhir, $tgl_bayar);

        //TODO: Get total ketelambatan
        list($dayDiff, $monthDiff) = Utility::getDiffDate($tgl_skrd_akhir, $tgl_jatuh_tempo_baru);

        $result = [
            'hari' => $dayDiff,
            'tgl_skrd_akhir' => $tgl_skrd_akhir,
            'tgl_jatuh_tempo_baru' => $tgl_jatuh_tempo_baru,
            'tgl_bayar' => $tgl_bayar,
            'kenaikan' => $kenaikan,
            'denda' => $jumlahBunga,
            'jumlah_bayar' => (int) $jumlah_bayar,
            'total_bayar_bjb' => (int) $total_bayar_bjb
        ];

        dd($result);
    }

    public function addVA()
    {
        $datas = TransaksiOPD::where('total_bayar', '!=', 0)
            ->whereNull('nomor_va_bjb')
            ->where('status_ttd', 0)
            ->whereYear('tgl_skrd_awal', 2024)
            ->where('created_by', 'PERKIM API | API Retribusi')
            ->get();

        foreach ($datas as $data) {
            $rincian = RincianJenisPendapatan::where('id', $data->id_rincian_jenis_pendapatan)->first();

            $amount  = $data->total_bayar;
            $expiredDate  = $data->tgl_skrd_akhir . ' 23:59:59';
            $customerName = $data->nm_wajib_pajak;
            $clientRefnum = $data->no_bayar;
            $no_bayar     = $data->no_bayar;
            $productCode  = $rincian->kd_jenis;
            $no_hp        = $rincian->no_hp;

            if ($data->total_bayar != 0) {
                // 1. VA
                //TODO: Get Token BJB
                list($err, $errMsg, $tokenBJB) = $this->vabjbres->getTokenBJBres();
                if ($err) {
                    return dd('gagal token VA', $errMsg);
                }

                //TODO: Create VA BJB
                list($err, $errMsg, $VABJB) = $this->vabjbres->createVABJBres($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode, 1, $no_bayar);
                if ($err) {
                    return dd('gagal create VA', $errMsg);
                }

                // 2. QRIS
                //TODO: Get Token QRIS
                list($err, $errMsg, $tokenQRISBJB) = $this->qrisbjbres->getTokenQrisres();
                if ($err) {
                    return dd('gagal create QRIS');
                }

                // TODO: Create QRIS
                list($err, $errMsg, $invoiceId, $textQRIS) = $this->qrisbjbres->createQRISres($tokenQRISBJB, $amount, $no_hp, 2, $clientRefnum);
                if ($err) {
                    return dd('gagal create QRIS');
                }
            }

            $data->update([
                'nomor_va_bjb' => $VABJB,
                'text_qris' => $textQRIS,
                'invoice_id' => $invoiceId
            ]);
        }

        return dd('berhasil');
    }
}
