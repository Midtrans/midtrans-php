Veritrans-PHP
===============

[![Build Status](https://travis-ci.org/veritrans/veritrans-php.svg)](https://travis-ci.org/veritrans/veritrans-php)

Veritrans :heart: PHP!

This is the all new PHP client library for Veritrans 2.0. This is the official PHP wrapper for Veritrans Payment API. Visit [https://www.veritrans.co.id](https://www.veritrans.co.id) for more information about the product and see documentation at [http://docs.veritrans.co.id](http://docs.veritrans.co.id) for more technical details.

## Installation

### Composer Installation

If you are using [Composer](https://getcomposer.org), add this require line to your `composer.json` file:

```json
{
	"require": {
		"veritrans/veritrans-php": "dev-master"
	}
}
```

and run `composer install` on your terminal.

### Manual Instalation

If you are not using Composer, you can clone or [download](https://github.com/veritrans/veritrans-php/archive/master.zip) this repository.

## How to Use

### General Settings

#### Set Server Key

```php
Veritrans_Config::$serverKey = '<your server key>';
```

#### Set Client Key (VT-Direct)

```javascript
Veritrans.client_key = "<your client key>";
```

#### Set Environment
```php
// Development Environment (the default)
Veritrans_Config::$isProduction = false;

// Production Environment
Veritrans_Config::$isProduction = true;
```

#### Set Sanitization
```php
// Set sanitization off (default)
Veritrans_Config::$isSanitized = false;

// Set sanitization on
Veritrans_Config::$isSanitized = true;
```

### VT-Web

You can see some VT-Web examples [here](https://github.com/veritrans/veritrans-php/tree/master/examples/vt-web).

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

#### Handle Notification Callback

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

### VT-Direct

You can see some VT-Direct examples [here](https://github.com/veritrans/veritrans-php/tree/master/examples/vt-direct).

#### Checkout Page

```html
<html>

<head>
  <title>Checkout</title>
  <link rel="stylesheet" href="jquery.fancybox.css">
</head>

<body>
  <script type="text/javascript" src="https://api.sandbox.veritrans.co.id/v2/assets/js/veritrans.js"></script>
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script type="text/javascript" src="jquery.fancybox.pack.js"></script>

  <h1>Checkout</h1>
  <form action="checkout-process.php" method="POST" id="payment-form">
    <fieldset>
      <legend>Checkout</legend>
      <p>
        <label>Card Number</label>
        <input class="card-number" value="4111111111111111" size="20" type="text" autocomplete="off" />
      </p>
      <p>
        <label>Expiration (MM/YYYY)</label>
        <input class="card-expiry-month" value="12" placeholder="MM" size="2" type="text" />
        <span> / </span>
        <input class="card-expiry-year" value="2020" placeholder="YYYY" size="4" type="text" />
      </p>
      <p>
        <label>CVV</label>
        <input class="card-cvv" value="123" size="4" type="password" autocomplete="off" />
      </p>

      <p>
        <label>Save credit card</label>
        <input type="checkbox" name="save_cc" value="true">
      </p>

      <input id="token_id" name="token_id" type="hidden" />
      <button class="submit-button" type="submit">Submit Payment</button>
    </fieldset>
  </form>

  <!-- Javascript for token generation -->
  <script type="text/javascript">
    $(function () {
      // Sandbox URL
      Veritrans.url = "https://api.sandbox.veritrans.co.id/v2/token";
      // TODO: Change with your client key.
      Veritrans.client_key = "<your client key>";
      var card = function () {
        return {
          "card_number": $(".card-number").val(),
          "card_exp_month": $(".card-expiry-month").val(),
          "card_exp_year": $(".card-expiry-year").val(),
          "card_cvv": $(".card-cvv").val(),
          "secure": false,
          "gross_amount": 200000
        }
      };

      function callback(response) {
        console.log(response);
        if (response.redirect_url) {
          console.log("3D SECURE");
          // 3D Secure transaction, please open this popup
          openDialog(response.redirect_url);

        }
        else if (response.status_code == "200") {
          console.log("NOT 3-D SECURE");
          // Success 3-D Secure or success normal
          closeDialog();
          // Submit form
          $("#token_id").val(response.token_id);
          $("#payment-form").submit();
        }
        else {
          // Failed request token
          console.log(response.status_code);
          alert(response.status_message);
        }
      }

      function openDialog(url) {
        $.fancybox.open({
          href: url,
          type: "iframe",
          autoSize: false,
          width: 700,
          height: 500,
          closeBtn: false,
          modal: true
        });
      }

      function closeDialog() {
        $.fancybox.close();
      }

      $(".submit-button").click(function (event) {
        console.log("SUBMIT");
        event.preventDefault();
        $(this).attr("disabled", "disabled");
        Veritrans.token(card, callback);
        return false;
      });
    });
  </script>
</body>

</html>
```

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
    'email'            => "payment-api@veritrans.co.id",
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

#### Process Transaction

##### Get a Transaction Status

```php
$status = Veritrans_Transaction::status($orderId);
var_dump($status);
```

##### Approve a Transaction

```php
$approve = Veritrans_Transaction::approve($orderId);
var_dump($approve);
```

##### Cancel a Transaction

```php
$cancel = Veritrans_Transaction::cancel($orderId);
var_dump($cancel);
```

## Contributing

### Developing e-commerce plug-ins

There are several guides that must be taken care of when you develop new plugins.

1. __Handling currency other than IDR.__ Veritrans `v1` and `v2` currently accepts payments in Indonesian Rupiah only. As a corrolary, there is a validation on the server to check whether the item prices are in integer or not. As much as you are tempted to round-off the price, DO NOT do that! Always prepare when your system uses currencies other than IDR, convert them to IDR accordingly, and only round the price AFTER that.

2. Consider using the __auto-sanitization__ feature.
