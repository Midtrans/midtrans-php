<?php

namespace SnapBi;

use Midtrans\Config;

/**
 * API methods to get transaction status, approve and cancel transactions
 */
class SnapBi
{

    const ACCESS_TOKEN = '/v1.0/access-token/b2b';
    const PAYMENT_HOST_TO_HOST = '/v1.0/debit/payment-host-to-host';
    const CREATE_VA = '/v1.0/transfer-va/create-va';
    const STATUS = '/v1.0/debit/status';
    const REFUND = '/v1.0/debit/refund';
    const CANCEL = '/v1.0/debit/cancel';
    private $apiPath;
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

    public function __construct($apiUrl)
    {
        $this->apiPath = $apiUrl;
        $this->timeStamp = date("c");
    }


    /**
     * this method chain is used to start creating direct debit payment
     */

    public static function directDebit()
    {
        $apiPath = self::PAYMENT_HOST_TO_HOST;
        return new self($apiPath);
    }

    /**
     * this method chain is used to start creating va(bank transfer) payment
     */
    public static function va()
    {
        $apiPath = self::CREATE_VA;
        return new self($apiPath);
    }

    /**
     * this method chain is used to handle transaction (getStatus, refund, cancel)
     */
    public static function transaction()
    {
        $apiPath = "";
        return new self($apiPath);
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
    public function withBody(array $body)
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

    /**
     * These method chain below are used to execute the preferred functionalities
     */

    /**
     * these method chain is used to execute create payment
     */
    public function createPayment($externalId)
    {
        return $this->createConnection($externalId);
    }

    /**
     * these method chain is used to cancel the transaction
     */
    public function cancel($externalId)
    {
        $this->apiPath = self::CANCEL;
        return $this->createConnection($externalId);
    }

    /**
     * these method chain is used to refund the transaction
     */
    public function refund($externalId)
    {
        $this->apiPath = self::REFUND;
        return $this->createConnection($externalId);
    }

    /**
     * these method chain is used to get the status of the transaction
     */
    public function getStatus($externalId)
    {
        $this->apiPath = self::STATUS;
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

    private function createConnection($externalId = null)
    {
        // Attempt to get the access token if it's not already set
        if (!$this->accessToken) {
            $access_token_response = $this->getAccessToken($this->timeStamp);

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
}
