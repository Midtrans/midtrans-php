<?php

namespace Midtrans;

class MidtransConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testReturnBaseUrl() {
        Midtrans_Config::$isProduction = false;
        $this->assertEquals(
            Midtrans_Config::getBaseUrl(),
            Midtrans_Config::SANDBOX_BASE_URL);

        Midtrans_Config::$isProduction = true;
        $this->assertEquals(
            Midtrans_Config::getBaseUrl(),
            Midtrans_Config::PRODUCTION_BASE_URL);
    }

    public function tearDown() {
      Midtrans_Config::$isProduction = false;
    }
}