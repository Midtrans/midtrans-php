<?php

namespace SnapBi;

use Exception;

/**
 * Provide Snap-Bi functionalities (create transaction, refund, cancel, get status)
 */
class SnapBi
{

    const ACCESS_TOKEN = '/v1.0/access-token/b2b';
    const PAYMENT_HOST_TO_HOST = '/v1.0/debit/payment-host-to-host';
    const CREATE_VA = '/v1.0/transfer-va/create-va';
    const DEBIT_STATUS = '/v1.0/debit/status';
    const DEBIT_REFUND = '/v1.0/debit/refund';
    const DEBIT_CANCEL = '/v1.0/debit/cancel';
    const VA_STATUS = '/v1.0/transfer-va/status';
    const VA_CANCEL = '/v1.0/transfer-va/delete-va';
    const QRIS_PAYMENT = '/v1.0/qr/qr-mpm-generate';
    const QRIS_STATUS = '/v1.0/qr/qr-mpm-query';
    const QRIS_REFUND = '/v1.0/qr/qr-mpm-refund';
    const QRIS_CANCEL = '/v1.0/qr/qr-mpm-cancel';
    private $apiPath;
    private $paymentMethod;
    private $accessTokenHeader = [];
    private $transactionHeader = [];
    private $accessToken;
    private $body;
    private $privateKey;
    private $clientId;
    private $partnerId;
    private $channelId;
    private $clientSecret;
    private $deviceId;
    private $debugId;
    private $timeStamp;
    private $signature;
    private $timestamp;
    private $notificationUrlPath;

    public function __construct($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        $this->timeStamp = date("c");
    }

    /**
     * this method chain is used to start Direct Debit (Gopay, Shopeepay, Dana) related transaction
     */

    public static function directDebit()
    {
        return new self("directDebit");
    }

    /**
     * this method chain is used to start VA(Bank Transfer) related transaction
     */
    public static function va()
    {
        return new self("va");
    }
    /**
     * this method chain is used to start Qris related transaction
     */
    public static function qris()
    {
        return new self("qris");
    }
    /**
     * this method chain is used to verify webhook notification
     */
    public static function notification(){
        return new self("");
    }
    /**
     * this method chain is used to add additional header during access token request
     */
    public function withAccessTokenHeader(array $headers)
    {
        $this->accessTokenHeader = array_merge($this->accessTokenHeader, $headers);
        return $this;
    }

    /**
     * this method chain is used to add additional header during transaction process (create payment/ get status/ refund/ cancel)
     */
    public function withTransactionHeader(array $headers)
    {
        $this->transactionHeader = array_merge($this->transactionHeader, $headers);
        return $this;
    }

    /**
     * this method chain is used to supply access token that you already have, and want to re-use
     */
    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * this method chain is used to supply the request body/ payload
     */
    public function withBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * These method chains below are config related method chain that can be used as an option
     */
    public function withPrivateKey($privateKey)
    {
        SnapBiConfig::$snapBiPrivateKey = $privateKey;
        return $this;
    }

    public function withClientId($clientId)
    {
        SnapBiConfig::$snapBiClientId = $clientId;
        return $this;
    }

    public function withClientSecret($clientSecret)
    {
        SnapBiConfig::$snapBiClientSecret = $clientSecret;
        return $this;
    }

    public function withPartnerId($partnerId)
    {
        SnapBiConfig::$snapBiPartnerId = $partnerId;
        return $this;
    }

    public function withChannelId($channelId)
    {
        SnapBiConfig::$snapBiChannelId = $channelId;
        return $this;
    }

