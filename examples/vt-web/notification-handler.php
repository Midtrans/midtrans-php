<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans::$serverKey = '<your server key>';

$notif = new Veritrans_Notification();
if ($notif->verified()) {
  error_log("Status order ID $result->order_id: $result->status_code");

  // Success
  if ($result->status_code == '200') {
    // TODO Update merchant's database (i.e. update status order)
  }
  // Pending
  else if ($result->status_code == '201') {
    // TODO Update merchant's database (i.e. update status order)
  }
  // Denied
  else if ($result->status_code == '202') {
    // TODO Update merchant's database (i.e. update status order)
  }
  // Error
  else {
    // TODO Update merchant's database (i.e. update status order)
  }
}