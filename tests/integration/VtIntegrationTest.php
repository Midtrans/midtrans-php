<?php

namespace Midtrans;

abstract class VtIntegrationTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Midtrans_Config::$serverKey = getenv('SERVER_KEY');
        Midtrans_Config::$clientKey = getenv('CLIENT_KEY');
        Midtrans_Config::$isProduction = false;
    }

    public function tearDown()
    {
        // One second interval to avoid throttle
        sleep(1);
    }
}
