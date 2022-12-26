<?php

namespace App\Libraries;

use App\Http\Services\VABJB;

use Illuminate\Support\Facades\Log;

class VABJBRes
{
    public static function getTokenBJBres()
    {
        $tokenBJB = '';
        $errMsg = '';

        $resGetTokenBJB = VABJB::getTokenBJB();
        if ($resGetTokenBJB->successful()) {
            $resJson = $resGetTokenBJB->json();
            if ($resJson['rc'] != 0000) {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat mengambil token VA';
            } else {
                $err = false;
                $tokenBJB = $resJson['data'];
            }
        } else {
            $err = true;
            $err = 'Terjadi kegagalan saat mengambil token. Error Sever';
        }

        return [$err, $errMsg, $tokenBJB];
    }

    public static function createVABJBres($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode, $jenis, $no_bayar)
    {
        $VABJB = '';
        $errMsg = '';

        switch ($jenis) {
            case 1:
                $channel = 'skrd_create_va';
                $log = 'Create VA SKRD';
                break;
            default:
                # code...
                break;
        }

        $resCreateVABJB = VABJB::createVABJB($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode);
        if ($resCreateVABJB->successful()) {
            $resJson = $resCreateVABJB->json();
            //* LOG VA
            $dataVA = [
                'no_bayar' => $no_bayar,
                'data' => $resJson
            ];
            Log::channel($channel)->info($log, $dataVA);
            if (isset($resJson['rc']) != 0000) {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat membuat Virtual Account.';
            } else {
                $err = false;
                $VABJB = $resJson['va_number'];
            }
        } else {
            $err = true;
            $errMsg = 'Terjadi kegagalan saat membuat Virtual Account. Error Server';
        }

        return [$err, $errMsg, $VABJB];
    }
}
