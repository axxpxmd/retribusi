<?php

namespace App\Http\Controllers;

use Mail;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

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

    public function skrd($id)
    {
        try {
            $data = TransaksiOPD::find($id);
            $tgl_jatuh_tempo = Utility::tglJatuhTempo($data->tgl_strd_akhir, $data->tgl_skrd_akhir);

            if ($data->email) {
                $this->email->sendSKRD($data, $tgl_jatuh_tempo);
            }
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

    public function sendEmail($id)
    {
        try {
            $data = TransaksiOPD::find($id);

            if ($data->email) {
                $this->email->sendSTS($data);
            }
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
}
