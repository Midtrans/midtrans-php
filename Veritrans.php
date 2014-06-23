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

require('Veritrans/Transaction.php');
require('Veritrans/ApiRequestor.php');
require('Veritrans/Notification.php');
require('Veritrans/Response.php');
require('Veritrans/Vtdirect.php');
require('Veritrans/Vtlink.php');
require('Veritrans/Vtweb.php');

class Veritrans {

	public static $server_key;
	public static $is_production = false;
	public static $sandbox_base_url = 'https://api.sandbox.veritrans.co.id/v2';
	public static $production_base_url = 'https://api.veritrans.co.id/v2';

}