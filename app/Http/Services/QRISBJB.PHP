<?php

namespace App\Http\Services;

use Carbon\Carbon;

use Illuminate\Support\Facades\Http;

class QRISBJB
{
    public static function getToken()
    {
        $time = Carbon::now();
        $date = $time->format('Y-m-d');
        $hour = $time->format('H:m:i');

        $url  = config('app.ip_qris');
        $msisdn   = config('app.msisdn_bjb');
        $password = config('app.password_bjb');

        // Body / Payload
        $metadata = [
            "datetime" => $date . 'T' . $hour . '.' . '450Z',
            "deviceId" => "100",
            "devicePlatform" => "Linux",
            "deviceOSVersion" => "",
            "deviceType" => "",
            "latitude" => "",
            "longitude" => "",
            "appId" => "20",
            "appVersion" => "1.0"
        ];

        $body = [
            "msisdn" => $msisdn,
            "password" => $password
        ];

        $res = Http::withHeaders([
            'Content-Type'  => 'application/json'
        ])->post($url . '/mobile-webconsole/apps/pocket/requestTokenFintech/', [
            'metadata' => $metadata,
            'body' => $body
        ]);

        return $res;
    }

    public static function createQRIS($tokenQRISBJB, $amount)
    {
        $time = Carbon::now();
        $date = $time->format('Y-m-d');
        $hour = $time->format('H:m:i');

        $url  = config('app.ip_qris');
        $msisdn = config('app.msisdn_bjb');

        // Body / Payload
        $metadata = [
            "datetime" => $date . 'T' . $hour . '.' . '450Z',
            "deviceId" => "100",
            "devicePlatform" => "Linux",
            "deviceOSVersion" => "",
            "deviceType" => "",
            "latitude" => "",
            "longitude" => "",
            "appId" => "20",
            "appVersion" => "1.0"
        ];

        $body = [
            "merchantAccountNumber" => $msisdn,
            "amount" => $amount,
            "expInSecond" => '2629746'
        ];

        $res = Http::withHeaders([
            'X-AUTH-TOKEN' => $tokenQRISBJB,
            'Content-Type'  => 'application/json'
        ])->post($url . '/mobile-webconsole/apps/4/pbTransactionAdapter/createInvoiceQRISDinamisExt', [
            'metadata' => $metadata,
            'body' => $body
        ]);

        return $res;
    }
}
