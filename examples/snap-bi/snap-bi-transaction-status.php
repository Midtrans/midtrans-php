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
$external_id_status = "qa-test-axel-0000001as5";

$statusByExternalIdArray = array(
    "originalExternalId" => "uzi-order-testing66ce90ce90ee5",
    "originalPartnerReferenceNo" => "uzi-order-testing66ce90ce90ee5",
    "serviceCode" => "54",
    "additionalInfo" => array()
);

$statusByReferenceArray = array(
    "originalReferenceNo" => "A1202408280618283vcBaAmf7RID",
    "serviceCode" => "54",
    "additionalInfo" => array()
);

$snapBiResponse = null;
SnapBiConfig::$snapBiClientId = $client_id;
SnapBiConfig::$snapBiPrivateKey = $private_key;
SnapBiConfig::$snapBiClientSecret = $client_secret;
SnapBiConfig::$snapBiPartnerId = $partner_id;
SnapBiConfig::$snapBiChannelId = $partner_id;
SnapBiConfig::$snapBiChannelId = $channel_id;

try {

    /**
     * Example code for SnapBI, you can uncomment and run the code
     * The difference is based on the request body/ payload.
     * you can refer to the variable $statusByExternalIdArray or $statusByReferenceArray to see the value.
     */

    /**
     * Example code for getStatus using externalId
     */
    $snapBiResponse = SnapBi::transaction()
        ->withBody($statusByExternalIdArray)
        ->getStatus($external_id);

//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessToken("")
//        ->withBody($statusByExternalIdArray)
//        ->getStatus($external_id);

//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessTokenHeader([
//            "CHANNEL-ID" => "12345"
//        ])
//        ->withTransactionHeader([
//            "CHANNEL-ID" => "12345"
//        ])
//        ->withBody($statusByExternalIdArray)
//        ->getStatus($external_id);


    /**
     * Example code for getStatus using referenceNo
     */
//    $snapBiResponse = SnapBi::transaction()
//        ->withBody($statusByReferenceArray)
//        ->getStatus($external_id);

//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessToken("")
//        ->withBody($statusByReferenceArray)
//        ->getStatus($external_id);

//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessTokenHeader([
//            "CHANNEL-ID" => "12345"
//        ])
//        ->withTransactionHeader([
//            "CHANNEL-ID" => "12345"
//        ])
//        ->withBody($statusByReferenceArray)
//        ->getStatus($external_id);


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