    public function withDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;
        return $this;
    }

    public function withDebuglId($debugId)
    {
        $this->debugId = $debugId;
        return $this;
    }

    public function withSignature($signature)
    {
        $this->signature = $signature;
        return $this;
    }
    public function withTimeStamp($timeStamp)
    {
        $this->timestamp = $timeStamp;
        return $this;
    }
    public function withNotificationUrlPath($notificationUrlPath)
    {
        $this->notificationUrlPath = $notificationUrlPath;
        return $this;
    }

    /**
     * these method chain is used to execute create payment
     */
    public function createPayment($externalId)
    {
        $this->apiPath = $this->setupCreatePaymentApiPath($this->paymentMethod);
        return $this->createConnection($externalId);
    }

    /**
     * these method chain is used to cancel the transaction
     */
    public function cancel($externalId)
    {
        $this->apiPath = $this->setupCancelApiPath($this->paymentMethod);
        return $this->createConnection($externalId);
    }

    /**
     * these method chain is used to refund the transaction
     */
    public function refund($externalId)
    {
        $this->apiPath = $this->setupRefundApiPath($this->paymentMethod);
        return $this->createConnection($externalId);
    }

    /**
     * these method chain is used to get the status of the transaction
     */
    public function getStatus($externalId)
    {
        $this->apiPath = $this->setupGetStatusApiPath($this->paymentMethod);
        return $this->createConnection($externalId);
    }


    /**
     * these method chain is used to get the access token
     */
    public function getAccessToken()
    {
        $snapBiAccessTokenHeader = $this->buildAccessTokenHeader($this->timeStamp);
        $openApiPayload = array(
            'grant_type' => 'client_credentials',
        );
        return SnapBiApiRequestor::remoteCall(SnapBiConfig::getSnapBiTransactionBaseUrl() . self::ACCESS_TOKEN, $snapBiAccessTokenHeader, $openApiPayload);
    }

    /**
     * @throws Exception
     */
    public function  isWebhookNotificationVerified(){
        if (!SnapBiConfig::$snapBiPublicKey){
            throw new Exception(
                'The public key is null, You need to set the public key from SnapBiConfig.' .
                'For more details contact support at support@midtrans.com if you have any questions.'
            );
        }
        $notificationHttpMethod = "POST";
        $minifiedNotificationBodyJsonString = json_encode($this->body);
        $hashedNotificationBodyJsonString = hash('sha256', $minifiedNotificationBodyJsonString);
        $rawStringDataToVerifyAgainstSignature = $notificationHttpMethod . ':' . $this->notificationUrlPath . ':' . $hashedNotificationBodyJsonString . ':' . $this->timestamp;
        $isSignatureVerified = openssl_verify(
            $rawStringDataToVerifyAgainstSignature,
            base64_decode($this->signature),
            SnapBiConfig::$snapBiPublicKey,
            OPENSSL_ALGO_SHA256
        );
        return $isSignatureVerified === 1;
    }
    private function createConnection($externalId = null)
    {
        // Attempt to get the access token if it's not already set
        if (!$this->accessToken) {
            $access_token_response = $this->getAccessToken();

            // If getting the access token failed, return the response from getAccessToken
            if (!isset($access_token_response->accessToken)) {
                return $access_token_response;
            }
            // Set the access token if it was successfully retrieved
            $this->accessToken = $access_token_response->accessToken;
        }
        // Proceed with the payment creation if access token is available
        $snapBiTransactionHeader = $this->buildSnapBiTransactionHeader($externalId, $this->timeStamp);
        // Make the remote call and return the response
        return SnapBiApiRequestor::remoteCall(SnapBiConfig::getSnapBiTransactionBaseUrl() . $this->apiPath, $snapBiTransactionHeader, $this->body);
    }

    public static function getSymmetricSignatureHmacSh512($accessToken, $requestBody, $method, $path, $clientSecret, $timeStamp)
    {
        // Minify and hash the request body
        $minifiedBody = json_encode($requestBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $hashedBody = hash('sha256', $minifiedBody, true); // Get binary digest
        $hexEncodedHash = bin2hex($hashedBody);
        $lowercaseHexHash = strtolower($hexEncodedHash);
        // Construct the payload
        $payload = strtoupper($method) . ":" . $path . ":" . $accessToken . ":" . $lowercaseHexHash . ":" . $timeStamp;
        // Generate HMAC using SHA512
        $hmac = hash_hmac('sha512', $payload, $clientSecret, true);
        // Encode the result to Base64
        return base64_encode($hmac);
    }

    public static function getAsymmetricSignatureSha256WithRsa($client_id, $x_time_stamp, $private_key)
    {
        $stringToSign = $client_id . "|" . $x_time_stamp;
        $binarySignature = null;
        openssl_sign($stringToSign, $binarySignature, $private_key, OPENSSL_ALGO_SHA256);
        return base64_encode($binarySignature);
    }

    /**
     * @param $externalId
     * @param $timeStamp
     * @return array
     */
    private function buildSnapBiTransactionHeader($externalId, $timeStamp)
    {
        $snapBiTransactionHeader = array(
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "X-PARTNER-ID" => SnapBiConfig::$snapBiPartnerId,
            "X-EXTERNAL-ID" => $externalId,
            "X-DEVICE-ID" => $this->deviceId,
            "CHANNEL-ID" => SnapBiConfig::$snapBiChannelId,
            "debug-id" => $this->debugId,
            "Authorization" => "Bearer " . $this->accessToken,
            "X-TIMESTAMP" => $timeStamp,
            "X-SIGNATURE" => SnapBi::getSymmetricSignatureHmacSh512(
                $this->accessToken,
                $this->body,
                "post",
                $this->apiPath,
                SnapBiConfig::$snapBiClientSecret,
                $timeStamp
            ),
        );
        //if withTransactionHeader is used, the header will be merged with the default header
        if (isset($this->transactionHeader)) {
            $snapBiTransactionHeader = array_merge($snapBiTransactionHeader, $this->transactionHeader);
        }
        return $snapBiTransactionHeader;
    }

    /**
     * @param $timeStamp
     * @return array
     */
    private function buildAccessTokenHeader($timeStamp)
    {
        $snapBiAccessTokenHeader = array(
            "Content-Type" => "application/json",
            "Accept" => "application/json",
            "X-CLIENT-KEY" => SnapBiConfig::$snapBiClientId,
            "X-SIGNATURE" => SnapBi::getAsymmetricSignatureSha256WithRsa(SnapBiConfig::$snapBiClientId, $timeStamp, SnapBiConfig::$snapBiPrivateKey),
            "X-TIMESTAMP" => $timeStamp,
            "debug-id" => $this->debugId
        );
        //if withAccessTokenHeader is used, the header will be merged with the default header
        if (isset($this->accessTokenHeader)) {
            $snapBiAccessTokenHeader = array_merge($snapBiAccessTokenHeader, $this->accessTokenHeader);
        }
        return $snapBiAccessTokenHeader;
    }

    private function setupCreatePaymentApiPath($paymentMethod)
    {
        switch ($paymentMethod) {
            case "va":
                return self::CREATE_VA;
            case "qris":
                return self::QRIS_PAYMENT;
            default:
                return self::PAYMENT_HOST_TO_HOST;
        }
    }
    private function setupRefundApiPath($paymentMethod)
    {
        switch ($paymentMethod) {
            case "qris":
                return self::QRIS_REFUND;
            default:
                return self::DEBIT_REFUND;
        }
    }

    private function setupCancelApiPath($paymentMethod)
    {
        switch ($paymentMethod) {
            case "va":
                return self::VA_CANCEL;
            case "qris":
                return self::QRIS_CANCEL;
            default:
                return self::DEBIT_CANCEL;
        }
    }

    private function setupGetStatusApiPath($paymentMethod)
    {
        switch ($paymentMethod) {
            case "va":
                return self::VA_STATUS;
            case "qris":
                return self::QRIS_STATUS;
            default:
                return self::DEBIT_STATUS;
        }
    }
}
