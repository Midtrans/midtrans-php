<?php

class Veritrans_Notification {

  private $response;

  public function __construct($input_source = "php://input")
  {
    $raw_notification = json_decode(file_get_contents($input_source), true);
    $status_response = Veritrans_Transaction::status($raw_notification['transaction_id']);
    $this->response = $status_response;
  }

  public function __get($name)
  {
    if (array_key_exists($name, $this->response)) {
      return $this->response->$name;
    }
  }
}

?>