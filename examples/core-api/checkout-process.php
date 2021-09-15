<?php
// This is just for very basic implementation reference, in production, you should validate the incoming requests and implement your backend more securely.
// Please refer to this docs:
// https://docs.midtrans.com/en/core-api/credit-card?id=_2-sending-transaction-data-to-charge-api

namespace Midtrans;

require_once dirname(__FILE__) . '/../../Midtrans.php';

Config::$serverKey = '<your server key>';
// Uncomment for append and override notification URL
// Config::$appendNotifUrl = "https://example.com";
// Config::$overrideNotifUrl = "https://example.com";

// non-relevant function only used for demo/example purpose
printExampleWarningMessage();

// Uncomment for production environment
// Config::$isProduction = true;

// Uncomment to enable sanitization
// Config::$isSanitized = true;

// Uncomment to enable idempotency-key, more details: (http://api-docs.midtrans.com/#idempotent-requests)
// Config::$paymentIdempotencyKey = "Unique-ID";

$transaction_details = array(
    'order_id'    => time(),
    'gross_amount'  => 200000
);

// Populate items
$items = array(
    array(
        'id'       => 'item1',
        'price'    => 100000,
        'quantity' => 1,
        'name'     => 'Adidas f50'
    ),
    array(
        'id'       => 'item2',
        'price'    => 50000,
        'quantity' => 2,
        'name'     => 'Nike N90'
    ));

// Populate customer's billing address
$billing_address = array(
    'first_name'   => "Andri",
    'last_name'    => "Setiawan",
    'address'      => "Karet Belakang 15A, Setiabudi.",
    'city'         => "Jakarta",
    'postal_code'  => "51161",
    'phone'        => "081322311801",
    'country_code' => 'IDN'
  );

// Populate customer's shipping address
$shipping_address = array(
    'first_name'   => "John",
    'last_name'    => "Watson",
    'address'      => "Bakerstreet 221B.",
    'city'         => "Jakarta",
    'postal_code'  => "51162",
    'phone'        => "081322311801",
    'country_code' => 'IDN'
  );

// Populate customer's info
$customer_details = array(
    'first_name'       => "Andri",
    'last_name'        => "Setiawan",
    'email'            => "andri@setiawan.com",
    'phone'            => "081322311801",
    'billing_address'  => $billing_address,
    'shipping_address' => $shipping_address
  );

// Token ID from checkout page
$token_id = $_POST['token_id'];
$authentication = isset($_POST['secure']);
$save_token_id = isset($_POST['save_cc']);

// Transaction data to be sent
$transaction_data = array(
    'payment_type' => 'credit_card',
    'credit_card'  => array(
        'token_id'       => $token_id,
        'authentication' => $authentication,
        // 'bank'        => 'bni', // optional acquiring bank
        'save_token_id'  => $save_token_id
    ),
    'transaction_details' => $transaction_details,
    'item_details'        => $items,
    'customer_details'    => $customer_details
  );

try {
    $response = CoreApi::charge($transaction_data);
    header('Content-Type: application/json');
    echo json_encode($response);
} catch (\Exception $e) {
    echo $e->getMessage();
}

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

