<?php

use Midtrans\Config;
use Midtrans\Transaction;

class MidtransTransactionTest extends \PHPUnit_Framework_TestCase
{

    public function testStatus()
    {
        Config::$serverKey = 'MyVerySecretKey';
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
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

        $status = Transaction::status("Order-111");

        $this->assertEquals("200", $status->status_code);
        $this->assertEquals("Order-111", $status->order_id);
        $this->assertEquals("1416550071152", $status->approval_code);

        $this->assertEquals(
            "https://api.sandbox.midtrans.com/v2/Order-111/status",
            MT_Tests::$lastHttpRequest["url"]
        );

        $fields = MT_Tests::lastReqOptions();
        $this->assertFalse(isset($fields['POST']));
        $this->assertFalse(isset($fields['POSTFIELDS']));
    }

    public function testFailureStatus()
    {
        Config::$serverKey = 'MyVerySecretKey';
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
            "status_code": "404",
            "status_message": "The requested resource is not found"
        }';

        try {
            $status = Transaction::status("Order-111");
        } catch (\Exception $error) {
            $errorHappen = true;
            $this->assertEquals(404, $error->getCode());
        }

        $this->assertTrue($errorHappen);
        MT_Tests::reset();
    }

    public function testRealStatus()
    {
        Config::$serverKey = 'MyVerySecretKey';
        try {
            $status = Transaction::status("Order-111");
        } catch (\Exception $error) {
            $errorHappen = true;
            $this->assertContains("Midtrans API is returning API error. HTTP status code: 401", $error->getMessage());
        }

        $this->assertTrue($errorHappen);
    }

    public function testApprove()
    {
        Config::$serverKey = 'MyVerySecretKey';
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
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

        $approve = Transaction::approve("Order-111");

        $this->assertEquals("200", $approve);

        $this->assertEquals(
            "https://api.sandbox.midtrans.com/v2/Order-111/approve",
            MT_Tests::$lastHttpRequest["url"]
        );

        $fields = MT_Tests::lastReqOptions();
        $this->assertEquals(1, $fields["POST"]);
        $this->assertEquals(null, $fields["POSTFIELDS"]);
    }

    public function testCancel()
    {
        Config::$serverKey = 'MyVerySecretKey';
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
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

        $cancel = Transaction::cancel("Order-111");

        $this->assertEquals("200", $cancel);

        $this->assertEquals(
            "https://api.sandbox.midtrans.com/v2/Order-111/cancel",
            MT_Tests::$lastHttpRequest["url"]
        );

        $fields = MT_Tests::lastReqOptions();
        $this->assertEquals(1, $fields["POST"]);
        $this->assertEquals(null, $fields["POSTFIELDS"]);
    }

    public function testExpire()
    {
        Config::$serverKey = 'MyVerySecretKey';
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
            "status_code": "407",
            "status_message": "Success, transaction has expired",
            "transaction_id": "2af158d4-b82e-46ac-808b-be19aaa96ce3",
            "order_id": "Order-111",
            "payment_type": "echannel",
            "transaction_time": "2014-11-27 10:05:10",
            "transaction_status": "expire",
            "gross_amount": "10000.00"
        }';

        $expire = Transaction::expire("Order-111");

        $this->assertEquals("407", $expire->status_code);
        $this->assertEquals("Success, transaction has expired", $expire->status_message);

        $this->assertEquals(
            "https://api.sandbox.midtrans.com/v2/Order-111/expire",
            MT_Tests::$lastHttpRequest["url"]
        );

        $fields = MT_Tests::lastReqOptions();
        $this->assertEquals(1, $fields["POST"]);
        $this->assertEquals(null, $fields["POSTFIELDS"]);
    }

    public function testRefund()
    {
        Config::$serverKey = 'MyVerySecretKey';
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
            "status_code": "200",
            "status_message": "Success, refund request is approved",
            "transaction_id": "447e846a-403e-47db-a5da-d7f3f06375d6",
            "order_id": "Order-111",
            "payment_type": "credit_card",
            "transaction_time": "2015-06-15 13:36:24",
            "transaction_status": "refund",
            "gross_amount": "10000.00",
            "refund_chargeback_id": 1,
            "refund_amount": "10000.00",
            "refund_key": "reference1"
        }';

        $params = array(
            'refund_key' => 'reference1',
            'amount' => 10000,
            'reason' => 'Item out of stock'
        );
        $refund = Transaction::refund("Order-111",$params);

        $this->assertEquals("200", $refund->status_code);

        $this->assertEquals(
            "https://api.sandbox.midtrans.com/v2/Order-111/refund",
            MT_Tests::$lastHttpRequest["url"]
        );

        $fields = MT_Tests::lastReqOptions();
        $this->assertEquals(1, $fields["POST"]);
        $this->assertEquals(
            '{"refund_key":"reference1","amount":10000,"reason":"Item out of stock"}',
            $fields["POSTFIELDS"]);
    }

    public function testRefundDirect()
    {
        Config::$serverKey = 'MyVerySecretKey';
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
            "status_code": "200",
            "status_message": "Success, refund request is approved",
            "transaction_id": "447e846a-403e-47db-a5da-d7f3f06375d6",
            "order_id": "Order-111",
            "payment_type": "credit_card",
            "transaction_time": "2015-06-15 13:36:24",
            "transaction_status": "refund",
            "gross_amount": "10000.00",
            "refund_chargeback_id": 1,
            "refund_amount": "10000.00",
            "refund_key": "reference1"
        }';
        
        $params = array(
            'refund_key' => 'reference1',
            'amount' => 10000,
            'reason' => 'Item out of stock'
        );
        $refund = Transaction::refundDirect("Order-111", $params);
        $this->assertEquals("200", $refund->status_code);

        $this->assertEquals(
            "https://api.sandbox.midtrans.com/v2/Order-111/refund/online/direct",
            MT_Tests::$lastHttpRequest["url"]
        );
        $fields = MT_Tests::lastReqOptions();
        $this->assertEquals(1, $fields["POST"]);
        $this->assertEquals(
            '{"refund_key":"reference1","amount":10000,"reason":"Item out of stock"}',
            $fields["POSTFIELDS"]);
    }

    public function testDeny()
    {
        Config::$serverKey = 'MyVerySecretKey';
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{
            "status_code" : "200",
            "status_message" : "Success, transaction is denied",
            "transaction_id" : "ca297170-be4c-45ed-9dc9-be5ba99d30ee",
            "masked_card" : "451111-1117",
            "order_id" : "Order-111",
            "payment_type" : "credit_card",
            "transaction_time" : "2014-10-31 14:46:44",
            "transaction_status" : "deny",
            "fraud_status" : "deny",
            "bank" : "bni",
            "gross_amount" : "30000.00"
        }';

        $deny = Transaction::deny("Order-111");

        $this->assertEquals("200", $deny->status_code);

        $this->assertEquals(
            "https://api.sandbox.midtrans.com/v2/Order-111/deny",
            MT_Tests::$lastHttpRequest["url"]
        );

        $fields = MT_Tests::lastReqOptions();
        $this->assertEquals(1, $fields["POST"]);
        $this->assertEquals(null, $fields["POSTFIELDS"]);
    }

    public function tearDown()
    {
        MT_Tests::reset();
    }
}
