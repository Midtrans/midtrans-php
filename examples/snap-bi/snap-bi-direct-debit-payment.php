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

$debitParams = array(
    "partnerReferenceNo" => $external_id,
    "chargeToken" => "",
    "merchantId" => $merchant_id,
    "urlParam" => array(
        array(
            "url" => "https://www.google.com",
            "type" => "PAY_RETURN",
            "isDeeplink" => "Y"
        )
    ),
    "validUpTo" => $valid_until,
    "payOptionDetails" => array(
        array(
            "payMethod" => "GOPAY",
            "payOption" => "GOPAY_WALLET",
            "transAmount" => array(
                "value" => "10000.0",
                "currency" => "IDR"
            )
        )
    ),
    "additionalInfo" => array(
        "customerDetails" => array(
            "phone" => "081122334455",
            "firstName" => "Andri",
            "lastName" => "Litani",
            "email" => "andri@litani.com",
            "billingAddress" => array(
                "firstName" => "Andri",
                "lastName" => "Litani",
                "phone" => "081122334455",
                "address" => "billingAddress",
                "city" => "billingCity",
                "postalCode" => "12790",
                "countryCode" => "CZH"
            ),
            "shippingAddress" => array(
                "firstName" => "Andri",
                "lastName" => "Litani",
                "phone" => "081122334455",
                "address" => "shippingAddress",
                "city" => "shippingCity",
                "postalCode" => "12790",
                "countryCode" => "CZH"
            )
        ),
        "items" => array(
            array(
                "id" => "1",
                "price" => array(
                    "value" => "10000.00",
                    "currency" => "IDR"
                ),
                "quantity" => 1,
                "name" => "Apple",
                "brand" => "Apple",
                "category" => "Subscription",
                "merchantName" => "amazon prime",
                "url" => "itemUrl"
            )
        ),
        "metadata" => array()
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
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($debitParams)
        ->createPayment($external_id);

    /**
     * Example of using existing access token to create payment. You can uncomment and run the code
     * to change the payment method, you can change the value of the request body on the `payOptionDetails`
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessToken("")
        ->withBody($debitParams)
        ->createPayment($external_id);

    /**
     * Example of using additional header on access token and when doing transaction  header. You can uncomment and run the code
     * to change the payment method, you can change the value of the request body on the `payOptionDetails`
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id",
        ])
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id",
        ])
        ->withBody($debitParams)
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
