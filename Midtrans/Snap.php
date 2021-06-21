<?php

namespace Midtrans;

use Exception;

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
     * Create Snap URL payment
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
     *   $paymentUrl = Snap::getSnapUrl($params);
     * ```
     *
     * @param  array $params Payment options
     * @return string Snap redirect url.
     * @throws Exception curl error or midtrans error
     */
    public static function getSnapUrl($params)
    {
        return (Snap::createTransaction($params)->redirect_url);
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

        if (isset($params['item_details'])) {
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

        return ApiRequestor::post(
            Config::getSnapBaseUrl() . '/transactions',
            Config::$serverKey,
            $params
        );
    }
}
