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
date_default_timezone_set('Asia/Jakarta');
$time_stamp = date("c");
$date = new DateTime($time_stamp);

$directDebitRefundByExternalIdBody = array(
    "originalExternalId" => "uzi-order-testing66cec41c7f905",
    "partnerRefundNo" =>  "uzi-order-testing66cec41c7f905" . "refund-0001".rand(),
    "reason" => "some-reason",
    "additionalInfo" => array(),
    "refundAmount" => array(
        "value" => "100.00",
        "currency" => "IDR"
    ));

$directDebitRefundByReferenceBody = array(
    "originalReferenceNo" => "A120240907120426ZsbsQvlcYBID",
    "reason" => "some-reason",
    "additionalInfo" => array(),
    "refundAmount" => array(
        "value" => "100.00",
        "currency" => "IDR"
    ));

$qrisRefundBody = array(
    "merchantId" => $merchant_id,
    "originalPartnerReferenceNo" => "uzi-order-testing66feb6218257b",
    "originalReferenceNo" => "A1202410031520025F17xSCZWMID",
    "partnerRefundNo" => "partner-refund-no-". uniqid(),
    "reason" => "refund reason",
    "refundAmount" => array(
        "value" => "100.00",
        "currency" => "IDR"
    ),
    "additionalInfo" => array(
        "foo" => "bar"
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
     * Example code for SnapBI, you can uncomment and run the code.
     * Below are example code to refund the transaction.
     * For Direct Debit, you can refund the transaction using externalId or referenceNo
     * The difference is based on the payload, you can refer to $directDebitRefundByExternalIdBody or $directDebitRefundByReferenceBody to see the value
     * For Qris refund, you can refer to $qrisRefundBody to see the value.
     */


    /**
     * Example code for Direct Debit refund
     */
    /**
     * Example code for Direct Debit refund using externalId
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($directDebitRefundByExternalIdBody)
        ->refund($external_id);

    /**
     * Example code for Direct Debit refund using externalId by re-using access token
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessToken("")
        ->withBody($directDebitRefundByExternalIdBody)
        ->refund($external_id);

    /**
     * Example code for Direct Debit refund using externalId by adding additional header
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withBody($directDebitRefundByExternalIdBody)
        ->refund($external_id);

    /**
     * Example code for Direct Debit refund using reference no
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($directDebitRefundByReferenceBody)
        ->refund($external_id);

    /**
     * Example code for Direct Debit refund using reference no by re-using access token
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessToken("")
        ->withBody($directDebitRefundByReferenceBody)
        ->refund($external_id);

    /**
     * Example code for Direct Debit refund using reference no by adding additional header
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withBody($directDebitRefundByReferenceBody)
        ->refund($external_id);

    /**
     * Example code for Qris refund
     */
    /**
     * Example code for Qris refund basic implementation
     */
    $snapBiResponse = SnapBi::qris()
        ->withBody($qrisRefundBody)
        ->refund($external_id);

    /**
     * Example code for Qris refund by re-using access token
     */
    $snapBiResponse = SnapBi::qris()
        ->withAccessToken("")
        ->withBody($qrisRefundBody)
        ->refund($external_id);

    /**
     * Example code for Qris refund by adding additional header
     */
    $snapBiResponse = SnapBi::qris()
        ->withAccessTokenHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withTransactionHeader([
            "debug-id"=> "va debug id",
            "X-DEVICE-ID"=>"va device id"
        ])
        ->withBody($qrisRefundBody)
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
