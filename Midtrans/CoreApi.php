<?php

namespace Midtrans;

/**
 * Provide charge and capture functions for Core API
 */
class CoreApi
{
    /**
     * Create transaction.
     *
     * @param mixed[] $params Transaction options
     */
    public static function charge($params)
    {
        $payloads = array(
            'payment_type' => 'credit_card'
        );

        if (isset($params['item_details'])) {
            $gross_amount = 0;
            foreach ($params['item_details'] as $item) {
                $gross_amount += $item['quantity'] * $item['price'];
            }
            $payloads['transaction_details']['gross_amount'] = $gross_amount;
        }

        $payloads = array_replace_recursive($payloads, $params);

        if (Config::$isSanitized) {
            Sanitizer::jsonRequest($payloads);
        }

        if (Config::$appendNotifUrl)
            Config::$curlOptions[CURLOPT_HTTPHEADER][] = 'X-Append-Notification: ' . Config::$appendNotifUrl;

        if (Config::$overrideNotifUrl)
            Config::$curlOptions[CURLOPT_HTTPHEADER][] = 'X-Override-Notification: ' . Config::$overrideNotifUrl;

        $result = ApiRequestor::post(
            Config::getBaseUrl() . '/charge',
            Config::$serverKey,
            $payloads
        );

        return $result;
    }

    /**
     * Capture pre-authorized transaction
     *
     * @param string $param Order ID or transaction ID, that you want to capture
     */
    public static function capture($param)
    {
        $payloads = array(
        'transaction_id' => $param,
        );

        $result = ApiRequestor::post(
            Config::getBaseUrl() . '/capture',
            Config::$serverKey,
            $payloads
        );

        return $result;
    }
}
