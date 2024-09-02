<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs for snap popup:
// https://docs.midtrans.com/en/snap/integration-guide?id=integration-steps-overview

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



date_default_timezone_set('Asia/Jakarta');
$time_stamp = date("c");
$date = new DateTime($time_stamp);
$external_id = "uzi-order-testing" . uniqid();
$external_id_status = "qa-test-axel-0000001as5";

// Add 10 minutes validity time
$date->modify('+10 minutes');

// Format the new date
$valid_until = $date->format('c');

$debitParamsArray = array(
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
            "payMethod" => "DANA",
            "payOption" => "DANA",
            "transAmount" => array(
                "value" => "100.0",
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
                    "value" => "100.00",
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

$vaCustomerNo = generateRandomNumber();
$vaParamsArray = array(
    "partnerServiceId"=> "   70012",
    "customerNo"=> $vaCustomerNo,
    "virtualAccountNo"=> "   70012" . $vaCustomerNo,
    "virtualAccountName"=> "Jokul Doe",
    "virtualAccountEmail"=> "jokul@email.com",
    "virtualAccountPhone"=> "6281828384858",
    "trxId"=> $external_id,
    "totalAmount"=> [
    "value"=> "10000.00",
        "currency"=> "IDR"
    ],
    "additionalInfo"=> [
    "merchantId"=> "G059876677",
        "bank"=> "mandiri",
        "flags"=> [
        "shouldRandomizeVaNumber"=> false
            ],
        "mandiri"=> [
        "billInfo1"=> "bank_name",
            "billInfo2"=> "mandiri",
            "billInfo3"=> "Name:",
            "billInfo4"=> "Budi Utomo",
            "billInfo5"=> "Class:",
            "billInfo6"=> "Computer Science",
            "billInfo7"=> "ID:",
            "billInfo8"=> "VT-12345"
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


$statusByExternalId = '{
  "originalExternalId": "qa-test-axel-0000001as5",
  "originalPartnerReferenceNo": "qa-test-axel-0000001as5",
  "serviceCode": "54",
  "additionalInfo" : {}
}';

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

$refundByExternalIdArray = array(
    "originalExternalId" => "qa-test-axel-0000001as5",
    "partnerRefundNo" => "qa-test-axel-0000001as5" . "refund-0001",
    "reason" => "some-reason",
    "additionalInfo" => array(),
    "refundAmount" => array(
    "value" => "100.00",
    "currency" => "IDR"
)
);

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


$statusByReferenceArrayzz = array(
    "originalReferenceNo" => "A120240828025158LSO0It0MjkID",
    "serviceCode" => "54",
    "latestTransactionStatus" => "04",
    "transAmount" => array(
        "value" => "100.00",
        "currency" => "IDR"
    ),
    "paidTime" => "2024-04-22T05:33:57.367183Z",
    "refundHistory" => array(
        array(
            "refundNo" => "A120240422060848ZP50k2YsR9ID",
            "partnerReferenceNo" => "qa-test-axel-0000003-refund-0001",
            "refundAmount" => array(
                "value" => "50.00",
                "currency" => "IDR"
            ),
            "refundStatus" => "00",
            "refundDate" => "2024-04-22T06:08:48.416Z",
            "reason" => "some-reason"
        ), array(
            "refundNo" => "A120240422060903bisk7gM97EID",
            "partnerReferenceNo" => "qa-test-axel-0000003-refund-0002",
            "refundAmount" => array(
                "value" => "50.00",
                "currency" => "IDR"
            ),
            "refundStatus" => "00",
            "refundDate" => "2024-04-22T06:09:04.12Z",
            "reason" => "some-reason"
        ),
    ),
    "additionalInfo" => array(
        "fraudStatus" => "accept",
        "validUpTo" => "2024-04-22T05:43:30Z",
        "payOptionDetails" => array(
            array(
                "payMethod" => "DANA",
                "payOption" => "DANA",
            )
        ),
        "metadata" => array(
            "internal_service" => "1oms",
            "x-service" => "oms",
            "x-source" => "oms",
            "tags" => "{ \"service_type\": \"GOPAY_OFFLINE\" }"
        )
    ),
    "responseCode" => "2005500",
    "responseMessage" => "Successful"
);

$cancelByReferenceArray = array(
  "originalReferenceNo" => "A120240902104935GBqSQK0gtQID"
);
$cancelByExternalIdArray = array(
    "originalExternalId" => "uzi-order-testing66d5983eabc71"
);

$snapBiResponse = null;
SnapBiConfig::$snapBiClientId = $client_id;
SnapBiConfig::$snapBiPrivateKey = $private_key;
SnapBiConfig::$snapBiClientSecret = $client_secret;
SnapBiConfig::$snapBiPartnerId = $partner_id;
SnapBiConfig::$snapBiChannelId = $partner_id;
SnapBiConfig::$snapBiChannelId = "12345";

try {

    /**
     * Example code for SnapBI, you can uncomment and run the code
     */

    /**
     * Example code to create va
     */
    $snapBiResponse = SnapBi::va()
        ->withBody($vaParamsArray)
        ->createPayment($external_id);
//
//    $snapBiResponse = SnapBi::va()
//        ->withAccessToken("")
//        ->withBody($vaParamsArray)
//        ->createPayment($external_id);
//
//    $snapBiResponse = SnapBi::va()
//        ->withAccessTokenHeader([
//            "debug-id"=> "va debug id",
//            "X-DEVICE-ID"=>"va device id"
//        ])
//        ->withBody($vaParamsArray)
//        ->createPayment($external_id);
//
//    $snapBiResponse = SnapBi::va()
//        ->withTransactionHeader([
//            "debug-id"=> "va debug id",
//            "X-DEVICE-ID"=>"va device id"
//        ])
//        ->withBody($vaParamsArray)
//        ->createPayment($external_id);
//
//    $snapBiResponse = SnapBi::va()
//        ->withAccessTokenHeader([
//            "debug-id"=> "va debug id",
//            "X-DEVICE-ID"=>"va device id"
//        ])
//        ->withTransactionHeader([
//            "debug-id"=> "va debug id",
//            "X-DEVICE-ID"=>"va device id"
//        ])
//        ->withBody($vaParamsArray)
//        ->createPayment($external_id);
    /**
     * Example code for Direct Debit (gopay/ dana/ shopeepay)
     */
//    $snapBiResponse = SnapBi::directDebit()
//        ->withBody($debitParamsArray)
//        ->createPayment($external_id);

//    $snapBiResponse = SnapBi::directDebit()
//        ->withAccessToken("")
//        ->withBody($debitParamsArray)
//        ->createPayment($external_id);
//
//    $snapBiResponse = SnapBi::directDebit()
//        ->withAccessTokenHeader([
//            "debug-id"=> "va debug id",
//            "X-DEVICE-ID"=>"va device id"
//        ])
//        ->withBody($debitParamsArray)
//        ->createPayment($external_id);
//
//    $snapBiResponse = SnapBi::directDebit()
//        ->withTransactionHeader([
//            "debug-id"=> "va debug id",
//            "X-DEVICE-ID"=>"va device id"
//        ])
//        ->withBody($debitParamsArray)
//        ->createPayment($external_id);
//
//    $snapBiResponse = SnapBi::directDebit()
//        ->withAccessTokenHeader([
//            "debug-id"=> "va debug id",
//            "X-DEVICE-ID"=>"va device id",
//        ])
//        ->withTransactionHeader([
//            "debug-id"=> "va debug id",
//            "X-DEVICE-ID"=>"va device id",
//        ])
//        ->withBody($debitParamsArray)
//        ->createPayment($external_id);
//
    /**
     * Example code for operation related with the transaction (getStatus/refund/cancel)
     */
    /**
     * Example code for getStatus using externalId and referenceNo
     */
//    $snapBiResponse = SnapBi::transaction()
//        ->withBody($statusByExternalIdArray)
//        ->getStatus($external_id);
//
//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessToken("")
//        ->withBody($statusByExternalIdArray)
//        ->getStatus($external_id);
//
//    $snapBiResponse = SnapBi::transaction()
//        ->withBody($statusByReferenceArray)
//        ->getStatus($external_id);
//
//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessToken("")
//        ->withBody($statusByReferenceArray)
//        ->getStatus($external_id);
//
    /**
     * Example code for refund using externalId and referenceNo
     */
//    $snapBiResponse = SnapBi::transaction()
//        ->withBody($refundByExternalIdArray)
//        ->refund($external_id);
//
//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessToken("")
//        ->withBody($refundByExternalIdArray)
//        ->refund($external_id);
//
//    $snapBiResponse = SnapBi::transaction()
//        ->withBody($refundByReferenceArray)
//        ->refund($external_id);
//
//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessToken("")
//        ->withBody($refundByReferenceArray)
//        ->refund($external_id);
//
    /**
     * Example code for cancel using externalId and referenceNo
     */
//    $snapBiResponse = SnapBi::transaction()
//        ->withBody($cancelByExternalIdArray)
//        ->cancel($external_id);
//
//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessToken("")
//        ->withBody($cancelByExternalIdArray)
//        ->cancel($external_id);
////
//    $snapBiResponse = SnapBi::transaction()
//        ->withBody($cancelByExternalIdArray)
//        ->withAccessTokenHeader([
//            "CHANNEL-ID" => "12345"
//        ])
//        ->withTransactionHeader([
//            "CHANNEL-ID" => "12345"
//        ])
//        ->cancel($external_id);
//
//    $snapBiResponse = SnapBi::transaction()
//        ->withAccessToken("")
//        ->withBody($cancelByReferenceArray)
//        ->cancel($external_id);

    /**
     * Example code to only get access token
     */
//    $snapBiResponse = SnapBi::transaction()
//        ->withBody($cancelByExternalIdArray)
//        ->getAccessToken();

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
