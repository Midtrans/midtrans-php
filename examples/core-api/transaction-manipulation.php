<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.

namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php';
// Set Your server key
// can find in Merchant Portal -> Settings -> Access keys
Config::$serverKey = '<your server key>';

// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

$orderId = '<your order id / transaction id>';
// Get transaction status to Midtrans API
$status = '';
try {
    $status = Transaction::status($orderId);
} catch (\Exception $e) {
    echo $e->getMessage();
    die();
}

echo '<pre>';
echo json_encode($status);

function printExampleWarningMessage() {
    if (strpos(Config::$serverKey, 'your ') != false ) {
        echo "<code>";
        echo "<h4>Please set your server key from sandbox</h4>";
        echo "In file: " . __FILE__;
        echo "<br>";
        echo "<br>";
        echo htmlspecialchars('Config::$serverKey = \'<your server key>\';');
        die();
    } 
}


// Approve a transaction that is in Challenge status
// $approve = Transaction::approve($orderId);
// var_dump($approve);

// Cancel a transaction
// $cancel = Transaction::cancel($orderId);
// var_dump($cancel);

// Expire a transaction
// $expire = Transaction::expire($orderId);
// var_dump($expire);
