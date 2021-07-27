<?php

namespace Midtrans\integration;

use Midtrans\Config;

abstract class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        Config::$serverKey = getenv('SERVER_KEY');
        Config::$clientKey = getenv('CLIENT_KEY');
        Config::$isProduction = false;
    }

    public function tearDown()
    {
        // One second interval to avoid throttle
        sleep(1);
    }
}
