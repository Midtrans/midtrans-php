<?php

use Midtrans\Config;
use Midtrans\Notification;

require_once dirname(__FILE__) . '/../Midtrans.php';

define(
    'TEST_CAPTURE_JSON', '{
        "status_code" : "200",
        "status_message" : "Midtrans payment notification",
        "transaction_id" : "826acc53-14e0-4ae7-95e2-845bf0311579",
        "order_id" : "2014040745",
        "payment_type" : "credit_card",
        "transaction_time" : "2014-04-07 16:22:36",
        "transaction_status" : "capture",
        "fraud_status" : "accept",
        "masked_card" : "411111-1111",
        "gross_amount" : "2700"
    }'
);

class MidtransNotificationTest extends \PHPUnit_Framework_TestCase
{

    public function testCanWorkWithJSON()
    {
        $tmpfname = tempnam(sys_get_temp_dir(), "midtrans_test");
        file_put_contents($tmpfname, TEST_CAPTURE_JSON);

        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = TEST_CAPTURE_JSON;

        Config::$serverKey = 'dummy';
        $notif = new Notification($tmpfname);

        $this->assertEquals("capture", $notif->transaction_status);
        $this->assertEquals("credit_card", $notif->payment_type);
        $this->assertEquals("2014040745", $notif->order_id);
        $this->assertEquals("2700", $notif->gross_amount);

        unlink($tmpfname);
    }

    public function tearDown()
    {
        MT_Tests::reset();
    }
}