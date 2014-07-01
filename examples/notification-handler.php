<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans::$serverKey = '<your server key>';

$notif = new Veritrans_Notification();
if ($notif->verified()) {
  error_log("Status order ID $notif->order_id: $notif->status_code");

  // Success
  if ($notif->status_code == '200') {
    // TODO Update merchant's database (i.e. update status order)
  }
  // Pending
  else if ($notif->status_code == '201') {
    // TODO Update merchant's database (i.e. update status order)
  }
  // Denied
  else if ($notif->status_code == '202') {
    // TODO Update merchant's database (i.e. update status order)
  }
  // Error
  else {
    // TODO Update merchant's database (i.e. update status order)
  }
}