<?php

namespace SnapBi;

use Exception;

class SnapBiApiRequestor
{
    public static function remoteCall($url, $header, $body)
    {
        $ch = curl_init($url);

        $curlHeaders = [];
        //convert headers array into the correct format
        foreach ($header as $key => $value) {
            $curlHeaders[] = "$key: $value";
        }

        $payload_json = json_encode($body);

        if (SnapBiConfig::$enableLogging){
            echo sprintf("Request Body: \n%s\n", $payload_json);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHeaders);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_json);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_VERBOSE, SnapBiConfig::$enableLogging);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(curl_error($ch));
        }
        $jsonResponse = json_decode($response);
        curl_close($ch);
        if (SnapBiConfig::$enableLogging){
            echo sprintf("Response Body: \n%s\n", $response);
        }
        
        return $jsonResponse;
    }
}