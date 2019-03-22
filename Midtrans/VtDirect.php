<?php

namespace Midtrans;

/**
 * Provide charge and capture functions for VT-Direct
 */
class Midtrans_VtDirect {

  /**
   * Create VT-Direct transaction.
   *
   * @param mixed[] $params Transaction options
   */
  public static function charge($params)
  {
    $payloads = array(
        'payment_type' => 'credit_card'
    );

    if (array_key_exists('item_details', $params)) {
      $gross_amount = 0;
      foreach ($params['item_details'] as $item) {
        $gross_amount += $item['quantity'] * $item['price'];
      }
      $payloads['transaction_details']['gross_amount'] = $gross_amount;
    }

    $payloads = array_replace_recursive($payloads, $params);

    if (Midtrans_Config::$isSanitized) {
      Midtrans_Sanitizer::jsonRequest($payloads);
    }

    $result = Midtrans_ApiRequestor::post(
        Midtrans_Config::getBaseUrl() . '/charge',
        Midtrans_Config::$serverKey,
        $payloads);

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

    $result = Midtrans_ApiRequestor::post(
          Midtrans_Config::getBaseUrl() . '/capture',
          Midtrans_Config::$serverKey,
          $payloads);

    return $result;
  }
}
