<?php

class VeritransVtWebTest extends PHPUnit_Framework_TestCase
{

    public function testGetRedirectionUrl() {
      Veritrans_Config::$serverKey = 'My Very Secret Key';
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{ "status_code": 200, "redirect_url": "http://host.com/pay" }';

      $params = array(
        'transaction_details' => array(
          'order_id' => "Order-111",
          'gross_amount' => 10000,
        )
      );

      $paymentUrl = Veritrans_Vtweb::getRedirectionUrl($params);

      $this->assertEquals($paymentUrl, "http://host.com/pay");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.midtrans.com/v2/charge"
      );

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["server_key"],
        'My Very Secret Key'
      );

      $fields = VT_Tests::lastReqOptions();

      $this->assertEquals($fields["POST"], 1);
      $this->assertEquals($fields["POSTFIELDS"],
        '{"payment_type":"vtweb","vtweb":{"credit_card_3d_secure":false},' . 
        '"transaction_details":{"order_id":"Order-111","gross_amount":10000}}'
      );
    }

    public function testGrossAmount() {
      $params = array(
        'transaction_details' => array(
          'order_id' => rand()
        ),
        'item_details' => array( array( 'price' => 10000, 'quantity' => 5 ) )
      );

      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{ "status_code": 200, "redirect_url": "http://host.com/pay" }';

      $paymentUrl = Veritrans_Vtweb::getRedirectionUrl($params);

      $this->assertEquals(
        VT_Tests::$lastHttpRequest['data_hash']['transaction_details']['gross_amount'],
        50000
      );
    }

    public function testOverrideParams() {
      $params = array(
        'vtweb' => array(
          'extra' => 'param'
        )
      );

      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{ "status_code": 200, "redirect_url": "http://host.com/pay" }';

      $paymentUrl = Veritrans_Vtweb::getRedirectionUrl($params);

      $this->assertEquals(
        VT_Tests::$lastHttpRequest['data_hash']["vtweb"],
        array("credit_card_3d_secure" => false, "extra" => "param")
      );
    }

    public function testRealConnect() {
      $params = array(
        'transaction_details' => array(
          'order_id' => rand(),
          'gross_amount' => 10000,
        )
      );

      try {
        $paymentUrl = Veritrans_Vtweb::getRedirectionUrl($params);
      } catch (Exception $error) {
        $errorHappen = true;
        $this->assertContains(
          "authorized",
          $error->getMessage()
        );
      }

      $this->assertTrue($errorHappen);
    }

    public function tearDown() {
      VT_Tests::reset();
    }

}
