<?php

class Veritrans_Notification {

  private $response;

  public function __construct($input_source = "php://input")
  {
    $this->response = json_decode(file_get_contents($input_source), true);
  }

  public function __get($name)
  {
    if (array_key_exists($name, $this->response)) {
      return $this->response[$name];
    }
  }
}

?>