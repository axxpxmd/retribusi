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

    public static function iotentikRes($nip, $passphrase, $tokenGodem, $idCert, $qrimage, $file)
    {
        $fileTTD = '';
        $errMsg  = '';

        $data = [
            'username'   => $nip,
            'passphrase' => $passphrase,
            'token'      => $tokenGodem,
            'urx'  => 177,
            'ury'  => 840,
            'llx'  => 1,
            'lly'  => 795,
            'page' => 1,
            'idkeystore' => $idCert,
            'reason'     => 'Tanda Tangan Digital Retribusi',
            'location'   => 'Tangerang Selatan',
            'updated_at' => ''
        ];

        $url = config('app.signapi_ipserver');
        $res = Http::withToken(config('app.signapi_bearer'))
            ->attach('imageSign', $qrimage, 'myimg.png')
            ->attach('pdf', $file, 'myfile.pdf')
            ->post($url . 'signPDF', $data);

        if ($res->successful()) {
            $r = $res->json();
            if ($r['status'] == 200) {
                $err = false;
                $fileTTD = base64_decode($r['data'], true);
            } elseif ($r['status'] && $r['data']) {
                $err = true;
                $errMsg = 'Gagal melakukan tandatangan digital IOTENTIK. Error Code: ' .  $r['status'] . ' Message: ' . $r['data'];
            } else {
                $err = true;
                $errMsg = 'Gagal melakukan tandatangan digital IOTENTIK.';
            }
        } else {
            $err = true;
            $errMsg = 'Gagal melakukan tandatangan digital IOTENTIK . Error Server';
        }

        return [$err, $errMsg, $fileTTD];
    }
}
