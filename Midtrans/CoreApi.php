<?php

namespace Midtrans;

use Exception;
/**
 * Provide charge and capture functions for Core API
 */
class CoreApi
{
    /**
     * Create transaction.
     *
     * @param mixed[] $params Transaction options
     * @return mixed
     * @throws Exception
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

        return ApiRequestor::post(
            Config::getBaseUrl() . '/charge',
            Config::$serverKey,
            $payloads
        );
    }

    /**
     * Capture pre-authorized transaction
     *
     * @param string $param Order ID or transaction ID, that you want to capture
     * @return mixed
     * @throws Exception
     */
    public static function capture($param)
    {
        $payloads = array(
            'transaction_id' => $param,
        );

        return ApiRequestor::post(
            Config::getBaseUrl() . '/capture',
            Config::$serverKey,
            $payloads
        );
    }

    /**
     * Do `/card/register` API request to Core API
     *
     * @param $cardNumber
     * @param $expMoth
     * @param $expYear
     * @return mixed
     * @throws Exception
     */
    public static function cardRegister($cardNumber, $expMoth, $expYear)
    {
        $path = "/card/register?card_number=" . $cardNumber
            . "&card_exp_month=" . $expMoth
            . "&card_exp_year=" . $expYear
            . "&client_key=" . Config::$clientKey;

        return ApiRequestor::get(
            Config::getBaseUrl() . $path,
            Config::$clientKey,
            false
        );
    }

    /**
     * Do `/token` API request to Core API
     *
     * @param $cardNumber
     * @param $expMoth
     * @param $expYear
     * @param $cvv
     * @return mixed
     * @throws Exception
     */
    public static function cardToken($cardNumber, $expMoth, $expYear, $cvv)
    {
        $path = "/token?card_number=" . $cardNumber
            . "&card_exp_month=" . $expMoth
            . "&card_exp_year=" . $expYear
            . "&card_cvv=" . $cvv
            . "&client_key=" . Config::$clientKey;

        return ApiRequestor::get(
            Config::getBaseUrl() . $path,
            Config::$clientKey,
            false
        );
    }

    /**
     * Do `/point_inquiry/<tokenId>` API request to Core API
     *
     * @param string tokenId - tokenId of credit card (more params detail refer to: https://api-docs.midtrans.com)
     * @return mixed
     * @throws Exception
     */
    public static function cardPointInquiry($tokenId)
    {
        return ApiRequestor::get(
            Config::getBaseUrl() . '/point_inquiry/' . $tokenId,
            Config::$serverKey,
            false
        );
    }
}
