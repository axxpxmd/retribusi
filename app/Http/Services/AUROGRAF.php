<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class AUROGRAF
{
    public static function getListCert($nik)
    {
        $url   = config('app.aurografapi_server');
        $token = config('app.aurograf_bearer');

        $errMsg = '';
        $aurografCerts = '';

        $res = Http::withToken($token)->get($url . 'api/clientapp/certs/' . $nik);

        if ($res->successful()) {
            $resJson = $res->json();
            if ($resJson['result']) {
                $err = false;
                $aurografCerts = $resJson['result'];
            } else {
                $err = true;
                $err = 'Anda belum mempunyai sertifikat elektronik';
            }
        } else {
            $err = true;
            $err = 'Terjadi kegagalan saat mengambil sertifikat elektronik. Error Server';
        }

        return [$err, $errMsg, $aurografCerts];
    }

    public static function sign($aurograf_cert_id, $nik, $passphrase, $file, $qrimage)
    {
        $url   = config('app.aurografapi_server');
        $token = config('app.aurograf_bearer');

        $fileTTD = '';
        $errMsg  = '';

        $dataAurograf = [
            'sign_at_page' => 0,
            'from_x' => 177,
            'from_y' => 840,
            'to_x' => 1,
            'to_y' => 795,
            'cert_id' => $aurograf_cert_id,
            'passphrase' => $passphrase
        ];

        //* Process TTE
        $res = Http::withToken($token)
            ->attach('push_sign_image_file', $qrimage, 'myimg.png')
            ->attach('file', $file, 'myfile.pdf')
            ->post($url . 'api/clientapp/sign/' . $nik, $dataAurograf);

        if ($res->successful()) {
            $err = false;
            $resJson = $res->json();
            $file_id = $resJson['result']['file_id'];
        } else {
            if ($res->json()) {
                $resJson = $res->json();
                $err = true;
                $errMsg = $resJson['errors'];
            } else {
                $err = true;
                $err = 'Terjadi kegagalan saat melakukan TTE. Error Server';
            }
        }

        if ($err) {
            return [$err, $errMsg, $fileTTD];
        }

        //* Get file TTE
        $getFileRes = Http::withToken($token)
            ->get($url . 'api/clientapp/file/' . $nik . '/download/' . $file_id);

        if ($getFileRes->successful()) {
            $err = false;
            $fileTTD = $getFileRes->body();
        } else {
            $err = true;
            $err = 'Terjadi kegagalan saat mengambil file TTE. Error Server';
        }

        return [$err, $errMsg, $fileTTD];
    }
}
