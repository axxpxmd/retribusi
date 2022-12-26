<?php

namespace App\Libraries;

use App\Http\Services\QRISBJB;

use Illuminate\Support\Facades\Log;

class QRISBJBRes
{
    public static function getTokenQrisres()
    {
        $tokenQRISBJB = '';
        $errMsg = '';

        $resGetTokenQRISBJB = QRISBJB::getToken();
        if ($resGetTokenQRISBJB->successful()) {
            $resJsonQRIS = $resGetTokenQRISBJB->json();
            if ($resJsonQRIS["status"]["code"] != 200) {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat mengambil token QRIS BJB';
            } else {
                $err = false;
                $tokenQRISBJB = $resGetTokenQRISBJB->header('X-AUTH-TOKEN');
            }
        } else {
            $err = true;
            $errMsg = 'Terjadi kegagalan saat mengambil token QRIS BJB. Error Server';
        }

        return [$err, $errMsg, $tokenQRISBJB];
    }

    public static function createQRISres($tokenQRISBJB, $amount, $no_hp, $jenis, $no_bayar)
    {
        $errMsg = '';
        $invoiceId = '';
        $textQRIS = '';

        switch ($jenis) {
            case 1:
                $channel = 'skrd_create_qris';
                $log = 'Create Qris SKRD';
                break;
            default:
                # code...
                break;
        }

        //TODO: Create QRIS
        $resCreateQRISBJB = QRISBJB::createQRIS($tokenQRISBJB, $amount, $no_hp);
        $resJsonQRIS      = $resCreateQRISBJB->json();

        //* LOG QRIS
        $dataQris = [
            'no_bayar' => $no_bayar,
            'data' => $resJsonQRIS
        ];
        Log::channel($channel)->info($log, $dataQris);

        if ($resCreateQRISBJB->successful()) {
            if ($resJsonQRIS["status"]["code"] != 200) {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat membuat QRIS BJB';
            } else {
                $err = false;
                $respondBody = $resJsonQRIS["body"]["CreateInvoiceQRISDinamisExtResponse"];
                $invoiceId = $respondBody["invoiceId"]["_text"];
                $textQRIS = $respondBody["stringQR"]["_text"];
            }
        } else {
            $err = true;
            $errMsg = 'Terjadi kegagalan saat membuat QRIS BJB. Error Server';
        }

        return [$err, $errMsg, $invoiceId, $textQRIS];
    }
}
