<?php

namespace App\Libraries;

use App\Http\Services\QRISBJB;
use Explorin\Tebot\Services\Tebot;
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
                $res_message = "status : " .  $resJsonQRIS["status"]["code"] . " | " . "message : " . $resJsonQRIS["status"]["description"];
                $errMsg = 'Terjadi kegagalan saat mengambil token QRIS BJB';
            } else {
                $err = false;
                $tokenQRISBJB = $resGetTokenQRISBJB->header('X-AUTH-TOKEN');
            }
        } else {
            $err = true;
            $errMsg = 'Terjadi kegagalan saat mengambil token QRIS BJB. Error Server';
        }

        if ($err) {
            QRISBJBRes::sendLog($errMsg . " | " . $res_message);
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
                $log = 'Create Qris SKRD (create)';
                break;
            case 2:
                $log = 'Update Qris SKRD (update)';
                break;
            case 3:
                $log = 'Update Qris STRD (perbarui)';
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
            'data' => $resJsonQRIS,
            'no_hp' => $no_hp,
        ];
        Log::channel('create_qris')->info($log, $dataQris);

        if ($resCreateQRISBJB->successful()) {
            if ($resJsonQRIS["status"]["code"] != 200) {
                $err = true;
                $res_message = "status : " .  $resJsonQRIS["status"]["code"] . " " . "message : " . $resJsonQRIS["status"]["description"];
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

        if ($err) {
            QRISBJBRes::sendLog($errMsg . " " . $res_message);
        }

        return [$err, $errMsg, $invoiceId, $textQRIS];
    }

    // send log to telegram with tebot
    public static function sendLog($errMsg)
    {
        $logError = 'Message : ' . $errMsg;
        if (config('app.log_tebot') == 1) {
            Tebot::alert($logError)->channel('log_qris');
        }
    }
}
