<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans_Config::$serverKey = '<your server key>';

$orderId = '1404189699';

$status = Veritrans_Transaction::status($orderId);
var_dump($status);

// $approve = Veritrans_Transaction::approve($orderId);
// var_dump($approve);

// $cancel = Veritrans_Transaction::cancel($orderId);
// var_dump($cancel);
