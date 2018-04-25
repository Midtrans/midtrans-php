Veritrans-PHP
===============

[![Build Status](https://travis-ci.org/veritrans/veritrans-php.svg)](https://travis-ci.org/veritrans/veritrans-php)

Veritrans is now :arrow_right: [Midtrans](https://midtrans.com)

Midtrans :heart: PHP!

This is the Official PHP wrapper/library for Midtrans Payment API. Visit [https://midtrans.com](https://midtrans.com) for more information about the product and see documentation at [http://docs.midtrans.com](http://docs.midtrans) for more technical details.

## 1. Installation

### 1.a Composer Installation

If you are using [Composer](https://getcomposer.org), add this require line to your `composer.json` file:

```json
{
	"require": {
		"veritrans/veritrans-php": "dev-master"
	}
}
```

and run `composer install` on your terminal.

### 1.b Manual Instalation

If you are not using Composer, you can clone or [download](https://github.com/veritrans/veritrans-php/archive/master.zip) this repository.

## 2. How to Use

### 2.1 General Settings

```php
// Set your Merchant Server Key
Veritrans_Config::$serverKey = '<your server key>';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
Veritrans_Config::$isProduction = false;
// Set sanitization on (default)
Veritrans_Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
Veritrans_Config::$is3ds = true
```

### 2.2 Choose Product/Method

We have [3 different products](https://docs.midtrans.com/en/welcome/index.html) of payment that you can use:
- [Snap](https://snap-docs.midtrans.com/) - Customizable payment popup will appear on **your web/app** (no redirection)
- [VT-Web](https://docs.midtrans.com/en/vtweb/integration.html) - Customer need to be redirected to payment url **hosted by midtrans**
- [Core API (VT-Direct)](https://api-docs.midtrans.com/) - Basic backend implementation, you can customize the frontend embedded on **your web/app** as you like (no redirection)

Choose one that you think best for your unique needs.

### 2.2.a Snap

You can see Snap example [here](examples/snap).

#### Get Snap Token

```php
$params = array(
    'transaction_details' => array(
      'order_id' => rand(),
      'gross_amount' => 10000,
    )
  );

$snapToken = Veritrans_Snap::getSnapToken($params);
```

#### Initialize Snap JS when customer click pay button

```html
<html>
  <body>
    <button id="pay-button">Pay!</button>
    <pre><div id="result-json">JSON result will appear here after payment:<br></div></pre> 

<!-- TODO: Remove ".sandbox" from script src URL for production environment. Also input your client key in "data-client-key" -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<Set your ClientKey here>"></script>
    <script type="text/javascript">
      document.getElementById('pay-button').onclick = function(){
        // SnapToken acquired from previous step
        snap.pay('<?=$snapToken?>', {
          // Optional
          onSuccess: function(result){
            /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
          },
          // Optional
          onPending: function(result){
            /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
          },
          // Optional
          onError: function(result){
            /* You may add your own js here, this is just example */ document.getElementById('result-json').innerHTML += JSON.stringify(result, null, 2);
          }
        });
      };
    </script>
  </body>
</html>
```

#### Implement Notification Handler
[Refer to this section](#23-handle-http-notification)

### 2.2.b VT-Web

You can see some VT-Web examples [here](examples/vt-web).

#### Get Redirection URL of a Charge

```php
$params = array(
    'transaction_details' => array(
      'order_id' => rand(),
      'gross_amount' => 10000,
    ),
    'vtweb' => array()
  );

try {
  // Redirect to Veritrans VTWeb page
  header('Location: ' . Veritrans_Vtweb::getRedirectionUrl($params));
}
catch (Exception $e) {
  echo $e->getMessage();
}
```
#### Implement Notification Handler
[Refer to this section](#23-handle-http-notification)

### 2.2.c Core API (VT-Direct)

You can see some Core API examples [here](examples/core-api).

#### Set Client Key

```javascript
Veritrans.client_key = "<your client key>";
```

#### Checkout Page

Please refer to [this file](examples/core-api/checkout.php)

#### Checkout Process

##### 1. Create Transaction Details

```php
$transaction_details = array(
  'order_id'    => time(),
  'gross_amount'  => 200000
);
```

##### 2. Create Item Details, Billing Address, Shipping Address, and Customer Details (Optional)

```php
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
    'email'            => "test@test.com",
    'phone'            => "081322311801",
    'billing_address'  => $billing_address,
    'shipping_address' => $shipping_address
  );
```

##### 3. Get Token ID from Checkout Page

```php
// Token ID from checkout page
$token_id = $_POST['token_id'];
```

##### 4. Create Transaction Data

```php
// Transaction data to be sent
$transaction_data = array(
    'payment_type' => 'credit_card',
    'credit_card'  => array(
      'token_id'      => $token_id,
      'bank'          => 'bni',
      'save_token_id' => isset($_POST['save_cc'])
    ),
    'transaction_details' => $transaction_details,
    'item_details'        => $items,
    'customer_details'    => $customer_details
  );
```

##### 5. Charge

```php
$response = Veritrans_VtDirect::charge($transaction_data);
```

##### 6. Handle Transaction Status

```php
// Success
if($response->transaction_status == 'capture') {
  echo "<p>Transaksi berhasil.</p>";
  echo "<p>Status transaksi untuk order id $response->order_id: " .
      "$response->transaction_status</p>";

  echo "<h3>Detail transaksi:</h3>";
  echo "<pre>";
  var_dump($response);
  echo "</pre>";
}
// Deny
else if($response->transaction_status == 'deny') {
  echo "<p>Transaksi ditolak.</p>";
  echo "<p>Status transaksi untuk order id .$response->order_id: " .
      "$response->transaction_status</p>";

  echo "<h3>Detail transaksi:</h3>";
  echo "<pre>";
  var_dump($response);
  echo "</pre>";
}
// Challenge
else if($response->transaction_status == 'challenge') {
  echo "<p>Transaksi challenge.</p>";
  echo "<p>Status transaksi untuk order id $response->order_id: " .
      "$response->transaction_status</p>";

  echo "<h3>Detail transaksi:</h3>";
  echo "<pre>";
  var_dump($response);
  echo "</pre>";
}
// Error
else {
  echo "<p>Terjadi kesalahan pada data transaksi yang dikirim.</p>";
  echo "<p>Status message: [$response->status_code] " .
      "$response->status_message</p>";

  echo "<pre>";
  var_dump($response);
  echo "</pre>";
}
```
#### 7. Implement Notification Handler
[Refer to this section](#23-handle-http-notification)


### 2.3 Handle HTTP Notification

Create separated web endpoint (notification url) to receive HTTP POST notification callback/webhook. 
HTTP notification will be sent whenever transaction status is changed.
Example also available [here](examples/notification-handler.php)

```php
$notif = new Veritrans_Notification();

$transaction = $notif->transaction_status;
$fraud = $notif->fraud_status;

error_log("Order ID $notif->order_id: "."transaction status = $transaction, fraud staus = $fraud");

  if ($transaction == 'capture') {
    if ($fraud == 'challenge') {
      // TODO Set payment status in merchant's database to 'challenge'
    }
    else if ($fraud == 'accept') {
      // TODO Set payment status in merchant's database to 'success'
    }
  }
  else if ($transaction == 'cancel') {
    if ($fraud == 'challenge') {
      // TODO Set payment status in merchant's database to 'failure'
    }
    else if ($fraud == 'accept') {
      // TODO Set payment status in merchant's database to 'failure'
    }
  }
  else if ($transaction == 'deny') {
      // TODO Set payment status in merchant's database to 'failure'
  }
}
```

### 2.4 Process Transaction

#### Get Transaction Status

```php
$status = Veritrans_Transaction::status($orderId);
var_dump($status);
```

#### Approve Transaction
If transaction fraud_status == [CHALLENGE](https://support.midtrans.com/hc/en-us/articles/202710750-What-does-CHALLENGE-status-mean-What-should-I-do-if-there-is-a-CHALLENGE-transaction-), you can approve the transaction from Merchant Dashboard, or API :

```php
$approve = Veritrans_Transaction::approve($orderId);
var_dump($approve);
```

#### Cancel Transaction
You can Cancel transaction with `fraud_status == CHALLENGE`, or credit card transaction with `transaction_status == CAPTURE` (before it become SETTLEMENT)
```php
$cancel = Veritrans_Transaction::cancel($orderId);
var_dump($cancel);
```

#### Expire Transaction
You can Expire transaction with `transaction_status == PENDING` (before it become SETTLEMENT or EXPIRE)
```php
$cancel = Veritrans_Transaction::cancel($orderId);
var_dump($cancel);
```

## Contributing

### Developing e-commerce plug-ins

There are several guides that must be taken care of when you develop new plugins.

1. __Handling currency other than IDR.__ Veritrans `v1` and `v2` currently accepts payments in Indonesian Rupiah only. As a corrolary, there is a validation on the server to check whether the item prices are in integer or not. As much as you are tempted to round-off the price, DO NOT do that! Always prepare when your system uses currencies other than IDR, convert them to IDR accordingly, and only round the price AFTER that.

2. Consider using the __auto-sanitization__ feature.
