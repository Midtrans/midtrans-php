<?php

namespace Midtrans;

require_once 'VtIntegrationTest.php';

class SnapIntegrationTest extends VtIntegrationTest
{
    public function testSnapToken()
    {
        $charge_params = VtChargeFixture::build('vtweb');
        $token_id = Snap::getSnapToken($charge_params);

        $this->assertTrue(isset($token_id));
    }
}
