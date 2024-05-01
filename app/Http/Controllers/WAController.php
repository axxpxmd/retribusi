<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Services\WhatsApp;
use App\Http\Controllers\Controller;

// Models
use App\Models\Utility;
use App\Models\TransaksiOPD;

class WAController extends Controller
{
    public function __construct(WhatsApp $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    public function sendSKRD(Request $request, $id)
    {
        $no_telp = $request->no_telp;

        $validator = \Validator::make($request->all(), [
            "no_telp" => "required|min:10"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => 'No Telp wajib diisi!'
            ]);
        } // end validator fails

        try {
            $data = TransaksiOPD::find($id);

            $tgl_jatuh_tempo = Utility::tglJatuhTempo($data->tgl_strd_akhir, $data->tgl_skrd_akhir);

            // Send WA
            $this->whatsapp->sendSKRD($data, $tgl_jatuh_tempo, $no_telp);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => "Terjadi kesalahan saat mengirim Whatsapp"
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => "Selamat! WA berhasil terkirim kepada ' . $data->nm_wajib_pajak"
        ], 200);
    }

    public function sendSTS(Request $request, $id)
    {
        $no_telp = $request->no_telp;

        $validator = \Validator::make($request->all(), [
            "no_telp" => "required|min:10"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => 'No Telp wajib diisi!'
            ]);
        } // end validator fails

        try {
            $data = TransaksiOPD::find($id);

            $tgl_bayar = $data->tgl_bayar;
            $ntb = $data->ntb;
            $chanel_bayar = $data->chanel_bayar;
            $total_bayar_bjb = $data->total_bayar_bjb;

            // Send WA
            $this->whatsapp->sendSTS($tgl_bayar, $ntb, $chanel_bayar, $total_bayar_bjb, $data, $no_telp);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => "Terjadi kesalahan saat mengirim Whatsapp" . $th
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => "Selamat! WA berhasil terkirim kepada ' . $data->nm_wajib_pajak"
        ], 200);
    }
}
