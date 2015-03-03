<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans_Config::$serverKey = '<your server key>';

if (Veritrans_Config::$serverKey == '<your server key>') {
  echo "<code>";
  echo "<h4>Please set real server key from sandbox</h4>";
  echo "In file: " . __FILE__;
  echo "<br>";
  echo "<br>";
  echo htmlspecialchars('Veritrans_Config::$serverKey = \'<your server key>\';');
  die();
}

$orderId = '1404189699';

try {
  $status = Veritrans_Transaction::status($orderId);
} catch (Exception $e) {
  echo $e->getMessage();
  die();
}

var_dump($status);

// $approve = Veritrans_Transaction::approve($orderId);
// var_dump($approve);

// $cancel = Veritrans_Transaction::cancel($orderId);
// var_dump($cancel);
