<?php

require_once(dirname(__FILE__) . '/../Veritrans.php');

define('TEST_CAPTURE_JSON', '{
    "status_code" : "200",
    "status_message" : "Veritrans payment notification",
    "transaction_id" : "826acc53-14e0-4ae7-95e2-845bf0311579",
    "order_id" : "2014040745",
    "payment_type" : "credit_card",
    "transaction_time" : "2014-04-07 16:22:36",
    "transaction_status" : "capture",
    "fraud_status" : "accept",
    "masked_card" : "411111-1111",
    "gross_amount" : "2700"
}');

class VeritransNotificationTest extends PHPUnit_Framework_TestCase
{

    public function testCanWorkWithJSON() {
        $tmpfname = tempnam(sys_get_temp_dir(), "veritrans_test");
        file_put_contents($tmpfname, TEST_CAPTURE_JSON);

        VT_Tests::$stubHttp = true;
        VT_Tests::$stubHttpResponse = TEST_CAPTURE_JSON;

        $notif = new Veritrans_Notification($tmpfname);

        $this->assertEquals($notif->transaction_status, "capture");
        $this->assertEquals($notif->payment_type, "credit_card");
        $this->assertEquals($notif->order_id, "2014040745");
        $this->assertEquals($notif->gross_amount, "2700");

        unlink($tmpfname);
    }

    public function tearDown() {
      VT_Tests::reset();
    }
}