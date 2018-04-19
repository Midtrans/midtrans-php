<?php
require_once 'VtIntegrationTest.php';

class VtWebIntegrationTest extends VtIntegrationTest {
	public function testVtWeb() {
		$charge_params = VtChargeFixture::build('vtweb');
		$redirect_url = Veritrans_VtWeb::getRedirectionUrl($charge_params);
		// sample $redirect_url: https://app.sandbox.veritrans.co.id/snap/v2/vtweb/4eee5948-2ced-4cb9-a6f2-6ae3907b05ce
		$this->assertRegExp("/https:\/\/app.sandbox.[\w\.]+.[\w\.]+\/v2\/vtweb\/[\w\-]+/", $redirect_url);
	}
}