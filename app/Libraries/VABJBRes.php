<?php

namespace App\Libraries;

use App\Http\Services\VABJB;
use Explorin\Tebot\Services\Tebot;
use Illuminate\Support\Facades\Log;

class VABJBRes
{
    public static function getTokenBJBres()
    {
        $tokenBJB = '';
        $errMsg = '';

        $resGetTokenBJB = VABJB::getTokenBJB();
        $resJson = $resGetTokenBJB->json();

        //* LOG Token
        $dataToken = [
            'data' => $resJson
        ];
        Log::channel('token')->info('create token', $dataToken);

        if ($resGetTokenBJB->successful()) {
            if ($resJson['rc'] != 0000) {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat mengambil token VA. Message : ' . $resJson['message'];
            } else {
                $err = false;
                $tokenBJB = $resJson['data'];
            }
        } else {
            $err = true;
            $errMsg = 'Terjadi kegagalan saat mengambil token. Error Server';
        }

         //* Log Send to Telegram
        if ($err) {
            VABJBRes::sendLog('Terjadi kegagalan saat mengambil token'. ' | Data : ' . json_encode($dataToken));
        }

        return [$err, $errMsg, $tokenBJB];
    }

    public static function createVABJBres($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode, $jenis, $no_bayar)
    {
        $VABJB  = '';
        $errMsg = '';

        switch ($jenis) {
            case 1:
                $log = 'Create VA SKRD (create)';
                break;
            case 2:
                $log = 'Create VA SKRD (update)';
                break;
            case 3:
                $log = 'Create VA STRD (perbarui)';
                break;
            default:
                # code...
                break;
        }

        $resCreateVABJB = VABJB::createVABJB($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode);
        $resJson = $resCreateVABJB->json();

        //* LOG VA
        $dataVA = [
            'no_bayar' => $no_bayar,
            'data' => $resJson,
            'payload' => [$tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode]
        ];
        Log::channel('create_va')->info($log, $dataVA);

        if ($resCreateVABJB->successful()) {
            if ($resJson['response_code'] == "0000") {
                $err = false;
                $VABJB = $resJson['va_number'];
            } else {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat membuat Virtual Account. Hubungi Administrator';
            }
        } else {
            $err = true;
            $errMsg = 'Terjadi kegagalan saat membuat Virtual Account. Hubungi Administrator';
        }

        //* Log Send to Telegram
        if ($err) {
            VABJBRes::sendLog($log . ' | Data : ' . json_encode($dataVA));
        }

        return [$err, $errMsg, $VABJB];
    }

    public static function updateVABJBres($tokenBJB, $amount, $expiredDate, $customerName, $va_number, $jenis, $no_bayar)
    {
        $VABJB  = '';
        $errMsg = '';

        switch ($jenis) {
            case 1:
                $log = 'Update VA SKRD (update)';
                break;
            case 2:
                $log = 'Update VA SKRD (delete - make VA expired)';
                break;
            case 3:
                $log = 'Update VA STRD (perbarui)';
                break;
            default:
                # code...
                break;
        }

        $resUpdateVABJB = VABJB::updateVaBJB($tokenBJB, $amount, $expiredDate, $customerName, $va_number);
        $resJson = $resUpdateVABJB->json();

        //* LOG VA
        $dataVA = [
            'no_bayar' => $no_bayar,
            'data' => $resJson,
            'payload' => [$tokenBJB, $amount, $expiredDate, $customerName, $va_number]
        ];
        Log::channel('update_va')->info($log, $dataVA);

        if ($resUpdateVABJB->successful()) {
            if ($resJson['response_code'] == "0000") {
                $err = false;
                $VABJB = $resJson['va_number'];
            } else {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat membuat Virtual Account. Hubungi Administrator';
            }
        } else {
            $err = true;
            $errMsg = 'Terjadi kegagalan saat membuat Virtual Account. Hubungi Administrator';
        }

        //* Log Send to Telegram
        if ($err) {
            VABJBRes::sendLog($log . ' | Data : ' . json_encode($dataVA));
        }

        return [$err, $errMsg, $VABJB];
    }

    public static function CheckVABJBres($tokenBJB, $va_number, $jenis, $no_bayar)
    {
        $VABJB  = '';
        $errMsg = '';
        $status = 0;
        $transactionTime   = null;
        $transactionAmount = null;

        switch ($jenis) {
            case 1:
                $log = 'Check inquiry VA STS (show)';
                break;
            case 2:
                $log = 'Check inquiry VA STS (edit)';
                break;
            default:
                # code...
                break;
        }

        $resCheckVABJB = VABJB::CheckVABJB($tokenBJB, $va_number);
        $resJson = $resCheckVABJB->json();

        //* LOG VA
        $dataVA = [
            'no_bayar' => $no_bayar,
            'data' => $resJson,
            'payload' => [$tokenBJB, $va_number]
        ];
        Log::channel('check_va')->info($log, $dataVA);

        if ($resCheckVABJB->successful()) {
            if ($resJson['response_code'] == "0000") {
                $err = false;
                $VABJB  = $resJson['va_number'];
                $status = $resJson['status'];
                $transactionTime = $resJson['transactions']['transaction_date'];
                $transactionAmount = $resJson['transactions']['transaction_amount'];
            } else {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat membuat Virtual Account. Hubungi Administrator';
            }
        } else {
            $err = true;
            $errMsg = 'Terjadi kegagalan saat membuat Virtual Account. Hubungi Administrator';
        }

        //* Log Send to Telegram
        if ($err) {
            VABJBRes::sendLog($log . ' | Data : ' . json_encode($dataVA));
        }

        return [$err, $errMsg, $VABJB, $status, $transactionTime, $transactionAmount];
    }

    public static function sendLog($errMsg)
    {
        $logError = 'Message : ' . $errMsg;
        if (config('app.log_tebot') == 1) {
            Tebot::alert($logError)->channel('log_va');
        }
    }
}
