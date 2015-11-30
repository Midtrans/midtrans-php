<?php
/**
 * API methods to get transaction status, approve and cancel transactions
 */
class Veritrans_Transaction {

  /**
   * Retrieve transaction status
   * @param string $id Order ID or transaction ID
   * @return mixed[]
   */
  public static function status($id)
  {
    return Veritrans_ApiRequestor::get(
        Veritrans_Config::getBaseUrl() . '/' . $id . '/status',
        Veritrans_Config::$serverKey,
        false);
  }

  /**
   * Approve challenge transaction
   * @param string $id Order ID or transaction ID
   * @return string
   */
  public static function approve($id)
  {
    return Veritrans_ApiRequestor::post(
        Veritrans_Config::getBaseUrl() . '/' . $id . '/approve',
        Veritrans_Config::$serverKey,
        false)->status_code;
  }

  /**
   * Cancel transaction before it's settled
   * @param string $id Order ID or transaction ID
   * @return string
   */
  public static function cancel($id)
  {
    return Veritrans_ApiRequestor::post(
        Veritrans_Config::getBaseUrl() . '/' . $id . '/cancel',
        Veritrans_Config::$serverKey,
        false)->status_code;
  }
  
  /**
   * Expire transaction before it's setteled
   * @param string $id Order ID or transaction ID
   * @return mixed[]
   */
  public static function expire($id)
  {
    return Veritrans_ApiRequestor::post(
        Veritrans_Config::getBaseUrl() . '/' . $id . '/expire',
        Veritrans_Config::$serverKey,
        false);
  }
}
