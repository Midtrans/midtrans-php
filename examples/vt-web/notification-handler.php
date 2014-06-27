<?php

require_once(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans::$serverKey = 'f1faeaec-0889-47e2-9d3b-cb8f49f41a3d';

$notif = new Veritrans_Notification();
if ($notif->verify()) {
  error_log(print_r($notif, true));
}
else {
  error_log("Not verified!\n");
}