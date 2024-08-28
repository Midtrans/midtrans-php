<?php
namespace SnapBi;
use Exception;

class SnapBiApiRequestor
{
    public static function remoteCall($url, $header, $body) {
        $ch = curl_init($url);

        $curlHeaders = [];
        //convert headers array into the correct format
        foreach ($header as $key => $value) {
            $curlHeaders[] = "$key: $value";
        }

        $payload_json = json_encode($body);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_json);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        curl_close($ch);

        return json_decode($response);
    }
}