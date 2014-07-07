<?php

class Veritrans_Transaction {

  public static function status($id)
  {
    return Veritrans_ApiRequestor::get(
        Veritrans_Config::getBaseUrl() . '/' . $id . '/status',
        Veritrans_Config::$serverKey,
        false);
  }

  public static function approve($id)
  {
    return Veritrans_ApiRequestor::post(
        Veritrans_Config::getBaseUrl() . '/' . $id . '/approve',
        Veritrans_Config::$serverKey,
        false)->status_code;
  }

  public static function cancel($id)
  {
    return Veritrans_ApiRequestor::post(
        Veritrans_Config::getBaseUrl() . '/' . $id . '/cancel',
        Veritrans_Config::$serverKey,
        false)->status_code;
  }
}