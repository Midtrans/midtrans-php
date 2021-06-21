<?php

use Midtrans\Config;
use Midtrans\CoreApi;

class MidtransCoreApiTest extends \PHPUnit_Framework_TestCase
{

    public function testCharge()
    {
        Config::$appendNotifUrl = "https://example.com";
        Config::$overrideNotifUrl = "https://example.com";
        Config::$paymentIdempotencyKey = "123456";
        Config::$serverKey = "dummy";
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
            "status_code": 200,
            "redirect_url": "http://host.com/pay"
        }';

        $params = array(
            'transaction_details' => array(
            'order_id' => "Order-111",
            'gross_amount' => 10000,
            )
        );

        $charge = CoreApi::charge($params);

        $this->assertEquals($charge->status_code, "200");

        $this->assertEquals(
            MT_Tests::$lastHttpRequest["url"],
            "https://api.sandbox.midtrans.com/v2/charge"
        );

        $fields = MT_Tests::lastReqOptions();
        $this->assertEquals($fields["POST"], 1);
        $this->assertEquals(
            $fields["POSTFIELDS"],
            '{"payment_type":"credit_card","transaction_details":{"order_id":"Order-111","gross_amount":10000}}'
        );
        $this->assertTrue(in_array('X-Append-Notification: https://example.com', $fields["HTTPHEADER"]));
        $this->assertTrue(in_array('X-Override-Notification: https://example.com', $fields["HTTPHEADER"]));
        $this->assertTrue(in_array('Idempotency-Key: 123456', $fields["HTTPHEADER"]));
    }

    public function testRealConnectWithInvalidKey()
    {
        Config::$serverKey = 'invalid-server-key';
        $params = array(
            'transaction_details' => array(
            'order_id' => rand(),
            'gross_amount' => 10000,
            )
        );

        try {
            $paymentUrl = CoreApi::charge($params);
        } catch (\Exception $error) {
            $this->assertContains("Midtrans API is returning API error. HTTP status code: 401", $error->getMessage());
        }
    }

    public function testCapture()
    {
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
            "status_code": "200",
            "status_message": "Success, Credit Card capture transaction is successful",
            "transaction_id": "1ac1a089d-a587-40f1-a936-a7770667d6dd",
            "order_id": "A27550",
            "payment_type": "credit_card",
            "transaction_time": "2014-08-25 10:20:54",
            "transaction_status": "capture",
            "fraud_status": "accept",
            "masked_card": "481111-1114",
            "bank": "bni",
            "approval_code": "1408937217061",
            "gross_amount": "55000.00"
        }';

        $capture = CoreApi::capture("A27550");

        $this->assertEquals($capture->status_code, "200");

        $this->assertEquals("https://api.sandbox.midtrans.com/v2/capture", MT_Tests::$lastHttpRequest["url"]);

        $fields = MT_Tests::lastReqOptions();
        $this->assertEquals(1, $fields["POST"]);
        $this->assertEquals('{"transaction_id":"A27550"}', $fields["POSTFIELDS"]);
    }

    public function tearDown()
    {
        MT_Tests::reset();
    }
}
