<?php

class Veritrans_Notification {

  private $response;

  public function __construct()
  {
    $this->response = json_decode(file_get_contents('php://input'), true);
  }

  public function isVerified()
  {
    return $this->response['notification_key'] == Veritrans_Config::$serverKey;
  }

  public function __get($name)
  {
    if (array_key_exists($name, $this->response)) {
      return $this->response[$name];
    }
  }
}