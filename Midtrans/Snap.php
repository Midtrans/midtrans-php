<?php

namespace Midtrans;

/**
 * Create Snap payment page and return snap token
 */
class Snap
{
    /**
     * Create Snap payment page
     *
     * Example:
     *
     * ```php
     *   
     *   namespace Midtrans;
     * 
     *   $params = array(
     *     'transaction_details' => array(
     *       'order_id' => rand(),
     *       'gross_amount' => 10000,
     *     )
     *   );
     *   $paymentUrl = Snap::getSnapToken($params);
     * ```
     *
     * @param  array $params Payment options
     * @return string Snap token.
     * @throws Exception curl error or midtrans error
     */
    public static function getSnapToken($params)
    {
        return (Snap::createTransaction($params)->token);
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
     *   $paymentUrl = Snap::getSnapToken($params);
     * ```
     *
     * @param  array $params Payment options
     * @return object Snap response (token and redirect_url).
     * @throws Exception curl error or midtrans error
     */
    public static function createTransaction($params)
    {
        $payloads = array(
        'credit_card' => array(
            // 'enabled_payments' => array('credit_card'),
            'secure' => Config::$is3ds
        )
        );

        if (isset('item_details', $params)) {
            $gross_amount = 0;
            foreach ($params['item_details'] as $item) {
                $gross_amount += $item['quantity'] * $item['price'];
            }
            $params['transaction_details']['gross_amount'] = $gross_amount;
        }

        if (Config::$isSanitized) {
            Sanitizer::jsonRequest($params);
        }

        $params = array_replace_recursive($payloads, $params);

        $result = SnapApiRequestor::post(
            Config::getSnapBaseUrl() . '/transactions',
            Config::$serverKey,
            $params
        );

        return $result;
    }  
}
