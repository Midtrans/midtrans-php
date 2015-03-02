<?php
/**
 * Veritrans Configuration
 */
class Veritrans_Config {

  /**
   * Your merchant's server key
   * @static
   */
  public static $serverKey;
  /**
   * Your merchant's client key
   * @static
   */
  public static $client_key;
  /**
   * This should be 2
   * @static
   */
  public static $apiVersion = 2;
  /**
   * Make it true to integrate with production
   * @static
   */
  public static $isProduction = false;
  /**
   * Enable 3D Secure by default
   * @static
   */
  public static $is3ds = false;
  /**
   * Enable request params sanitizer (validate and modify charge request params).
   * See Veritrans_Sanitizer for more details
   * @static
   */
  public static $isSanitized = false;

  const SANDBOX_BASE_URL = 'https://api.sandbox.veritrans.co.id/v2';
  const PRODUCTION_BASE_URL = 'https://api.veritrans.co.id/v2';

  /**
   * @return string Veritrans API URL, depends on $isProduction
   */
  public static function getBaseUrl()
  {
    return Veritrans_Config::$isProduction ?
        Veritrans_Config::PRODUCTION_BASE_URL : Veritrans_Config::SANDBOX_BASE_URL;
  }
}
