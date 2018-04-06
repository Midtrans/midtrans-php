<?php

class VeritransVtDirectTest extends PHPUnit_Framework_TestCase
{

    public function testCharge() {
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{
        "status_code": 200,
        "redirect_url": "http://host.com/pay"
      }';

      $params = array(
        'transaction_details' => array(
          'order_id' => "Order-111",
          'gross_amount' => 10000,
        )
      );

      $charge = Veritrans_VtDirect::charge($params);

      $this->assertEquals($charge->status_code, "200");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.midtrans.com/v2/charge"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertEquals($fields["POST"], 1);
      $this->assertEquals($fields["POSTFIELDS"],
        '{"payment_type":"credit_card","transaction_details":{"order_id":"Order-111","gross_amount":10000}}'
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
        $paymentUrl = Veritrans_VtDirect::charge($params);
      } catch (Exception $error) {
        $errorHappen = true;
        $this->assertContains(
          $error->getMessage(),
          array(
            "Veritrans Error (401): Transaction cannot be authorized with the current client/server key.",
            "Veritrans Error (411): Token id is missing, invalid, or timed out"
          )
        );
        /*
        $this->assertEquals(
          $error->getMessage(),
          "Veritrans Error (401): Access denied due to unauthorized transaction, please check client or server key");
        */
      }

      $this->assertTrue($errorHappen);
    }

    public function testCapture() {
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{
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

      $capture = Veritrans_VtDirect::capture("A27550");

      $this->assertEquals($capture->status_code, "200");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.midtrans.com/v2/capture"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertEquals($fields["POST"], 1);
      $this->assertEquals($fields["POSTFIELDS"], '{"transaction_id":"A27550"}');
    }

    public function tearDown() {
      VT_Tests::reset();
    }
}
