<?php

class VeritransConfigTest extends PHPUnit_Framework_TestCase
{

    public function testReturnBaseUrl() {
        Veritrans_Config::$isProduction = false;
        $this->assertEquals(
            Veritrans_Config::getBaseUrl(),
            Veritrans_Config::SANDBOX_BASE_URL);

        Veritrans_Config::$isProduction = true;
        $this->assertEquals(
            Veritrans_Config::getBaseUrl(),
            Veritrans_Config::PRODUCTION_BASE_URL);
    }

    public function tearDown() {
      Veritrans_Config::$isProduction = false;
    }
}