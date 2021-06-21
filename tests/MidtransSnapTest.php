<?php

use Midtrans\Config;
use Midtrans\Snap;

class MidtransSnapTest extends \PHPUnit_Framework_TestCase
{

    public function testGetSnapToken()
    {
        Config::$serverKey = 'MyVerySecretKey';
        Config::$appendNotifUrl = "https://example.com";
        Config::$overrideNotifUrl = "https://example.com";
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{ "token": "abcdefghijklmnopqrstuvwxyz" }';
        MT_Tests::$stubHttpStatus = array('http_code' => 201);

        $params = array(
            'transaction_details' => array(
            'order_id' => "Order-111",
            'gross_amount' => 10000,
            )
        );

        $tokenId = Snap::getSnapToken($params);

        $this->assertEquals("abcdefghijklmnopqrstuvwxyz", $tokenId);

        $this->assertEquals(
            "https://app.sandbox.midtrans.com/snap/v1/transactions",
            MT_Tests::$lastHttpRequest["url"]
        );

        $this->assertEquals(
            'MyVerySecretKey',
            MT_Tests::$lastHttpRequest["server_key"]
        );

        $fields = MT_Tests::lastReqOptions();

        $this->assertEquals(1, $fields["POST"]);
        $this->assertTrue(in_array('X-Append-Notification: https://example.com', $fields["HTTPHEADER"]));
        $this->assertTrue(in_array('X-Override-Notification: https://example.com', $fields["HTTPHEADER"]));
        $this->assertEquals(
            $fields["POSTFIELDS"],
            '{"credit_card":{"secure":false},' .
            '"transaction_details":{"order_id":"Order-111","gross_amount":10000}}'
        );
    }

    public function testGrossAmount()
    {
        $params = array(
            'transaction_details' => array(
            'order_id' => rand()
            ),
            'item_details' => array( array( 'price' => 10000, 'quantity' => 5 ) )
        );

        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{ "token": "abcdefghijklmnopqrstuvwxyz" }';
        MT_Tests::$stubHttpStatus = array('http_code' => 201);

        $tokenId = Snap::getSnapToken($params);

        $this->assertEquals(
            50000,
            MT_Tests::$lastHttpRequest['data_hash']['transaction_details']['gross_amount']
        );
    }

    public function testOverrideParams()
    {
        $params = array(
            'echannel' => array(
            'bill_info1' => 'bill_value1'
            )
        );

        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{ "token": "abcdefghijklmnopqrstuvwxyz" }';
        MT_Tests::$stubHttpStatus = array('http_code' => 201);

        $tokenId = Snap::getSnapToken($params);

        $this->assertEquals(
            array('bill_info1' => 'bill_value1'),
            MT_Tests::$lastHttpRequest['data_hash']['echannel']
        );
    }

    public function testRealConnect()
    {
        $params = array(
            'transaction_details' => array(
            'order_id' => rand(),
            'gross_amount' => 10000,
            )
        );

        try {
            $tokenId = Snap::getSnapToken($params);
        } catch (\Exception $error) {
            $errorHappen = true;
            $this->assertContains(
                "authorized",
                $error->getMessage()
            );
        }

        $this->assertTrue($errorHappen);
    }

    public function tearDown()
    {
        MT_Tests::reset();
    }

}
