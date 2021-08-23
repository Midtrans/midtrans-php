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
            Config::getBaseUrl() . '/v2/charge',
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
            Config::getBaseUrl() . '/v2/capture',
            Config::$serverKey,
            $payloads
        );
    }

    /**
     * Do `/v2/card/register` API request to Core API
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
            Config::getBaseUrl() . "/v2" . $path,
            Config::$clientKey,
            false
        );
    }

    /**
     * Do `/v2/token` API request to Core API
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
            Config::getBaseUrl() . "/v2" . $path,
            Config::$clientKey,
            false
        );
    }

    /**
     * Do `/v2/point_inquiry/<tokenId>` API request to Core API
     *
     * @param string tokenId - tokenId of credit card (more params detail refer to: https://api-docs.midtrans.com)
     * @return mixed
     * @throws Exception
     */
    public static function cardPointInquiry($tokenId)
    {
        return ApiRequestor::get(
            Config::getBaseUrl() . '/v2/point_inquiry/' . $tokenId,
            Config::$serverKey,
            false
        );
    }

    /**
     * Create `/v2/pay/account` API request to Core API
     *
     * @param string create pay account request (more params detail refer to: https://api-docs.midtrans.com/#create-pay-account)
     * @return mixed
     * @throws Exception
     */
    public static function linkPaymentAccount($param)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/v2/pay/account',
            Config::$serverKey,
            $param
        );
    }

    /**
     * Do `/v2/pay/account/<accountId>` API request to Core API
     *
     * @param string accountId (more params detail refer to: https://api-docs.midtrans.com/#get-pay-account)
     * @return mixed
     * @throws Exception
     */
    public static function getPaymentAccount($accountId)
    {
        return ApiRequestor::get(
            Config::getBaseUrl() . '/v2/pay/account/' . $accountId,
            Config::$serverKey,
            false
        );
    }

    /**
     * Unbind `/v2/pay/account/<accountId>/unbind` API request to Core API
     *
     * @param string accountId (more params detail refer to: https://api-docs.midtrans.com/#unbind-pay-account)
     * @return mixed
     * @throws Exception
     */
    public static function unlinkPaymentAccount($accountId)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/v2/pay/account/' . $accountId . '/unbind',
            Config::$serverKey,
            false
        );
    }

    /**
     * Create `/v1/subscription` API request to Core API
     *
     * @param string create subscription request (more params detail refer to: https://api-docs.midtrans.com/#create-subscription)
     * @return mixed
     * @throws Exception
     */
    public static function createSubscription($param)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/v1/subscriptions',
            Config::$serverKey,
            $param
        );
    }

    /**
     * Do `/v1/subscription/<subscription_id>` API request to Core API
     *
     * @param string get subscription request (more params detail refer to: https://api-docs.midtrans.com/#get-subscription)
     * @return mixed
     * @throws Exception
     */
    public static function getSubscription($SubscriptionId)
    {
        return ApiRequestor::get(
            Config::getBaseUrl() . '/v1/subscriptions/' . $SubscriptionId,
            Config::$serverKey,
            false
        );
    }

    /**
     * Do disable `/v1/subscription/<subscription_id>/disable` API request to Core API
     *
     * @param string disable subscription request (more params detail refer to: https://api-docs.midtrans.com/#disable-subscription)
     * @return mixed
     * @throws Exception
     */
    public static function disableSubscription($SubscriptionId)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/v1/subscriptions/' . $SubscriptionId . '/disable',
            Config::$serverKey,
            false
        );
    }

    /**
     * Do enable `/v1/subscription/<subscription_id>/enable` API request to Core API
     *
     * @param string enable subscription request (more params detail refer to: https://api-docs.midtrans.com/#enable-subscription)
     * @return mixed
     * @throws Exception
     */
    public static function enableSubscription($SubscriptionId)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/v1/subscriptions/' . $SubscriptionId . '/enable',
            Config::$serverKey,
            false
        );
    }

    /**
     * Do update subscription `/v1/subscription/<subscription_id>` API request to Core API
     *
     * @param string update subscription request (more params detail refer to: https://api-docs.midtrans.com/#update-subscription)
     * @return mixed
     * @throws Exception
     */
    public static function updateSubscription($SubscriptionId, $param)
    {
        return ApiRequestor::patch(
            Config::getBaseUrl() . '/v1/subscriptions/' . $SubscriptionId,
            Config::$serverKey,
            $param
        );
    }
}
