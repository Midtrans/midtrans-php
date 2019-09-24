<?php

namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php';

Config::$serverKey = '<your server key>';

if (strpos(Config::$serverKey, 'your ') != false ) {
    echo "<code>";
    echo "<h4>Please set your server key from sandbox</h4>";
    echo "In file: " . __FILE__;
    echo "<br>";
    echo "<br>";
    echo htmlspecialchars('Config::$serverKey = \'<your server key>\';');
    die();
}

$orderId = '<your order id / transaction id>';

// Get transaction status to Midtrans API
try {
    $status = Transaction::status($orderId);
} catch (Exception $e) {
    echo $e->getMessage();
    die();
}

echo '<pre>';
echo json_encode($status);

// Approve a transaction that is in Challenge status
// $approve = Transaction::approve($orderId);
// var_dump($approve);

// Cancel a transaction
// $cancel = Transaction::cancel($orderId);
// var_dump($cancel);

// Expire a transaction
// $expire = Transaction::expire($orderId);
// var_dump($expire);
