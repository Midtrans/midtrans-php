<?php

use Midtrans\ApiRequestor;
use Midtrans\Config;

class MidtransSnapApiRequestorTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigOptionsOverrideCurlOptions()
    {
        MT_Tests::$stubHttp = true;
        MT_Tests::$stubHttpResponse = '{ "status_code": "200" }';
        MT_Tests::$stubHttpStatus = array('http_code' => 201);

        Config::$curlOptions = array(
            CURLOPT_HTTPHEADER => array( "User-Agent: testing lib" ),
            CURLOPT_PROXY => "http://proxy.com"
        );

        $resp = ApiRequestor::post("http://example.com", "dummy", "");

        $fields = MT_Tests::lastReqOptions();
        $this->assertTrue(in_array("User-Agent: testing lib", $fields["HTTPHEADER"]));
        $this->assertTrue(in_array('Content-Type: application/json', $fields["HTTPHEADER"]));

        $this->assertEquals("http://proxy.com", $fields["PROXY"]);
    }

    public function tearDown()
    {
        MT_Tests::reset();
        Config::$curlOptions = array();
    }

}
