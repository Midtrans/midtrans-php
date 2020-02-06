<?php

namespace Midtrans;

/**
 * Read raw post input and parse as JSON. Provide getters for fields in notification object
 *
 * Example:
 *
 * ```php
 * 
 *   namespace Midtrans;
 * 
 *   $notif = new Notification();
 *   echo $notif->order_id;
 *   echo $notif->transaction_status;
 * ```
 */
class Notification
{
    private $response;

    public function __construct($input_source = "php://input")
    {
        $raw_notification = json_decode(file_get_contents($input_source), true);
        $status_response = Transaction::status($raw_notification['transaction_id']);
        $this->response = $status_response;
    }

    public function __get($name)
    {
        if (isset($this->response->$name)) {
            return $this->response->$name;
        }
    }

    public function getResponse()
    {
        return $this->response;
    }
}
