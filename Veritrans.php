<?php 

// This snippet due to the braintree_php.
if (version_compare(PHP_VERSION, '5.2.1', '<')) {
    throw new Braintree_Exception('PHP version >= 5.2.1 required');
}

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('Veritrans needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Veritrans needs the JSON PHP extension.');
}

// The singleton
require('Veritrans/Veritrans.php');

// Veritrans API Resources
require('Veritrans/Transaction.php');

// Plumbing
require('Veritrans/ApiRequestor.php');
require('Veritrans/Notification.php');
require('Veritrans/Response.php');
require('Veritrans/Vtdirect.php');
require('Veritrans/Vtlink.php');
require('Veritrans/Vtweb.php');
