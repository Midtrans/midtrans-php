<?php

namespace Midtrans\integration;

use Midtrans\CoreApi;
use Midtrans\utility\MtChargeFixture;

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

    public function testCreatePayAccount()
    {
        $params = array(
            "payment_type" => "gopay",
            "gopay_partner" => array(
                "phone_number" => 874567446788,
                "redirect_url" => "https://www.google.com"
            )
        );
        $this->charge_response = CoreApi::linkPaymentAccount($params);
        $this->assertEquals('201', $this->charge_response->status_code);
        $this->assertEquals('PENDING', $this->charge_response->account_status);
    }

    public function testGetPaymentAccount()
    {
        try {
            $this->charge_response = CoreApi::getPaymentAccount("dummy");
        } catch (\Exception $e) {
            $this->assertContains("Midtrans API is returning API error.", $e->getMessage());
        }
    }

    public function testUnlinkPaymentAccount()
    {
        try {
            $this->charge_response = CoreApi::unlinkPaymentAccount("dummy");
        } catch (\Exception $e) {
            $this->assertContains("Account doesn't exist.", $e->getMessage());
        }
    }

    public function testCreateSubscription()
    {
        $param = array(
            "name" => "Monthly_2021",
            "amount" => "10000",
            "currency" => "IDR",
            "payment_type" => "credit_card",
            "token" => "dummy",
            "schedule" => array(
                "interval" => 1,
                "interval_unit" => "month",
                "max_interval" => "12",
                "start_time" => "2022-08-17 10:00:01 +0700"
            ),
            "metadata" => array(
                "description" => "Recurring payment for user a"
            ),
            "customer_details" => array(
                "first_name" => "John",
                "last_name" => "Doe",
                "email" => "johndoe@gmail.com",
                "phone_number" => "+628987654321"
            )
        );
        $this->charge_response = CoreApi::createSubscription($param);
        $this->assertEquals('active', $this->charge_response->status);
        $this->subscriptionId = $this->charge_response->id;
    }

    public function testGetSubscription()
    {
        try {
            $this->charge_response = CoreApi::getSubscription("dummy");
        } catch (\Exception $e) {
            $this->assertContains("Subscription doesn't exist.", $e->getMessage());
        }
    }

    public function testDisableSubscription()
    {
        try {
            $this->charge_response = CoreApi::disableSubscription("dummy");
        } catch (\Exception $e) {
            $this->assertContains("Subscription doesn't exist.", $e->getMessage());
        }
    }

    public function testEnableSubscription()
    {
        try {
            $this->charge_response = CoreApi::enableSubscription("dummy");
        } catch (\Exception $e) {
            $this->assertContains("Subscription doesn't exist.", $e->getMessage());
        }
    }

    public function testUpdateSubscription()
    {
        $param = array(
            "name" => "Monthly_2021",
            "amount" => "25000",
            "currency" => "IDR",
            "token" => "dummy",
            "schedule" => array(
                "interval" => 1
            )
        );

        try {
            $this->charge_response = CoreApi::updateSubscription("dummy", $param);
        } catch (\Exception $e) {
            $this->assertContains("Subscription doesn't exist.", $e->getMessage());
        }
    }
}
