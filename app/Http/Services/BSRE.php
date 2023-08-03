<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Http;

class BSRE
{
    public static function bsreRes($nik, $passphrase, $file, $qrimage)
    {
        $fileTTD = '';
        $errMsg  = '';

        $dataBSRE = [
            'nik'        => $nik,
            'passphrase' => $passphrase,
            'tampilan'   => 'visible',
            'xAxis'      => 177,
            'yAxis'      => 840,
            'width'      => 1,
            'height'     => 795,
            'page'       => 1,
            'image'      => 'true',
            'reason'     => 'Tanda Tangan Digital Retribusi',
            'location'   => 'Tangerang Selatan'
        ];

        $url = config('app.bsre_ip');
        $res = Http::attach('file', $file, 'myfile.pdf')
            ->attach('imageTTD', $qrimage, 'myimg.png')
            ->withBasicAuth('esign', 'qwerty')
            ->post($url . 'api/sign/pdf', $dataBSRE);

       
        $dataJson = $res->json();
        if ($res->status() == 200) {
            if ($res->body()) {
                $err = false;
                $fileTTD = $res->body();
            } else {
                $err = true;
                $errMsg = 'Gagal melakukan tandatangan digital BSRE. Silahkan dicoba lagi';
            }
        } elseif($dataJson['error']) {
            $err = true;
            $errMsg = 'Gagal melakukan tandatangan digital BSRE. '. $dataJson['error'];
        } else{
            $err = true;
            $errMsg = 'Gagal melakukan tandatangan digital BSRE. Error Server';
        }

        return [$err, $errMsg, $fileTTD];
    }
}
