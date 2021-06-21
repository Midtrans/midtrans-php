<?php

namespace integration;

use Midtrans\Snap;
use utility\MtChargeFixture;
require_once 'IntegrationTest.php';

class SnapIntegrationTest extends IntegrationTest
{
    public function testSnapToken()
    {
        $charge_params = MtChargeFixture::build('vtweb');
        $token_id = Snap::getSnapToken($charge_params);

        $this->assertTrue(isset($token_id));
    }
}
