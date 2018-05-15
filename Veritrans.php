<?php

// Check PHP version.
if (version_compare(PHP_VERSION, '5.2.1', '<')) {
    throw new Exception('PHP version >= 5.2.1 required');
}

// Check PHP Curl & json decode capabilities.
if (!function_exists('curl_init') || !function_exists('curl_exec')) {
  throw new Exception('Veritrans needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('Veritrans needs the JSON PHP extension.');
}

// Configurations
require_once('Veritrans/Config.php');

// Veritrans API Resources
require_once('Veritrans/Transaction.php');

// Plumbing
require_once('Veritrans/ApiRequestor.php');
require_once('Veritrans/SnapApiRequestor.php');
require_once('Veritrans/Notification.php');
require_once('Veritrans/VtDirect.php');
require_once('Veritrans/VtWeb.php');
require_once('Veritrans/Snap.php');

// Sanitization
require_once('Veritrans/Sanitizer.php');
