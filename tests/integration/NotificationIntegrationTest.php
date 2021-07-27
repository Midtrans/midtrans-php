<?php

namespace Midtrans\integration;

use Midtrans\CoreApi;
use Midtrans\Notification;
use Midtrans\Transaction;
use Midtrans\utility\MtChargeFixture;

require_once 'IntegrationTest.php';


class NotificationIntegrationTest extends IntegrationTest
{
    private $status_response;

    public function setUp()
    {
        $charge_params = MtChargeFixture::build('bri_epay');
        $charge_response = CoreApi::charge($charge_params);
        $this->status_response = Transaction::status($charge_response->transaction_id);
    }

    public function testValidBriEPayNotification()
    {
        // Assume status response is similar to HTTP(s) notification
        $tmpfname = tempnam(sys_get_temp_dir(), "test");
        file_put_contents($tmpfname, json_encode($this->status_response));

        $notif = new Notification($tmpfname);

        $this->assertEquals($notif->status_code, "201");
        $this->assertEquals($notif->transaction_status, "pending");
        $this->assertEquals($notif->payment_type, "bri_epay");
        $this->assertEquals($notif->order_id, $this->status_response->order_id);
        $this->assertEquals($notif->transaction_id, $this->status_response->transaction_id);
        $this->assertEquals($notif->gross_amount, $this->status_response->gross_amount);

        unlink($tmpfname);
    }

    public function testFraudulentBriEPayNotification()
    {
        /*
        As a fraudster, I want the merchant to think that I finished e-Pay BRI payment
        1. alter transaction status, from pending to settlement
        2. alter status code, from 201 to 200
         */
        $this->status_response->transaction_status = "settlement";
        $this->status_response->status_code = "200";

        $tmpfname = tempnam(sys_get_temp_dir(), "test");
        file_put_contents($tmpfname, json_encode($this->status_response));

        $notif = new Notification($tmpfname);

        /*
        Merchant should not be tricked... thanks to Get Status API
         */
        $this->assertEquals("201", $notif->status_code);
        $this->assertEquals("pending", $notif->transaction_status);
        $this->assertEquals("bri_epay", $notif->payment_type);
        $this->assertEquals($this->status_response->order_id, $notif->order_id);
        $this->assertEquals($this->status_response->transaction_id, $notif->transaction_id);
        $this->assertEquals($this->status_response->gross_amount, $notif->gross_amount);

        unlink($tmpfname);
    }
}
