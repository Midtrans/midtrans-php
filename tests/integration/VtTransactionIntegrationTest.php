<?php
require_once 'VtIntegrationTest.php';

class VtTransactionIntegrationTest extends VtIntegrationTest {

	public function testStatusPermataVa() {
		$charge_params = VtChargeFixture::build('bank_transfer',
			array(
				"bank" => "permata",
			));
		$charge_response = Veritrans_VtDirect::charge($charge_params);
		$status_response = Veritrans_Transaction::status($charge_response->transaction_id);

		$this->assertEquals($status_response->status_code, '201');
		$this->assertEquals($status_response->transaction_status, 'pending');
		$this->assertEquals($status_response->order_id, $charge_params['transaction_details']['order_id']);
		$this->assertEquals($status_response->gross_amount, $charge_params['transaction_details']['gross_amount']);
		$this->assertEquals($status_response->transaction_id, $charge_response->transaction_id);
		$this->assertEquals($status_response->transaction_time, $charge_response->transaction_time);
		$this->assertEquals($status_response->status_message, 'Success, transaction is found');

		$this->assertTrue(isset($status_response->signature_key));
	}

	public function testCancelPermataVa() {
		$charge_params = VtChargeFixture::build('bank_transfer',
			array(
				"bank" => "permata",
			));
		$charge_response = Veritrans_VtDirect::charge($charge_params);
		$cancel_status_code = Veritrans_Transaction::cancel($charge_response->transaction_id);

		$this->assertEquals($cancel_status_code, '200');
	}

	public function testExpirePermataVa() {
		$charge_params = VtChargeFixture::build('bank_transfer',
			array(
				"bank" => "permata",
			));
		$charge_response = Veritrans_VtDirect::charge($charge_params);
		$expire = Veritrans_Transaction::expire($charge_response->transaction_id);

		$this->assertEquals($expire->status_code, '407');

		// Verify transaction via API
		$txn_status = Veritrans_Transaction::status($charge_response->transaction_id);
		$this->assertEquals($txn_status->status_code, "407");
		$this->assertEquals($txn_status->transaction_status, "expire");
	}
}
