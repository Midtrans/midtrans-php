<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.

namespace Midtrans;

use SnapBi\SnapBi;
use SnapBi\SnapBiConfig;

require_once dirname(__FILE__) . '/../../Midtrans.php';
/**
 * SETUP YOUR CREDENTIALS HERE
 */

$client_id = "Zabcdefg-MIDTRANS-CLIENT-SNAP";

//make sure to add 3 newline "\n" to your private key as shown below
$private_key = "-----BEGIN PRIVATE KEY-----\nABCDEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC7Zk6kJjqamLddaN1lK03XJW3vi5zOSA7V+5eSiYeM9tCOGouJewN/Py58wgvRh7OMAMm1IbSZpAbcZbBa1=\n-----END PRIVATE KEY-----\n";
$client_secret = "ABcdefghiSrLJPgKRXqdjaSAuj5WDAbeaXAX8Vn7CWGHuBCfFgABCDVqRLvNZf8BaqPGKaksMjrZDrZqzZEbaA1AYFwBewIWCqLZr4PuvuLBqfTmYIzAbCakHKejABCa";
$partner_id = "partner-id";
$merchant_id = "M001234";

$channel_id = "12345";

$external_id = "uzi-order-testing" . uniqid();

$refundByExternalIdArray = array(
    "originalExternalId" => "uzi-order-testing66cec41c7f905",
    "partnerRefundNo" =>  "uzi-order-testing66cec41c7f905" . "refund-0001".rand(),
    "reason" => "some-reason",
    "additionalInfo" => array(),
    "refundAmount" => array(
        "value" => "100.00",
        "currency" => "IDR"
    ));

$refundByReferenceArray = array(
    "originalReferenceNo" => "A120240828062651Y0NQMbJkDOID",
    "reason" => "some-reason",
    "additionalInfo" => array(),
    "refundAmount" => array(
        "value" => "100.00",
        "currency" => "IDR"
    ));


$snapBiResponse = null;
SnapBiConfig::$snapBiClientId = $client_id;
SnapBiConfig::$snapBiPrivateKey = $private_key;
SnapBiConfig::$snapBiClientSecret = $client_secret;
SnapBiConfig::$snapBiPartnerId = $partner_id;
SnapBiConfig::$snapBiChannelId = $partner_id;
SnapBiConfig::$snapBiChannelId = $channel_id;

try {

    /**
     * Example code for SnapBI, you can uncomment and run the code.
     * Below are example code to refund the transaction.
     * You can refund the transaction using externalId or referenceNo
     * The difference is based on the payload, you can refer to $refundByExternalIdArray or $refundByReferenceArray to see the value
     */


    /**
     * Example code for refund using externalId
     */
    $snapBiResponse = SnapBi::transaction()
        ->withBody($refundByExternalIdArray)
        ->refund($external_id);

    /**
     * Example code for refund using externalId by re-using access token
     */
    $snapBiResponse = SnapBi::transaction()
        ->withAccessToken("")
        ->withBody($refundByExternalIdArray)
        ->refund($external_id);

    /**
     * Example code for refund using externalId by adding additional header
     */
    $snapBiResponse = SnapBi::transaction()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withBody($refundByExternalIdArray)
        ->refund($external_id);



    /**
     * Example code for refund using reference no
     */
    $snapBiResponse = SnapBi::transaction()
        ->withBody($refundByReferenceArray)
        ->refund($external_id);

    /**
     * Example code for refund using reference no by re-using access token
     */
    $snapBiResponse = SnapBi::transaction()
        ->withAccessToken("")
        ->withBody($refundByReferenceArray)
        ->refund($external_id);

    /**
     * Example code for refund using reference no by adding additional header
     */
    $snapBiResponse = SnapBi::transaction()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withBody($refundByReferenceArray)
        ->refund($external_id);


} catch (\Exception $e) {
    echo $e->getMessage();
}
echo "snap bi response = " . print_r($snapBiResponse, true), PHP_EOL;

function generateRandomNumber()
{
    $prefix = "6280"; // Fixed prefix
    $randomDigits = mt_rand(100000000, 999999999); // Generate 9 random digits
    return $prefix . $randomDigits;
}
