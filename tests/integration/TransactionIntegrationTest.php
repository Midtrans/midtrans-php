<?php

namespace integration;

use Midtrans\CoreApi;
use Midtrans\Transaction;
use utility\MtChargeFixture;
require_once 'IntegrationTest.php';

class TransactionIntegrationTest extends IntegrationTest
{

    public function testStatusPermataVa()
    {
        $charge_params = MtChargeFixture::build(
            'bank_transfer',
            array(
                "bank" => "permata",
            )
        );
        $charge_response = CoreApi::charge($charge_params);
        $status_response = Transaction::status($charge_response->transaction_id);

        $this->assertEquals('201', $status_response->status_code);
        $this->assertEquals('pending', $status_response->transaction_status);
        $this->assertEquals($charge_params['transaction_details']['order_id'], $status_response->order_id);
        $this->assertEquals($charge_params['transaction_details']['gross_amount'], $status_response->gross_amount);
        $this->assertEquals($charge_response->transaction_id, $status_response->transaction_id);
        $this->assertEquals($charge_response->transaction_time, $status_response->transaction_time);
        $this->assertEquals('Success, transaction is found', $status_response->status_message);

        $this->assertTrue(isset($status_response->signature_key));
    }

    public function testCancelPermataVa()
    {
        $charge_params = MtChargeFixture::build(
            'bank_transfer',
            array(
                "bank" => "permata",
            )
        );
        $charge_response = CoreApi::charge($charge_params);
        $cancel_status_code = Transaction::cancel($charge_response->transaction_id);

        $this->assertEquals('200', $cancel_status_code);
    }

    public function testExpirePermataVa()
    {
        $charge_params = MtChargeFixture::build(
            'bank_transfer',
            array(
                "bank" => "permata",
            )
        );
        $charge_response = CoreApi::charge($charge_params);
        $expire = Transaction::expire($charge_response->transaction_id);

        $this->assertEquals('407', $expire->status_code);

        // Verify transaction via API
        $txn_status = Transaction::status($charge_response->transaction_id);
        $this->assertEquals("407", $txn_status->status_code);
        $this->assertEquals("expire", $txn_status->transaction_status);
    }
}
