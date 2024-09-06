Midtrans-PHP
===============

[![PHP version](https://badge.fury.io/ph/midtrans%2Fmidtrans-php.svg)](https://badge.fury.io/ph/midtrans%2Fmidtrans-php)
[![Latest Stable Version](https://poser.pugx.org/midtrans/midtrans-php/v/stable)](https://packagist.org/packages/midtrans/midtrans-php)
[![Monthly Downloads](https://poser.pugx.org/midtrans/midtrans-php/d/monthly)](https://packagist.org/packages/midtrans/midtrans-php)
[![Total Downloads](https://poser.pugx.org/midtrans/midtrans-php/downloads)](https://packagist.org/packages/midtrans/midtrans-php)
<!-- [![Build Status](https://travis-ci.org/midtrans/midtrans-php.svg)](https://travis-ci.org/midtrans/midtrans-php) -->

[Midtrans](https://midtrans.com) :heart: PHP!

This is the Official PHP wrapper/library for Midtrans Payment API, that is compatible with Composer. Visit [https://midtrans.com](https://midtrans.com) for more information about the product and see documentation at [http://docs.midtrans.com](https://docs.midtrans.com) for more technical details.
Starting version 2.6, this library now supports Snap-bi. You can go to this [docs](https://docs.midtrans.com/reference/core-api-snap-open-api-overview) to learn more about Snap-bi.
## 1. Installation

### 1.a Composer Installation

If you are using [Composer](https://getcomposer.org), you can install via composer CLI:

```
composer require midtrans/midtrans-php
```

**or**

add this require line to your `composer.json` file:

```json
{
    "require": {
        "midtrans/midtrans-php": "2.*"
    }
}
```

and run `composer install` on your terminal.

> **Note:** If you are using Laravel framework, in [some](https://laracasts.com/discuss/channels/general-discussion/using-non-laravel-composer-package-with-laravel?page=1#reply=461608) [case](https://stackoverflow.com/a/23675376) you also need to run `composer dumpautoload`

> `/Midtrans` will then be available (auto loaded) as Object in your Laravel project.

### 1.b Manual Instalation

If you are not using Composer, you can clone or [download](https://github.com/midtrans/midtrans-php/archive/master.zip) this repository.

Then you should require/autoload `Midtrans.php` file on your code.

```php
require_once dirname(__FILE__) . '/pathofproject/Midtrans.php';

// my code goes here
```

## 2. How to Use

### 2.1 General Settings

```php
// Set your Merchant Server Key
\Midtrans\Config::$serverKey = '<your server key>';
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
\Midtrans\Config::$isProduction = false;
// Set sanitization on (default)
\Midtrans\Config::$isSanitized = true;
// Set 3DS transaction for credit card to true
\Midtrans\Config::$is3ds = true;
```

#### Override Notification URL

You can opt to change or add custom notification urls on every transaction. It can be achieved by adding additional HTTP headers into charge request.

```php
// Add new notification url(s) alongside the settings on Midtrans Dashboard Portal (MAP)
Config::$appendNotifUrl = "https://example.com/test1,https://example.com/test2";
// Use new notification url(s) disregarding the settings on Midtrans Dashboard Portal (MAP)
Config::$overrideNotifUrl = "https://example.com/test1";
```

[More details](https://api-docs.midtrans.com/#override-notification-url)

> **Note:** When both `appendNotifUrl` and `overrideNotifUrl` are used together then only `overrideNotifUrl` will be used.

> Both header can only receive up to maximum of **3 urls**.

#### Idempotency-Key
You can opt to add idempotency key on charge transaction. It can be achieved by adding additional HTTP headers into charge request. 
Is a unique value that is put on header on API request. Midtrans API accept Idempotency-Key on header to safely handle retry request 
without performing the same operation twice. This is helpful for cases where merchant didn't receive the response because of network issue or other unexpected error.

```php
Config::$paymentIdempotencyKey = "Unique-ID";
```

[More details](http://api-docs.midtrans.com/#idempotent-requests)

### 2.2 Choose Product/Method

We have [3 different products](https://docs.midtrans.com/en/welcome/index.html) of payment that you can use:
- [Snap](#22a-snap) - Customizable payment popup will appear on **your web/app** (no redirection). [doc ref](https://snap-docs.midtrans.com/)
- [Snap Redirect](#22b-snap-redirect) - Customer need to be redirected to payment url **hosted by midtrans**. [doc ref](https://snap-docs.midtrans.com/)
- [Core API (VT-Direct)](#22c-core-api-vt-direct) - Basic backend implementation, you can customize the frontend embedded on **your web/app** as you like (no redirection). [doc ref](https://api-docs.midtrans.com/)

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

$snapToken = \Midtrans\Snap::getSnapToken($params);
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

### 2.2.b Snap Redirect

You can see some Snap Redirect examples [here](examples/snap-redirect).

#### Get Redirection URL of a Payment Page

```php
$params = array(
    'transaction_details' => array(
        'order_id' => rand(),
        'gross_amount' => 10000,
    )
);

try {
  // Get Snap Payment Page URL
  $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;
  
  // Redirect to Snap Payment Page
  header('Location: ' . $paymentUrl);
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
MidtransNew3ds.clientKey = "<your client key>";
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
    )
);

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
        'authentication'=> true,
//        'bank'          => 'bni', // optional to set acquiring bank
//        'save_token_id' => true   // optional for one/two clicks feature
    ),
    'transaction_details' => $transaction_details,
    'item_details'        => $items,
    'customer_details'    => $customer_details
);
```

##### 5. Charge

```php
$response = \Midtrans\CoreApi::charge($transaction_data);
```


##### 6. Credit Card 3DS Authentication

The credit card charge result may contains `redirect_url` for 3DS authentication. 3DS Authentication should be handled on Frontend please refer to [API docs](https://api-docs.midtrans.com/#card-features-3d-secure)

For full example on Credit Card 3DS transaction refer to:
- [Core API examples](/examples/core-api/)

##### 7. Handle Transaction Status

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
#### 8. Implement Notification Handler
[Refer to this section](#23-handle-http-notification)


### 2.3 Handle HTTP Notification

Create separated web endpoint (notification url) to receive HTTP POST notification callback/webhook. 
HTTP notification will be sent whenever transaction status is changed.
Example also available [here](examples/notification-handler.php)

```php
$notif = new \Midtrans\Notification();

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
```

### 2.4 Process Transaction

#### Get Transaction Status

```php
$status = \Midtrans\Transaction::status($orderId);
var_dump($status);
```

#### Approve Transaction
If transaction fraud_status == [CHALLENGE](https://support.midtrans.com/hc/en-us/articles/202710750-What-does-CHALLENGE-status-mean-What-should-I-do-if-there-is-a-CHALLENGE-transaction-), you can approve the transaction from Merchant Dashboard, or API :

```php
$approve = \Midtrans\Transaction::approve($orderId);
var_dump($approve);
```

#### Cancel Transaction
You can Cancel transaction with `fraud_status == CHALLENGE`, or credit card transaction with `transaction_status == CAPTURE` (before it become SETTLEMENT)
```php
$cancel = \Midtrans\Transaction::cancel($orderId);
var_dump($cancel);
```

#### Expire Transaction
You can Expire transaction with `transaction_status == PENDING` (before it become SETTLEMENT or EXPIRE)
```php
$cancel = \Midtrans\Transaction::cancel($orderId);
var_dump($cancel);
```

#### Refund Transaction
Refund a transaction (not all payment channel allow refund via API)
You can Refund transaction with `transaction_status == settlement`
```php
$params = array(
    'refund_key' => 'order1-ref1',
    'amount' => 10000,
    'reason' => 'Item out of stock'
);
$refund = \Midtrans\Transaction::refund($orderId, $params);
var_dump($refund);
```

#### Direct Refund Transaction
Refund a transaction via Direct Refund API
You can Refund transaction with `transaction_status == settlement`
```php
$params = array(
    'refund_key' => 'order1-ref1',
    'amount' => 10000,
    'reason' => 'Item out of stock'
);
$direct_refund = \Midtrans\Transaction::refundDirect($orderId, $params);
var_dump($direct_refund);
```
## 3. Snap-BI (*NEW FEATURE starting v2.6.0)

### 3.1 General Settings

```php
//These config value are based on the header stated here https://docs.midtrans.com/reference/getting-started-1
// Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
\SnapBi\Config::$isProduction = false;
// Set your client id. Merchant’s client ID that will be given by Midtrans, will be used as X-CLIENT-KEY on request’s header in B2B Access Token API.
\SnapBi\Config::$snapBiClientId = "YOUR CLIENT ID";
// Set your private key here, make sure to add \n on the private key, you can refer to the examples
\SnapBi\Config::$snapBiPrivateKey = "YOUR PRIVATE KEY";
// Set your client secret. Merchant’s secret key that will be given by Midtrans, will be used for symmetric signature generation for Transactional API’s header.
\SnapBi\Config::$snapBiClientSecret = "YOUR CLIENT SECRET";
// Set your partner id. Merchant’s partner ID that will be given by Midtrans, will be used as X-PARTNER-ID on Transactional API’s header.
\SnapBi\Config::$snapBiPartnerId = "YOUR PARTNER ID";
// Set the channel id here.
\SnapBi\Config::$snapBiChannelId = "CHANNEL ID";
```

### 3.2 Create Payment: Direct Debit (Gopay, Shopeepay, Dana)

```php
   
date_default_timezone_set('Asia/Jakarta');
$time_stamp = date("c");
$date = new DateTime($time_stamp);
$external_id = "uzi-order-testing" . uniqid();

// Add 10 minutes validity time
$date->modify('+10 minutes');

// Format the new date
$valid_until = $date->format('c');


//create direct debit request body/ payload
//you can change the payment method on the `payOptionDetails`
$debitParams = array(
    "partnerReferenceNo" => $external_id,
    "chargeToken" => "",
    "merchantId" => $merchant_id,
    "urlParam" => array(
        array(
            "url" => "https://www.google.com",
            "type" => "PAY_RETURN",
            "isDeeplink" => "Y"
        )
    ),
    "validUpTo" => $valid_until,
    "payOptionDetails" => array(
        array(
            "payMethod" => "DANA",
            "payOption" => "DANA",
            "transAmount" => array(
                "value" => "100.0",
                "currency" => "IDR"
            )
        )
    ),
    "additionalInfo" => array(
        "customerDetails" => array(
            "phone" => "081122334455",
            "firstName" => "Andri",
            "lastName" => "Litani",
            "email" => "andri@litani.com",
            "billingAddress" => array(
                "firstName" => "Andri",
                "lastName" => "Litani",
                "phone" => "081122334455",
                "address" => "billingAddress",
                "city" => "billingCity",
                "postalCode" => "12790",
                "countryCode" => "CZH"
            ),
            "shippingAddress" => array(
                "firstName" => "Andri",
                "lastName" => "Litani",
                "phone" => "081122334455",
                "address" => "shippingAddress",
                "city" => "shippingCity",
                "postalCode" => "12790",
                "countryCode" => "CZH"
            )
        ),
        "items" => array(
            array(
                "id" => "1",
                "price" => array(
                    "value" => "100.00",
                    "currency" => "IDR"
                ),
                "quantity" => 1,
                "name" => "Apple",
                "brand" => "Apple",
                "category" => "Subscription",
                "merchantName" => "amazon prime",
                "url" => "itemUrl"
            )
        ),
        "metadata" => array()
    )
);
/**
 *  Basic example
 * to change the payment method, you can change the value of the request body on the `payOptionDetails`
 * the `currency` value that we support for now is only `IDR`
 */
$snapBiResponse = SnapBi::directDebit()
    ->withBody($debitParams)
    ->createPayment($external_id);

```
### 3.3 Create Payment: VA (Bank Transfer)

```php
date_default_timezone_set('Asia/Jakarta');
$time_stamp = date("c");
$date = new DateTime($time_stamp);
$external_id = "uzi-order-testing" . uniqid();
$customerVaNo = "6280123456";

$vaParams = array(
    "partnerServiceId"=> "   70012",
    "customerNo"=> $customerVaNo,
    "virtualAccountNo"=> "   70012" . $customerVaNo,
    "virtualAccountName"=> "Jokul Doe",
    "virtualAccountEmail"=> "jokul@email.com",
    "virtualAccountPhone"=> "6281828384858",
    "trxId"=> $external_id,
    "totalAmount"=> [
        "value"=> "10000.00",
        "currency"=> "IDR"
    ],
    "additionalInfo"=> [
        "merchantId"=> $merchant_id,
        "bank"=> "mandiri",
        "flags"=> [
            "shouldRandomizeVaNumber"=> false
        ],
        "mandiri"=> [
            "billInfo1"=> "bank_name",
            "billInfo2"=> "mandiri",
            "billInfo3"=> "Name:",
            "billInfo4"=> "Budi Utomo",
            "billInfo5"=> "Class:",
            "billInfo6"=> "Computer Science",
            "billInfo7"=> "ID:",
            "billInfo8"=> "VT-12345"
        ],
        "customerDetails"=> [
            "firstName"=> "Jokul",
            "lastName"=> "Doe",
            "email"=> "jokul@email.com",
            "phone"=> "+6281828384858",
            "billingAddress"=> [
                "firstName"=> "Jukul",
                "lastName"=> "Doe",
                "address"=> "Kalibata",
                "city"=> "Jakarta",
                "postalCode"=> "12190",
                "phone"=> "+6281828384858",
                "countryCode"=> "IDN"
            ],
            "shippingAddress"=> [
                "firstName"=> "Jukul",
                "lastName"=> "Doe",
                "address"=> "Kalibata",
                "city"=> "Jakarta",
                "postalCode"=> "12190",
                "phone"=> "+6281828384858",
                "countryCode"=> "IDN"
            ]
        ],
        "customField"=> [
            "1"=> "custom-field-1",
            "2"=> "custom-field-2",
            "3"=> "custom-field-3"
        ],
        "items"=> [
            [
                "id"=> "a1",
                "price"=> [
                    "value"=> "1000.00",
                    "currency"=> "IDR"
                ],
                "quantity"=> 3,
                "name"=> "Apel",
                "brand"=> "Fuji Apple",
                "category"=> "Fruit",
                "merchantName"=> "Fruit-store"

            ],
            [
                "id"=> "a2",
                "price"=> [
                    "value"=> "1000.00",
                    "currency"=> "IDR"
                ],
                "quantity"=> 7,
                "name"=> "Apel Malang",
                "brand"=> "Fuji Apple",
                "category"=> "Fruit",
                "merchantName"=> "Fruit-store"
            ]
        ]
    ]
);

/**
 * basic implementation to create payment using va
 */
$snapBiResponse = SnapBi::va()
    ->withBody($vaParams)
    ->createPayment($external_id);
```

### 3.4 Get Transaction Status
```php
$statusByExternalId = array(
    "originalExternalId" => "uzi-order-testing66ce90ce90ee5",
    "originalPartnerReferenceNo" => "uzi-order-testing66ce90ce90ee5",
    "serviceCode" => "54",
    "additionalInfo" => array()
);

$statusByReference = array(
    "originalReferenceNo" => "A1202408280618283vcBaAmf7RID",
    "serviceCode" => "54",
    "additionalInfo" => array()
);

/**
 * Example code for getStatus using externalId
 */
$snapBiResponse = SnapBi::transaction()
    ->withBody($statusByExternalId)
    ->getStatus($external_id);

/**
 * Example code for getStatus using referenceNo
 */
$snapBiResponse = SnapBi::transaction()
    ->withBody($statusByReference)
    ->getStatus($external_id);

```

### 3.5 Cancel Transaction

```php
$cancelByReference = array(
    "originalReferenceNo" => "A120240902104935GBqSQK0gtQID"
);
        
$cancelByExternalId = array(
    "originalExternalId" => "uzi-order-testing66d5983eabc71"
);

/**
 * Basic implementation to cancel transaction using referenceNo
 */
$snapBiResponse = SnapBi::transaction()
    ->withBody($cancelByReference)
    ->cancel($external_id);

/**
 * Basic implementation to cancel transaction using externalId
 */
$snapBiResponse = SnapBi::transaction()
    ->withBody($cancelByExternalId)
    ->cancel($external_id);
```

### 3.6 Refund Transaction

```php
$refundByExternalId = array(
    "originalExternalId" => "uzi-order-testing66cec41c7f905",
    "partnerRefundNo" =>  "uzi-order-testing66cec41c7f905" . "refund-0001".rand(),
    "reason" => "some-reason",
    "additionalInfo" => array(),
    "refundAmount" => array(
        "value" => "100.00",
        "currency" => "IDR"
    ));

$refundByReference = array(
    "originalReferenceNo" => "A120240828062651Y0NQMbJkDOID",
    "reason" => "some-reason",
    "additionalInfo" => array(),
    "refundAmount" => array(
        "value" => "100.00",
        "currency" => "IDR"
    ));
/**
 * Example code for refund using externalId
 */
$snapBiResponse = SnapBi::transaction()
    ->withBody($refundByExternalId)
    ->refund($external_id);

/**
 * Example code for refund using reference no
 */
$snapBiResponse = SnapBi::transaction()
    ->withBody($refundByReference)
    ->refund($external_id);

```

### 3.7 Adding additional header / override the header

you can add or override the header value, by utilizing the `->withAccessTokenHeader` or `->withTransactionHeader`.
You can refer to this [docs](https://docs.midtrans.com/reference/core-api-snap-open-api-overview) to see the header value required by Snap-Bi , and see the default header on each payment method

```php
 /**
 * Example code for refund using additional header
 */
$snapBiResponse = SnapBi::transaction()
    ->withAccessTokenHeader([
        "debug-id"=> "va debug id",
        "X-DEVICE-ID"=>"va device id"
    ])
    ->withTransactionHeader([
        "debug-id"=> "va debug id",
        "X-DEVICE-ID"=>"va device id"
    ])
    ->withBody($refundByExternalIdArray)
    ->refund($external_id);
/**
 * Example code for using additional header on creating payment using VA
 */
$snapBiResponse = SnapBi::va()
    ->withAccessTokenHeader([
        "debug-id"=> "va debug id",
        "X-DEVICE-ID"=>"va device id"
    ])
    ->withTransactionHeader([
        "debug-id"=> "va debug id",
        "X-DEVICE-ID"=>"va device id"
    ])
    ->withBody($vaParams)
    ->createPayment($external_id);
```

### 3.8 Reusing Access Token

you can add or override the header value, by utilizing the `->withAccessToken`.

```php
/**
 * Example reusing your existing accessToken by using ->withAccessToken
 */
$snapBiResponse = SnapBi::va()
    ->withAccessToken("")
    ->withBody($vaParams)
    ->createPayment($external_id);

```

## Unit Test
### Integration Test (sandbox real transactions)
Please change server key and client key on `phpunit.xml` to your own.

### All Test
`vendor/bin/phpunit`

### Specific Test
`vendor/bin/phpunit tests/integration/CoreApiIntegrationTest.php`

## Contributing

### Developing e-commerce plug-ins

There are several guides that must be taken care of when you develop new plugins.

1. __Handling currency other than IDR.__ Midtrans `v1` and `v2` currently accepts payments in Indonesian Rupiah only. As a corrolary, there is a validation on the server to check whether the item prices are in integer or not. As much as you are tempted to round-off the price, DO NOT do that! Always prepare when your system uses currencies other than IDR, convert them to IDR accordingly, and only round the price AFTER that.

2. Consider using the __auto-sanitization__ feature.
