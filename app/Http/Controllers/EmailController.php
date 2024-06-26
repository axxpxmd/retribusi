<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Services\Email;
use App\Http\Controllers\Controller;

// Models
use App\Models\Utility;
use App\Models\TransaksiOPD;

class EmailController extends Controller
{
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    public function sendSKRD(Request $request, $id)
    {
        $email = $request->email;

        $validator = \Validator::make($request->all(), [
            "email" => "required|email"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => 'Email wajib diisi atau format email salah!'
            ]);
        } // end validator fails

        try {
            $data = TransaksiOPD::find($id);
            $tgl_jatuh_tempo = Utility::tglJatuhTempo($data->tgl_strd_akhir, $data->tgl_skrd_akhir);

            // Send Email
            $this->email->sendSKRD($data, $tgl_jatuh_tempo, $email);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => "Terjadi kesalahan saat mengirim email"
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => "Selamat! Email berhasil terkirim kepada ' . $data->nm_wajib_pajak"
        ], 200);
    }

    public function sendSTS(Request $request, $id)
    {
        $email = $request->email;

        $validator = \Validator::make($request->all(), [
            "email" => "required|email"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 500,
                'message' => 'Email wajib diisi atau format email salah!'
            ]);
        } // end validator fails

        try {
            $data = TransaksiOPD::find($id);

            // Send Email
            $this->email->sendSTS($data, $email);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => 500,
                'message' => "Terjadi kesalahan saat mengirim email" . $id
            ]);
        }

        return response()->json([
            'status' => 200,
            'message' => "Selamat! Email berhasil terkirim kepada ' . $data->nm_wajib_pajak"
        ], 200);
    }
}
