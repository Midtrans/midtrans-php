<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans_Config::$serverKey = '<your server key>';

// Uncomment for production environment
// Veritrans_Config::$isProduction = true;

// Uncomment to enable sanitization
// Veritrans_Config::$isSanitized = true;

// Uncomment to enable 3D-Secure
// Veritrans_Config::$is3ds = true;

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
