<?php
require_once 'VtIntegrationTest.php';

class SnapIntegrationTest extends VtIntegrationTest {
	public function testSnapToken() {
		$charge_params = VtChargeFixture::build('vtweb');
		$token_id = Veritrans_Snap::getSnapToken($charge_params);

		$this->assertTrue(isset($token_id));
	}
}
