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
            $err = 'Terjadi kegagalan saat mengambil token. Error Server';
        }

        return [$err, $errMsg, $tokenBJB];
    }

    public static function createVABJBres($tokenBJB, $clientRefnum, $amount, $expiredDate, $customerName, $productCode, $jenis, $no_bayar)
    {
        $VABJB  = '';
        $errMsg = '';

        switch ($jenis) {
            case 1:
                $channel = 'create_va';
                $log = 'Create VA SKRD (create)';
                break;
            case 2:
                $channel = 'create_va';
                $log = 'Create VA SKRD (update)';
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
            'data' => $resJson
        ];
        Log::channel($channel)->info($log, $dataVA);

        if ($resCreateVABJB->successful()) {
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

    public static function updateVABJBres($tokenBJB, $amount, $expiredDate, $customerName, $va_number, $jenis, $no_bayar)
    {
        $VABJB  = '';
        $errMsg = '';

        switch ($jenis) {
            case 1:
                $channel = 'update_va';
                $log = 'Update VA SKRD (update)';
                break;
            case 2:
                $channel = 'update_va';
                $log = 'Update VA SKRD (delete - make VA expired)';
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
            'data' => $resJson
        ];
        Log::channel($channel)->info($log, $dataVA);

        if ($resUpdateVABJB->successful()) {
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
