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

$va_merchant_id = "G059876677";

$external_id = "uzi-order-testing" . uniqid();
$external_id_status = "qa-test-axel-0000001as5";

$directDebitStatusByExternalIdBody = array(
    "originalExternalId" => "uzi-order-testing66ce90ce90ee5",
    "originalPartnerReferenceNo" => "uzi-order-testing66ce90ce90ee5",
    "serviceCode" => "54"
);

$directDebitStatusByReferenceBody = array(
    "originalReferenceNo" => "A120240907120426ZsbsQvlcYBID",
    "serviceCode" => "54"
);


//make sure to include the spaces based on the createPayment response
$vaStatusBody = array(
    "partnerServiceId" => "    5818",
    "customerNo" => "628064192914",
    "virtualAccountNo" => "    5818628064192914",
    "inquiryRequestId" => "uzi-order-testing66dc4799e4af5",
    "paymentRequestId" => "uzi-order-testing66dc4799e4af5",
    "additionalInfo" => array(
        "merchantId" => $va_merchant_id
    )
);

$qrisStatusBody = array(
    "originalReferenceNo" => "A120240910100828anKJlXgsi6ID",
    "originalPartnerReferenceNo" => "uzi-order-testing66e01a9b8c6bf",
    "merchantId" => $merchant_id,
    "serviceCode" => "54"
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
     * Example code for SnapBI
     * The difference is based on the request body/ payload.
     * For Direct Debit you can refer to the variable $directDebitStatusByExternalIdBody or $directDebitStatusByReferenceBody to see the value.
     * For VA (Bank Transfer) you can refer to the variable $vaStatusBody to see the value.
     * For qris, you can refer to the variable $qrisStatusBody.
     */

    /**
     * Example code for Direct Debit getStatus using externalId
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($directDebitStatusByExternalIdBody)
        ->getStatus($external_id);

    /**
     * Example code for Direct Debit getStatus using externalId by re-using access token
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessToken("")
        ->withBody($directDebitStatusByExternalIdBody)
        ->getStatus($external_id);

    /**
     * Example code for Direct Debit getStatus using externalId by adding additional header
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessTokenHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withTransactionHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withBody($directDebitStatusByExternalIdBody)
        ->getStatus($external_id);


    /**
     * Example code for Direct Debit getStatus using referenceNo
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($directDebitStatusByReferenceBody)
        ->getStatus($external_id);

    $snapBiResponse = SnapBi::va()
        ->withBody($directDebitStatusByReferenceBody)
        ->getStatus($external_id);

    /**
     * Example code for Direct Debit getStatus using referenceNo by re-using access token
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessToken("")
        ->withBody($directDebitStatusByReferenceBody)
        ->getStatus($external_id);

    /**
     * Example code for Direct Debit getStatus using referenceNo by adding additional header
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessTokenHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withTransactionHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withBody($directDebitStatusByReferenceBody)
        ->getStatus($external_id);

    /**
     * Example code for VA getStatus
     */
    $snapBiResponse = SnapBi::va()
        ->withBody($vaStatusBody)
        ->getStatus($external_id);

    /**
     * Example code for VA getStatus by re-using access token
     */
    $snapBiResponse = SnapBi::va()
        ->withBody($vaStatusBody)
        ->withAccessToken("")
        ->getStatus($external_id);

    /**
     * Example code for VA getStatus by adding additional header
     */
    $snapBiResponse = SnapBi::va()
        ->withBody($vaStatusBody)
        ->withAccessTokenHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withTransactionHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->getStatus($external_id);

    /**
     * Example code for Qris getStatus
     */
    $snapBiResponse = SnapBi::qris()
        ->withBody($qrisStatusBody)
        ->getStatus($external_id);

    /**
     * Example code for Qris getStatus by re-using access token
     */
    $snapBiResponse = SnapBi::qris()
        ->withBody($qrisStatusBody)
        ->withAccessToken("")
        ->getStatus($external_id);

    /**
     * Example code for Qris getStatus by adding additional header
     */
    $snapBiResponse = SnapBi::qris()
        ->withBody($qrisStatusBody)
        ->withAccessTokenHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withTransactionHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->getStatus($external_id);

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
