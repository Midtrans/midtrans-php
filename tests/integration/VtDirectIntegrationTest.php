<?php
require_once 'VtIntegrationTest.php';

class VtDirectIntegrationTest extends VtIntegrationTest {
	private $payment_type;
	private $charge_params;
	private $charge_response;

	public function prepareChargeParams($payment_type, $payment_data = NULL) {
		$this->payment_type = $payment_type;
		$this->charge_params = VtChargeFixture::build($payment_type, $payment_data);
	}

	public function testChargeMandiriClickpay() {
		$this->prepareChargeParams('mandiri_clickpay',
			array(
				"card_number" => "4111111111111111",
				"input1" => "1111111111",
				"input2" => "145000",
				"input3" => "54321",
				"token" => "000000",
			));
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'settlement');
	}

	public function testTCash() {
		$this->prepareChargeParams('telkomsel_cash',
			array(
				"customer" => "0811111111",
				"promo" => false,
				"is_reversal" => 0,
			));
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'settlement');
	}

	public function testCimbClicks() {
		$this->prepareChargeParams('cimb_clicks',
			array(
				"description" => "Item Descriptions",
			));
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'pending');
		$this->assertTrue(isset($this->charge_response->redirect_url));
	}

	public function testPermataVa() {
		$this->prepareChargeParams('bank_transfer',
			array(
				"bank" => "permata",
			));
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'pending');
		$this->assertTrue(isset($this->charge_response->permata_va_number));
	}

	public function testEPayBri() {
		$this->prepareChargeParams('bri_epay');
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'pending');
		$this->assertTrue(isset($this->charge_response->redirect_url));
	}

	public function testXlTunai() {
		$this->prepareChargeParams('xl_tunai');
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'pending');
		$this->assertTrue(isset($this->charge_response->xl_tunai_merchant_id));
		$this->assertTrue(isset($this->charge_response->xl_tunai_order_id));
	}

	public function testMandiriBillPayment() {
		$this->prepareChargeParams('echannel',
			array(
				"bill_info1" => "Payment for:",
				"bill_info2" => "Item descriptions",
			));
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'pending');
		$this->assertTrue(isset($this->charge_response->bill_key));
		$this->assertTrue(isset($this->charge_response->biller_code));
	}

	public function testBbmMoney() {
		$this->prepareChargeParams('bbm_money');
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'pending');
		$this->assertTrue(isset($this->charge_response->permata_va_number));
	}

	public function testIndomaret() {
		$this->prepareChargeParams('cstore',
			array(
				"store" => "Fruit Market",
				"message" => "Item descriptions",
			));
		$this->charge_response = Veritrans_VtDirect::charge($this->charge_params);
		$this->assertEquals($this->charge_response->transaction_status, 'pending');
		$this->assertTrue(isset($this->charge_response->payment_code));
	}

	public function assertPostConditions() {
		$this->assertContains($this->charge_response->status_code, [200, 201]);
		$this->assertEquals($this->charge_response->order_id,
			$this->charge_params['transaction_details']['order_id']);
		$this->assertEquals($this->charge_response->gross_amount,
			$this->charge_params['transaction_details']['gross_amount']);
		$this->assertEquals($this->charge_response->payment_type,
			$this->payment_type);
		$this->assertTrue(isset($this->charge_response->transaction_id));
		$this->assertTrue(isset($this->charge_response->transaction_time));
		$this->assertTrue(isset($this->charge_response->status_message));
	}
}