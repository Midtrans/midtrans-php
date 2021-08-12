<?php


namespace Midtrans;

use Midtrans\utility\MtChargeFixture;

class MidtransSanitizerTest extends \PHPUnit_Framework_TestCase
{

    public function testSanitizeWithoutOptionalRequest()
    {
        $params = MtChargeFixture::build('vtweb');
        unset($params['customer_details']);

        Sanitizer::jsonRequest($params);
        $this->assertEquals(false, isset($params['customer_details']));
    }

    public function testSanitizeWithoutOptionalCustDetails()
    {
        $params = MtChargeFixture::build('vtweb');
        unset($params['customer_details']['first_name']);
        unset($params['customer_details']['last_name']);
        unset($params['customer_details']['email']);
        unset($params['customer_details']['billing_address']);
        unset($params['customer_details']['shipping_address']);

        Sanitizer::jsonRequest($params);

        $this->assertEquals(false, isset($params['customer_details']['first_name']));
        $this->assertEquals(false, isset($params['customer_details']['last_name']));
        $this->assertEquals(false, isset($params['customer_details']['email']));
        $this->assertEquals(false, isset($params['customer_details']['billing_address']));
        $this->assertEquals(false, isset($params['customer_details']['shipping_address']));
    }

    public function testSanitizeWithoutOptionalInBillingAddress()
    {
        $params = MtChargeFixture::build('vtweb');
        unset($params['customer_details']['billing_address']['first_name']);
        unset($params['customer_details']['billing_address']['last_name']);
        unset($params['customer_details']['billing_address']['phone']);
        unset($params['customer_details']['billing_address']['address']);
        unset($params['customer_details']['billing_address']['city']);
        unset($params['customer_details']['billing_address']['postal_code']);
        unset($params['customer_details']['billing_address']['country_code']);

        Sanitizer::jsonRequest($params);

        $this->assertEquals(false, isset($params['customer_details']['billing_address']['first_name']));
        $this->assertEquals(false, isset($params['customer_details']['billing_address']['last_name']));
        $this->assertEquals(false, isset($params['customer_details']['billing_address']['phone']));
        $this->assertEquals(false, isset($params['customer_details']['billing_address']['address']));
        $this->assertEquals(false, isset($params['customer_details']['billing_address']['city']));
        $this->assertEquals(false, isset($params['customer_details']['billing_address']['postal_code']));
        $this->assertEquals(false, isset($params['customer_details']['billing_address']['country_code']));
    }

    public function testSanitizeWithoutOptionalInShippingAddress()
    {
        $params = MtChargeFixture::build('vtweb');
        unset($params['customer_details']['shipping_address']['first_name']);
        unset($params['customer_details']['shipping_address']['last_name']);
        unset($params['customer_details']['shipping_address']['phone']);
        unset($params['customer_details']['shipping_address']['address']);
        unset($params['customer_details']['shipping_address']['city']);
        unset($params['customer_details']['shipping_address']['postal_code']);
        unset($params['customer_details']['shipping_address']['country_code']);

        Sanitizer::jsonRequest($params);

        $this->assertEquals(false, isset($params['customer_details']['shipping_address']['first_name']));
        $this->assertEquals(false, isset($params['customer_details']['shipping_address']['last_name']));
        $this->assertEquals(false, isset($params['customer_details']['shipping_address']['phone']));
        $this->assertEquals(false, isset($params['customer_details']['shipping_address']['address']));
        $this->assertEquals(false, isset($params['customer_details']['shipping_address']['city']));
        $this->assertEquals(false, isset($params['customer_details']['shipping_address']['postal_code']));
        $this->assertEquals(false, isset($params['customer_details']['shipping_address']['country_code']));
    }

}