<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans::$serverKey = '<your server key>';

// Uncomment for production environment
// Veritrans::$is_production = true;

$item_details = array(
    array(
      'id' => 'Id',
      'quantity' => 1,
      'price' => 10000,
      'name' => 'Item'
    ));

$params = array(
    'transaction_details' => array(
      'order_id' => rand(),
      'gross_amount' => 10000,
    ),
    'item_details' => $item_details,
    'vtweb' => array()
  );

try {
  // Redirect to Veritrans VTWeb page
  header('Location: ' . Veritrans_Vtweb::getRedirectionUrl($params));
}
catch (Exception $e) {
  echo $e->getMessage();
}