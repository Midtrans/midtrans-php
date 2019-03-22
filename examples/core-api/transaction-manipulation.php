<?php

require_once(dirname(__FILE__) . '/../../Midtrans.php');

Midtrans_Config::$serverKey = '<your server key>';

if (strpos(Midtrans_Config::$serverKey,'your ') != false ) {
  echo "<code>";
  echo "<h4>Please set your server key from sandbox</h4>";
  echo "In file: " . __FILE__;
  echo "<br>";
  echo "<br>";
  echo htmlspecialchars('Midtrans_Config::$serverKey = \'<your server key>\';');
  die();
}

$orderId = '<your order id / transaction id>';

// Get transaction status to Midtrans API
try {
  $status = Midtrans_Transaction::status($orderId);
} catch (Exception $e) {
  echo $e->getMessage();
  die();
}

var_dump($status);

// Approve a transaction that is in Challenge status
// $approve = Midtrans_Transaction::approve($orderId);
// var_dump($approve);

// Cancel a transaction
// $cancel = Midtrans_Transaction::cancel($orderId);
// var_dump($cancel);

// Expire a transaction
// $expire = Midtrans_Transaction::expire($orderId);
// var_dump($expire);
