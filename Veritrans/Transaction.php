<?php

class Veritrans_Transaction {

  public static function status($id)
  {
    return Veritrans_ApiRequestor::get(Veritrans::getBaseUrl()
        . '/' . $id . '/status', Veritrans::$serverKey, false);
  }

  public static function approve($id)
  {
    return Veritrans_ApiRequestor::post(Veritrans::getBaseUrl()
        . '/' . $id . '/approve', Veritrans::$serverKey, false)->status_code;
  }

  public static function cancel($id)
  {
    return Veritrans_ApiRequestor::post(Veritrans::getBaseUrl()
        . '/' . $id . '/cancel', Veritrans::$serverKey, false)->status_code;
  }
}