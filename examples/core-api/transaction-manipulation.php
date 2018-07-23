<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans_Config::$serverKey = '<your server key>';

if (strpos(Veritrans_Config::$serverKey,'your ') != false ) {
  echo "<code>";
  echo "<h4>Please set your server key from sandbox</h4>";
  echo "In file: " . __FILE__;
  echo "<br>";
  echo "<br>";
  echo htmlspecialchars('Veritrans_Config::$serverKey = \'<your server key>\';');
  die();
}

$orderId = '<your order id / transaction id>';

// Get transaction status to Midtrans API
try {
  $status = Veritrans_Transaction::status($orderId);
} catch (Exception $e) {
  echo $e->getMessage();
  die();
}

var_dump($status);

// Approve a transaction that is in Challenge status
// $approve = Veritrans_Transaction::approve($orderId);
// var_dump($approve);

// Cancel a transaction
// $cancel = Veritrans_Transaction::cancel($orderId);
// var_dump($cancel);

// Expire a transaction
// $expire = Veritrans_Transaction::expire($orderId);
// var_dump($expire);
