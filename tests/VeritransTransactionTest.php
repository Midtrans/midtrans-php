<?php

class VeritransTransactionTest extends PHPUnit_Framework_TestCase
{

    public function testStatus() {
      Veritrans_Config::$serverKey = 'My Very Secret Key';
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{
        "status_code": "200",
        "status_message": "Success, transaction found",
        "transaction_id": "e3b8c383-55b4-4223-bd77-15c48c0245ca",
        "masked_card": "481111-1114",
        "order_id": "Order-111",
        "payment_type": "credit_card",
        "transaction_time": "2014-11-21 13:07:50",
        "transaction_status": "settlement",
        "fraud_status": "accept",
        "approval_code": "1416550071152",
        "signature_key": "4ef8218aad5b64bae2ec9d6b0f0a0b059b88bd...",
        "bank": "mandiri",
        "gross_amount": "10000.00"
      }';

      $status = Veritrans_Transaction::status("Order-111");

      $this->assertEquals($status->status_code, "200");
      $this->assertEquals($status->order_id, "Order-111");
      $this->assertEquals($status->approval_code, "1416550071152");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.midtrans.com/v2/Order-111/status"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertFalse(array_key_exists("POST", $fields));
      $this->assertFalse(array_key_exists("POSTFIELDS", $fields));
    }

    public function testFailureStatus() {
      Veritrans_Config::$serverKey = 'My Very Secret Key';
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{
        "status_code": "404",
        "status_message": "The requested resource is not found"
      }';

      try {
        $status = Veritrans_Transaction::status("Order-111");
      } catch (Exception $error) {
        $errorHappen = true;
        $this->assertEquals(
          $error->getMessage(),
          "Veritrans Error (404): The requested resource is not found");
      }

      $this->assertTrue($errorHappen);
      VT_Tests::reset();
    }

    public function testRealStatus() {
      try {
        $status = Veritrans_Transaction::status("Order-111");
      } catch (Exception $error) {
        $errorHappen = true;
        $this->assertContains(
          "authorized",
          $error->getMessage()
        );
      }

      $this->assertTrue($errorHappen);
    }

    public function testApprove () {
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{
        "status_code": "200",
        "status_message": "Success, transaction is approved",
        "transaction_id": "2af158d4-b82e-46ac-808b-be19aaa96ce3",
        "masked_card": "451111-1117",
        "order_id": "Order-111",
        "payment_type": "credit_card",
        "transaction_time": "2014-11-27 10:05:10",
        "transaction_status": "capture",
        "fraud_status": "accept",
        "approval_code": "1416550071152",
        "bank": "bni",
        "gross_amount": "10000.00"
      }';

      $approve = Veritrans_Transaction::approve("Order-111");

      $this->assertEquals($approve, "200");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.midtrans.com/v2/Order-111/approve"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertEquals($fields["POST"], 1);
      $this->assertEquals($fields["POSTFIELDS"], null);
    }

    public function testCancel() {
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{
        "status_code": "200",
        "status_message": "Success, transaction is canceled",
        "transaction_id": "2af158d4-b82e-46ac-808b-be19aaa96ce3",
        "masked_card": "451111-1117",
        "order_id": "Order-111",
        "payment_type": "credit_card",
        "transaction_time": "2014-11-27 10:05:10",
        "transaction_status": "cancel",
        "fraud_status": "accept",
        "approval_code": "1416550071152",
        "bank": "bni",
        "gross_amount": "10000.00"
      }';

      $cancel = Veritrans_Transaction::cancel("Order-111");

      $this->assertEquals($cancel, "200");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.midtrans.com/v2/Order-111/cancel"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertEquals($fields["POST"], 1);
      $this->assertEquals($fields["POSTFIELDS"], null);
    }

    public function testExpire() {
      VT_Tests::$stubHttp = true;
      VT_Tests::$stubHttpResponse = '{
        "status_code": "407",
        "status_message": "Success, transaction has expired",
        "transaction_id": "2af158d4-b82e-46ac-808b-be19aaa96ce3",
        "order_id": "Order-111",
        "payment_type": "echannel",
        "transaction_time": "2014-11-27 10:05:10",
        "transaction_status": "expire",
        "gross_amount": "10000.00"
      }';

      $expire = Veritrans_Transaction::expire("Order-111");

      $this->assertEquals($expire->status_code, "407");
      $this->assertEquals($expire->status_message, "Success, transaction has expired");

      $this->assertEquals(
        VT_Tests::$lastHttpRequest["url"],
        "https://api.sandbox.midtrans.com/v2/Order-111/expire"
      );

      $fields = VT_Tests::lastReqOptions();
      $this->assertEquals($fields["POST"], 1);
      $this->assertEquals($fields["POSTFIELDS"], null);
    }


    public function tearDown() {
      VT_Tests::reset();
    }
}
