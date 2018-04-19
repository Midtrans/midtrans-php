<?php
/**
 * Create Snap payment page and return snap token
 *
 */
class Veritrans_Snap {

  /**
   * Create Snap payment page
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
   *   $paymentUrl = Veritrans_Snap::getSnapToken($params);
   * ```
   *
   * @param array $params Payment options
   * @return string Snap token.
   * @throws Exception curl error or veritrans error
   */
  public static function getSnapToken($params) {
    return (Veritrans_Snap::createTransaction($params)->token);
  }

  /**
   * Create Snap payment page, with this version returning full API response
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
   *   $paymentUrl = Veritrans_Snap::getSnapToken($params);
   * ```
   *
   * @param array $params Payment options
   * @return object Snap response (token and redirect_url).
   * @throws Exception curl error or veritrans error
   */
  public static function createTransaction($params)
  {
    $payloads = array(
      'credit_card' => array(
        // 'enabled_payments' => array('credit_card'),
        'secure' => Veritrans_Config::$is3ds
      )
    );

    if (array_key_exists('item_details', $params)) {
      $gross_amount = 0;
      foreach ($params['item_details'] as $item) {
        $gross_amount += $item['quantity'] * $item['price'];
      }
      $params['transaction_details']['gross_amount'] = $gross_amount;
    }

    if (Veritrans_Config::$isSanitized) {
      Veritrans_Sanitizer::jsonRequest($params);
    }

    $params = array_replace_recursive($payloads, $params);

    $result = Veritrans_SnapApiRequestor::post(
        Veritrans_Config::getSnapBaseUrl() . '/transactions',
        Veritrans_Config::$serverKey,
        $params);

    return $result;
  }  
}
