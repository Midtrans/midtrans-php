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
$va_merchant_id = "G012345677";

$external_id = "uzi-order-testing" . uniqid();

$directDebitCancelByReferenceBody = array(
    "originalReferenceNo" => "A1202409071547203VpKvjM8MrID"
);
$directDebitCancelByExternalIdBody = array(
    "originalExternalId" => "uzi-order-testing66dc75ab3b96c"
);

$vaCancelBody = array(
    "partnerServiceId" => "    5818",
    "customerNo" => "628014506680",
    "virtualAccountNo" => "    5818628014506680",
    "trxId" => "uzi-order-testing66dc76754bf1c",
    "additionalInfo" => array(
        "merchantId" => $va_merchant_id
    )
);
$qrisCancelBody = array(
    "originalReferenceNo" => "A120241003151650FgOcD5vWIVID",
    "merchantId" => $merchant_id,
    "reason" => "cancel reason",
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
     * To cancel transaction you can use externalId or referenceNo.
     * The difference is based on the request body/ payload.
     * you can refer to the variable $directDebitCancelByReferenceBody or $directDebitCancelByExternalIdBody (for direct debit) or $vaCancelBody (for va) to see the value.
     *
     * Below are example code to cancel the transaction.
     */

    /**
     * Example code for Direct Debit cancel using externalId
     */

    /**
     * Basic implementation of Direct Debit to cancel transaction
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($directDebitCancelByExternalIdBody)
        ->cancel($external_id);

    /**
     * Example code of Direct Debit to cancel transaction using your existing access token
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessToken("")
        ->withBody($directDebitCancelByExternalIdBody)
        ->cancel($external_id);

    /**
     * Example code of Direct Debit to cancel transaction by adding or overriding the accessTokenHeader and TranasctionHeader
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($directDebitCancelByExternalIdBody)
        ->withAccessTokenHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withTransactionHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->cancel($external_id);

    /**
     * Example code for Direct Debit to cancel using referenceNo
     */

    /**
     * Basic implementation of Direct Debit to cancel transaction
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($directDebitCancelByReferenceBody)
        ->cancel($external_id);

    /**
     * Example code of Direct Debit to cancel transaction using your existing access token
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withAccessToken("")
        ->withBody($directDebitCancelByReferenceBody)
        ->cancel($external_id);

    /**
     * Example code of Direct Debit to cancel transaction by adding or overriding the accessTokenHeader and TransactionHeader
     */
    $snapBiResponse = SnapBi::directDebit()
        ->withBody($directDebitCancelByReferenceBody)
        ->withAccessTokenHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withTransactionHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->cancel($external_id);

    /**
     * Example code for VA (Bank Transfer) to cancel transaction
     */

    /**
     * Basic implementation of VA (Bank Transfer) to cancel transaction
     */
    $snapBiResponse = SnapBi::va()
        ->withBody($vaCancelBody)
        ->cancel($external_id);

    /**
     * Example code of VA (Bank Transfer) to cancel transaction using your existing access token
     */
    $snapBiResponse = SnapBi::va()
        ->withAccessToken("")
        ->withBody($vaCancelBody)
        ->cancel($external_id);

    /**
     * Example code of VA (Bank Transfer) to cancel transaction by adding or overriding the accessTokenHeader and TransactionHeader
     */
    $snapBiResponse = SnapBi::va()
        ->withBody($vaCancelBody)
        ->withAccessTokenHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withTransactionHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->cancel($external_id);

    /**
     * Example code for Qris to cancel transaction
     */

    /**
     * Basic implementation of Qris to cancel transaction
     */
    $snapBiResponse = SnapBi::qris()
        ->withBody($qrisCancelBody)
        ->cancel($external_id);

    /**
     * Example code of Qris to cancel transaction using your existing access token
     */
    $snapBiResponse = SnapBi::qris()
        ->withAccessToken("")
        ->withBody($qrisCancelBody)
        ->cancel($external_id);

    /**
     * Example code of Qris to cancel transaction by adding or overriding the accessTokenHeader and TransactionHeader
     */
    $snapBiResponse = SnapBi::qris()
        ->withBody($qrisCancelBody)
        ->withAccessTokenHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->withTransactionHeader([
            "CHANNEL-ID" => "12345"
        ])
        ->cancel($external_id);

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

