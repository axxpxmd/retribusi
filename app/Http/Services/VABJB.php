<?php

namespace App\Http\Services;

use Carbon\Carbon;

class VABJB
{
    public static function getTokenBJB()
    {
        /* Get Token From Bank BJB
         * TOKEN REQUEST (POST /oauth/client/token)
         */
        $timestamp_now   = Carbon::now()->timestamp;
        $timestamp_1hour = Carbon::now()->addHour()->timestamp;

        $url = config('app.ip_api_bjb');
        $client_id = config('app.client_id_bjb');
        $key = config('app.key_bjb');

        $payload   = array(
            "sub" => "va-online",
            "aud" => "access-token",
            "iat" => $timestamp_now,
            "exp" => $timestamp_1hour
        );

        $jwt = JWT::encode($payload, $key, 'HS256', $client_id); // Create JWT Signature (HMACSHA256)
        $res = Http::contentType("text/plain")->send('POST', $url . 'oauth/client/token', [
            'body' => $jwt
        ]);

        return $res;
    }

    public static function CheckVABJB()
    {
        $url = config('app.ip_api_bjb');
        $key = config('app.key_bjb');
        $cin = config('app.cin_bjb');
        $currency      = "360";
        $timestamp_now = Carbon::now()->timestamp;

        // Base Signature
        $signature = 'path=/billing/' . $cin . '/' . $va_number . '&method=POST&token=' . $tokenBJB . '&timestamp=' . $timestamp_now . '&body=""';
        $sha256    = hash_hmac('sha256', $signature, $key);
    }
}
