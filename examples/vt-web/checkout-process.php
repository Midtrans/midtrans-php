<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans::$serverKey = '<your server key>';

// Uncomment for production environment
// Veritrans::$is_production = true;

$params = array(
    'transaction_details' => array(
      'order_id' => rand(),
      'gross_amount' => 10000,
    )
  );

try {
  // Redirect to Veritrans VTWeb page
  header('Location: ' . Veritrans_Vtweb::getRedirectionUrl($params));
}
catch (Exception $e) {
  echo $e->getMessage();
}