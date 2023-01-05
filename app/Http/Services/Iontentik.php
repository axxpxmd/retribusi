<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class Iontentik
{
    public static function getListCert($nip)
    {
        $url   = config('app.signapi_ipserver');
        $token = config('app.signapi_bearer');

        $errMsg = '';
        $idCert = '';

        $res = Http::withToken($token)->post($url . 'listCert', [
            'username' => $nip
        ]);

        if ($res->successful()) {
            if ($res['code'] == 200) {
                $err = false;
                $arr = json_decode($res, true);
                $idCert = end($arr);
                $idCert = end($idCert);
                $idCert = $idCert['id'];
            } else {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat mengambil sertifikat elektronik.';
            }
        } else {
            $err = true;
            $err = 'Terjadi kegagalan saat mengambil sertifikat elektronik. Error Server';
        }

        return [$err, $errMsg, $idCert];
    }

    public static function getTokenGodem($nip)
    {
        $url   = config('app.signapi_ipserver');
        $token = config('app.signapi_bearer');

        $errMsg = '';
        $tokenGodem = '';

        $res = Http::withToken($token)->post($url . 'getToken', [
            'username' => $nip
        ]);

        if ($res->successful()) {
            if ($res['code'] == 200) {
                $err = false;
                $arr = json_decode($res, true);
                $tokenGodem = substr($arr['message'], 0, 6);
            } else {
                $err = true;
                $errMsg = 'Terjadi kegagalan saat mengambil token.';
            }
        } else {
            $err = true;
            $err = 'Terjadi kegagalan saat mengambil token. Error Server';
        }

        return [$err, $errMsg, $tokenGodem];
    }
}
