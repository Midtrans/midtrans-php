<?php

namespace Midtrans;

class MidtransConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testReturnBaseUrl()
    {
        Config::$isProduction = false;
        $this->assertEquals(
            Config::getBaseUrl(),
            Config::SANDBOX_BASE_URL
        );

        Config::$isProduction = true;
        $this->assertEquals(Config::PRODUCTION_BASE_URL, Config::getBaseUrl());
    }

    public function tearDown()
    {
        Config::$isProduction = false;
    }
}