<?php

class Veritrans_Vtweb {

  public static function getRedirectionUrl($params)
  {
    if (array_key_exists('item_details', $params)) {
      $gross_amount = 0;
      foreach ($params['item_details'] as $item) {
        $gross_amount += $item['quantity'] * $item['price'];
      }
    }

    $payloads = array(
      'payment_type' => 'vtweb',
      'vtweb' => array(
        'enabled_payments' => array('credit_card'),
        'credit_card_3d_secure' => Veritrans::$is3ds
      ),
      'transaction_details' => array(
        'gross_amount' => $gross_amount
      )
    );

    $payloads = array_replace_recursive($payloads, $params);

    $result = Veritrans_ApiRequestor::post(Veritrans::getBaseUrl() . '/charge',
        Veritrans::$serverKey, $payloads);

    return $result->redirect_url;
  }
}