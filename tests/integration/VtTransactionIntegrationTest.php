<?php
require_once 'VtIntegrationTest.php';

class VtTransactionIntegrationTest extends VtIntegrationTest {

	public function prepareChargeParams($payment_type, $payment_data = NULL) {
		$this->payment_type = $payment_type;
		$this->charge_params = VtChargeFixture::build($payment_type, $payment_data);
	}

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
		$this->assertEquals($status_response->status_message, 'Success, transaction found');

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
}