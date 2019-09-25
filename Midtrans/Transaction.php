<?php

namespace Midtrans;

/**
 * API methods to get transaction status, approve and cancel transactions
 */
class Transaction
{

    /**
     * Retrieve transaction status
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public static function status($id)
    {
        return ApiRequestor::get(
            Config::getBaseUrl() . '/' . $id . '/status',
            Config::$serverKey,
            false
        );
    }

    /**
     * Approve challenge transaction
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return string
     */
    public static function approve($id)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/' . $id . '/approve',
            Config::$serverKey,
            false
        )->status_code;
    }

    /**
     * Cancel transaction before it's settled
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return string
     */
    public static function cancel($id)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/' . $id . '/cancel',
            Config::$serverKey,
            false
        )->status_code;
    }
  
    /**
     * Expire transaction before it's setteled
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public static function expire($id)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/' . $id . '/expire',
            Config::$serverKey,
            false
        );
    }

    /**
     * Transaction status can be updated into refund
     * if the customer decides to cancel completed/settlement payment.
     * The same refund id cannot be reused again.
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public static function refund($id)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/' . $id . '/refund',
            Config::$serverKey,
            false
        );
    }

    /**
     * Deny method can be triggered to immediately deny card payment transaction
     * in which fraud_status is challenge.
     * 
     * @param string $id Order ID or transaction ID
     * 
     * @return mixed[]
     */
    public static function deny($id)
    {
        return ApiRequestor::post(
            Config::getBaseUrl() . '/' . $id . '/deny',
            Config::$serverKey,
            false
        );
    }
}
