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

$external_id = "uzi-order-testing" . uniqid();
$customerVaNo = generateRandomNumber();

$vaParams = array(
    "partnerServiceId"=> "    1234",
    "customerNo"=> "0000000000",
    "virtualAccountNo"=> "    12340000000000",
    "virtualAccountName"=> "Jokul Doe",
    "virtualAccountEmail"=> "jokul@email.com",
    "virtualAccountPhone"=> "6281828384858",
    "trxId"=> $external_id,
    "totalAmount"=> [
        "value"=> "10000.00",
        "currency"=> "IDR"
    ],
    "additionalInfo"=> [
        "merchantId"=> $merchant_id,
        "bank"=> "bca",
        "flags"=> [
            "shouldRandomizeVaNumber"=> true
        ],
        "customerDetails"=> [
            "firstName"=> "Jokul",
            "lastName"=> "Doe",
            "email"=> "jokul@email.com",
            "phone"=> "+6281828384858",
            "billingAddress"=> [
                "firstName"=> "Jukul",
                "lastName"=> "Doe",
                "address"=> "Kalibata",
                "city"=> "Jakarta",
                "postalCode"=> "12190",
                "phone"=> "+6281828384858",
                "countryCode"=> "IDN"
            ],
            "shippingAddress"=> [
                "firstName"=> "Jukul",
                "lastName"=> "Doe",
                "address"=> "Kalibata",
                "city"=> "Jakarta",
                "postalCode"=> "12190",
                "phone"=> "+6281828384858",
                "countryCode"=> "IDN"
            ]
        ],
        "customField"=> [
            "1"=> "custom-field-1",
            "2"=> "custom-field-2",
            "3"=> "custom-field-3"
        ],
        "items"=> [
            [
                "id"=> "a1",
                "price"=> [
                    "value"=> "1000.00",
                    "currency"=> "IDR"
                ],
                "quantity"=> 3,
                "name"=> "Apel",
                "brand"=> "Fuji Apple",
                "category"=> "Fruit",
                "merchantName"=> "Fruit-store"

            ],
            [
                "id"=> "a2",
                "price"=> [
                    "value"=> "1000.00",
                    "currency"=> "IDR"
                ],
                "quantity"=> 7,
                "name"=> "Apel Malang",
                "brand"=> "Fuji Apple",
                "category"=> "Fruit",
                "merchantName"=> "Fruit-store"
            ]
        ]
    ]
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
     * Example code for SnapBI, you can uncomment and run the code.
     * Below are example code to create va
     */

    /**
     * basic implementation to create payment using va
     */
    $snapBiResponse = SnapBi::va()
        ->withBody($vaParams)
        ->createPayment($external_id);

    /**
     * You can re-use your existing accessToken by using ->withAccessToken
     */
    $snapBiResponse = SnapBi::va()
        ->withAccessToken("")
        ->withBody($vaParams)
        ->createPayment($external_id);

    /**
     * Adding custom header during accessToken request by using ->withAccessTokenHeader
     */
    $snapBiResponse = SnapBi::va()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withBody($vaParams)
        ->createPayment($external_id);

    /**
     * Adding custom header during transaction process request by using ->withTransactionHeader
     */
    $snapBiResponse = SnapBi::va()
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withBody($vaParams)
        ->createPayment($external_id);

    /**
     * Adding custom header during both access token  & transaction process request by using ->withAccessTokenHeader ->withTransactionHeader
     */
    $snapBiResponse = SnapBi::va()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withBody($vaParams)
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
