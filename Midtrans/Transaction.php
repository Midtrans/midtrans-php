<?php

namespace Midtrans;

/**
 * API methods to get transaction status, approve and cancel transactions
 */
class Midtrans_Transaction
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
        return Midtrans_ApiRequestor::get(
            Midtrans_Config::getBaseUrl() . '/' . $id . '/status',
            Midtrans_Config::$serverKey,
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
        return Midtrans_ApiRequestor::post(
            Midtrans_Config::getBaseUrl() . '/' . $id . '/approve',
            Midtrans_Config::$serverKey,
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
        return Midtrans_ApiRequestor::post(
            Midtrans_Config::getBaseUrl() . '/' . $id . '/cancel',
            Midtrans_Config::$serverKey,
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
        return Midtrans_ApiRequestor::post(
            Midtrans_Config::getBaseUrl() . '/' . $id . '/expire',
            Midtrans_Config::$serverKey,
            false
        );
    }
}
