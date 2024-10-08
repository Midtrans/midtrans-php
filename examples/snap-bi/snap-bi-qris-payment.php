<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.

namespace Midtrans;

use DateTime;
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

date_default_timezone_set('Asia/Jakarta');
$time_stamp = date("c");
$date = new DateTime($time_stamp);
$external_id = "uzi-order-testing" . uniqid();

// Add 10 minutes validity time
$date->modify('+10 minutes');

// Format the new date
$valid_until = $date->format('c');



$qrisBody = array(
    "partnerReferenceNo" => $external_id,
    "amount" => array(
        "value" => "1500.00",
        "currency" => "IDR"
    ),
    "merchantId" => $merchant_id,
    "validityPeriod" => "2030-07-03T12:08:56-07:00",
    "additionalInfo" => array(
        "acquirer" => "gopay",
        "items" => array(
            array(
                "id" => "8143fc4f-ec05-4c55-92fb-620c212f401e",
                "price" => array(
                    "value" => "1500.00",
                    "currency" => "IDR"
                ),
                "quantity" => 1,
                "name" => "test item name",
                "brand" => "test item brand",
                "category" => "test item category",
                "merchantName" => "Merchant Operation"
            )
        ),
        "customerDetails" => array(
            "email" => "merchant-ops@midtrans.com",
            "firstName" => "Merchant",
            "lastName" => "Operation",
            "phone" => "+6281932358123"
        ),
        "countryCode" => "ID",
        "locale" => "id_ID"
    )
);


$snapBiResponse = null;
SnapBiConfig::$snapBiClientId = $client_id;
SnapBiConfig::$snapBiPrivateKey = $private_key;
SnapBiConfig::$snapBiClientSecret = $client_secret;
SnapBiConfig::$snapBiPartnerId = $partner_id;
SnapBiConfig::$snapBiChannelId = $channel_id;
SnapBiConfig::$enableLogging = true;

try {

    /**
     * Example code for Direct Debit (gopay/ dana/ shopeepay) using Snap Bi, you can uncomment and run the code.
     * Below are example code to create va
     */

    /**
     *  Basic example
     * to change the payment method, you can change the value of the request body on the `payOptionDetails`
     */
    $snapBiResponse = SnapBi::qris()
        ->withBody($qrisBody)
        ->createPayment($external_id);

    /**
     * Example of using existing access token to create payment. You can uncomment and run the code
     * to change the payment method, you can change the value of the request body on the `payOptionDetails`
     */
    $snapBiResponse = SnapBi::qris()
        ->withAccessToken("")
        ->withBody($qrisBody)
        ->createPayment($external_id);

    /**
     * Example of using additional header on access token and when doing transaction  header. You can uncomment and run the code
     * to change the payment method, you can change the value of the request body on the `payOptionDetails`
     */
    $snapBiResponse = SnapBi::qris()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id",
        ])
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id",
        ])
        ->withBody($qrisBody)
        ->createPayment($external_id);

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

