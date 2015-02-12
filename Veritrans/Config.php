<?php

class Veritrans_Config {

  public static $serverKey;
  public static $clientKey;
  public static $apiVersion = 2;
  public static $isProduction = false;
  public static $is3ds = false;
  public static $isSanitized = false;

  const SANDBOX_BASE_URL = 'https://api.sandbox.veritrans.co.id/v2';
  const PRODUCTION_BASE_URL = 'https://api.veritrans.co.id/v2';

  public static function getBaseUrl()
  {
    return Veritrans_Config::$isProduction ?
        Veritrans_Config::PRODUCTION_BASE_URL : Veritrans_Config::SANDBOX_BASE_URL;
  }
}
