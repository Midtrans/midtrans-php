<?php

class Veritrans_VtDirect {

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

    if (Veritrans_Config::$isSanitized) {
      Veritrans_Sanitizer::jsonRequest($payloads);
    }

    $result = Veritrans_ApiRequestor::post(
        Veritrans_Config::getBaseUrl() . '/charge',
        Veritrans_Config::$serverKey,
        $payloads);

    return $result;
  }
  
  public static function capture($param)
  {
	$payloads = array(
		'transaction_id' => $param,
	);	
	
	$result = Veritrans_ApiRequestor::post(
        Veritrans_Config::getBaseUrl() . '/capture',
        Veritrans_Config::$serverKey,
        $payloads);

    return $result;
  }
}
