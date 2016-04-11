<?php
require_once(dirname(__FILE__) . '/../../Veritrans.php');

//Set Your server key
Veritrans_Config::$serverKey = "VT-server-UJ4uPuXhwiNXwhpQx5-S76U1";

// Uncomment for production environment
// Veritrans_Config::$isProduction = true;

// Uncomment to enable sanitization
// Veritrans_Config::$isSanitized = true;

// Uncomment to enable 3D-Secure
// Veritrans_Config::$is3ds = true;

// Required
$transaction_details = array(
  'order_id' => rand(),
  'gross_amount' => 94000, // no decimal allowed for creditcard
);

// Optional
$item1_details = array(
  'id' => 'a1',
  'price' => 18000,
  'quantity' => 3,
  'name' => "Apple"
);

// Optional
$item2_details = array(
  'id' => 'a2',
  'price' => 20000,
  'quantity' => 2,
  'name' => "Orange"
);

// Optional
$item_details = array ($item1_details, $item2_details);

// Optional
$billing_address = array(
  'first_name'    => "Andri",
  'last_name'     => "Litani",
  'address'       => "Mangga 20",
  'city'          => "Jakarta",
  'postal_code'   => "16602",
  'phone'         => "081122334455",
  'country_code'  => 'IDN'
);

// Optional
$shipping_address = array(
  'first_name'    => "Obet",
  'last_name'     => "Supriadi",
  'address'       => "Manggis 90",
  'city'          => "Jakarta",
  'postal_code'   => "16601",
  'phone'         => "08113366345",
  'country_code'  => 'IDN'
);

// Optional
$customer_details = array(
  'first_name'    => "Andri",
  'last_name'     => "Litani",
  'email'         => "andri@litani.com",
  'phone'         => "081122334455",
  'billing_address'  => $billing_address,
  'shipping_address' => $shipping_address
);

$enable_payments = array('credit_card','cimb_clicks','mandiri_clickpay','echannel');

// Fill transaction details
$transaction = array(
  'enabled_payments' => $enable_payments,
  'transaction_details' => $transaction_details,
  'customer_details' => $customer_details,
  'item_details' => $item_details,
);

$response = Veritrans_Snap::getSnapToken($transaction);
error_log($response);
echo $response;
?>

<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <!-- Cross compatibility -->
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
      <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
      <title>Toko Buah</title>
      <meta name="description" content=""/>
  </head>
  <body>

    <button id="pay-button">Pay!</button>

    <div id="result-type"></div>
    <div id="result-data"></div>
    <script src="https://vtcheckout.sandbox.veritrans.co.id/snap.js"></script>
    <script type="text/javascript">
      var payButton = document.getElementById('pay-button');
      var resultType = document.getElementById('result-type');
      var resultData = document.getElementById('result-data');
      function changeResult(type,data){
        resultType.innerHTML = type;
        resultData.innerHTML = JSON.stringify(data);
      }
      payButton.onclick = function(){
        snap.pay('<?=$response?>', {
          env: 'sandbox',
          onSuccess: function(result){changeResult('success', result)},
          onPending: function(result){changeResult('pending', result)},
          onError: function(result){changeResult('error', result)}
        });
      };
    </script>
  </body>
</html>
