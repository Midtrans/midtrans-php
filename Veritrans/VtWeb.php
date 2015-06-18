<?php
/**
 * Create VtWeb transaction and return redirect url
 *
 */
class Veritrans_VtWeb {

  /**
   * Create VT-Web transaction
   *
   * Example:
   *
   * ```php
   *   $params = array(
   *     'transaction_details' => array(
   *       'order_id' => rand(),
   *       'gross_amount' => 10000,
   *     )
   *   );
   *   $paymentUrl = Veritrans_Vtweb::getRedirectionUrl($params);
   *   header('Location: ' . $paymentUrl);
   * ```
   *
   * @param array $params Payment options
   * @return string Redirect URL to VT-Web payment page.
   * @throws Exception curl error or veritrans error
   */
  public static function getRedirectionUrl($params)
  {
    $payloads = array(
      'payment_type' => 'vtweb',
      'vtweb' => array(
        // 'enabled_payments' => array('credit_card'),
        'credit_card_3d_secure' => Veritrans_Config::$is3ds
      )
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

    return $result->redirect_url;
  }
}
