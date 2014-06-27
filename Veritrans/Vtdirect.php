<?php

class Veritrans_VtDirect {

  public static function charge($params)
  {
    if (array_key_exists('item_details', $params)) {
      $gross_amount = 0;
      foreach ($params['item_details'] as $item) {
        $gross_amount += $item['quantity'] * $item['price'];
      }
    }

    $payloads = array(
        'payment_type' => 'credit_card',
        'transaction_details' => array('gross_amount' => $gross_amount)
      );

    $payloads = array_replace_recursive($payloads, $params);

    $result = Veritrans_ApiRequestor::post(Veritrans::getBaseUrl() . '/charge',
        Veritrans::$serverKey, $payloads);

    return $result;
  }
}