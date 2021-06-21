<?php

namespace integration;

use Midtrans\CoreApi;
use utility\MtChargeFixture;
require_once 'IntegrationTest.php';

class CoreApiIntegrationTest extends IntegrationTest
{
    private $payment_type;
    private $charge_params;
    private $charge_response;

    public function prepareChargeParams($payment_type, $payment_data = null)
    {
        $this->payment_type = $payment_type;
        $this->charge_params = MtChargeFixture::build($payment_type, $payment_data);
    }

    public function testCardRegister()
    {
        $this->charge_response = CoreApi::cardRegister("4811111111111114", "12", "2026");
        $this->assertEquals('200', $this->charge_response->status_code);
    }

    public function testCardToken()
    {
        $this->charge_response = CoreApi::cardToken("4811111111111114", "12", "2026", "123");
        $this->assertEquals('200', $this->charge_response->status_code);
    }

    public function testCardPointInquiry()
    {
        $this->charge_response = CoreApi::cardToken("4617006959746656", "12", "2026", "123");
        $cardPointResponse = CoreApi::cardPointInquiry($this->charge_response->token_id);
        $this->assertEquals('200', $cardPointResponse->status_code);
    }

    public function testChargeCimbClicks()
    {
        $this->prepareChargeParams(
            'cimb_clicks',
            array(
                "description" => "Item Descriptions",
            )
        );
        $this->charge_response = CoreApi::charge($this->charge_params);
        $this->assertEquals('pending', $this->charge_response->transaction_status);
        $this->assertTrue(isset($this->charge_response->redirect_url));
    }

    public function testChargePermataVa()
    {
        $this->prepareChargeParams(
            'bank_transfer',
            array(
                "bank" => "permata",
            )
        );
        $this->charge_response = CoreApi::charge($this->charge_params);
        $this->assertEquals('pending', $this->charge_response->transaction_status);
        $this->assertTrue(isset($this->charge_response->permata_va_number));
    }

    public function testChargeEPayBri()
    {
        $this->prepareChargeParams('bri_epay');
        $this->charge_response = CoreApi::charge($this->charge_params);
        $this->assertEquals('pending', $this->charge_response->transaction_status);
        $this->assertTrue(isset($this->charge_response->redirect_url));
    }

    public function testChargeMandiriBillPayment()
    {
        $this->prepareChargeParams(
            'echannel',
            array(
                "bill_info1" => "Payment for:",
                "bill_info2" => "Item descriptions",
            )
        );
        $this->charge_response = CoreApi::charge($this->charge_params);
        $this->assertEquals('pending', $this->charge_response->transaction_status);
    }

    public function testChargeIndomaret()
    {
        $this->prepareChargeParams(
            'cstore',
            array(
                "store" => "indomaret",
                "message" => "Item descriptions",
            )
        );
        $this->charge_response = CoreApi::charge($this->charge_params);
        $this->assertEquals('pending', $this->charge_response->transaction_status);
        $this->assertTrue(isset($this->charge_response->payment_code));
    }

    public function testChargeGopay()
    {
        $this->prepareChargeParams(
            'gopay',
            array(
                "enable_callback" => true,
                "callback_url" => "someapps://callback",
            )
        );
        $this->charge_response = CoreApi::charge($this->charge_params);
        $this->assertEquals('201', $this->charge_response->status_code);
        $this->assertEquals('pending', $this->charge_response->transaction_status);
    }
}
