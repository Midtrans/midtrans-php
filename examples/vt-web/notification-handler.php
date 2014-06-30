<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans::$serverKey = '<your server key>';

$notif = new Veritrans_Notification();
if ($notif->verify()) {
  error_log(print_r($notif, true));
}
else {
  error_log("Not verified!\n");
}