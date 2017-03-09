<?php
require_once(dirname(__FILE__) . '/../../Veritrans.php');
//Set Your server key
Veritrans_Config::$serverKey = "<Set your ServerKey here>";
// Uncomment for production environment
// Veritrans_Config::$isProduction = true;
Veritrans_Config::$isSanitized = Veritrans_Config::$is3ds = true;

// Required
$transaction_details = array(
  'order_id' => rand(),
  'gross_amount' => 94000, // no decimal allowed for creditcard
);
// Optional
$item_details = array (
    array(
      'id' => 'a1',
      'price' => 94000,
      'quantity' => 1,
      'name' => "Apple"
    ),
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
// Fill transaction details
$transaction = array(
  'transaction_details' => $transaction_details,
  'customer_details' => $customer_details,
  'item_details' => $item_details,
);

$snapToken = Veritrans_Snap::getSnapToken($transaction);
echo "snapToken = ".$snapToken;
?>

<!DOCTYPE html>
<html>
  <body>
    <button id="pay-button">Pay!</button>
<!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<Set your ClientKey here>"></script>
    <script type="text/javascript">
      document.getElementById('pay-button').onclick = function(){
        // SnapToken acquired from previous step
        snap.pay('<?=$snapToken?>');
      };
    </script>
  </body>
</html>
