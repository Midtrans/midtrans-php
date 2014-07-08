<?php

class Veritrans_Sanitizer {
  private $filters;

  public function __construct()
  {
    $this->filters = array();
  }

  public static function jsonRequest(&$json)
  {
    $keys = array('item_details', 'customer_details');
    foreach ($keys as $key) {
      if (!array_key_exists($key, $json)) continue;

      $camel = static::upperCamelize($key);
      $function = "field$camel";
      static::$function($json[$key]);
    }
  }

  private static function fieldItemDetails(&$items)
  {
    foreach ($items as &$item) {
      $item['id'] = (new self)
          ->maxLength(50)
          ->apply($item['id']);
      $item['name'] = (new self)
          ->maxLength(50)
          ->apply($item['name']);
    }
  }

  private static function fieldCustomerDetails(&$field)
  {
    $field['first_name'] = (new self)
        ->maxLength(20)
        ->apply($field['first_name']);
    if (array_key_exists('last_name', $field)) {
      $field['last_name'] = (new self)
          ->maxLength(20)
          ->apply($field['last_name']);
    }
    $field['email'] = (new self)
        ->maxLength(45)
        ->apply($field['email']);

    static::fieldPhone($field['phone']);

    $keys = array('billing_address', 'shipping_address');
    foreach ($keys as $key) {
      if (!array_key_exists($key, $field)) continue;

      $camel = static::upperCamelize($key);
      $function = "field$camel";
      static::$function($field[$key]);
    }
  }

  private static function fieldBillingAddress(&$field)
  {
    $fields = array(
        'first_name'   => 20,
        'last_name'    => 20,
        'address'      => 200,
        'city'         => 20,
        'country_code' => 10
      );

    foreach ($fields as $key => $value) {
      if (array_key_exists($key, $field)) {
        $field[$key] = (new self)
            ->maxLength($value)
            ->apply($field[$key]);
      }
    }

    if (array_key_exists('postal_code', $field)) {
      $field['postal_code'] = (new self)
          ->whitelist('A-Za-z0-9\\- ')
          ->maxLength(10)
          ->apply($field['postal_code']);
    }
    if (array_key_exists('phone', $field)) {
      static::fieldPhone($field['phone']);
    }
  }

  private static function fieldShippingAddress(&$field)
  {
    static::fieldBillingAddress($field);
  }

  private static function fieldPhone(&$field)
  {
    $plus = substr($field, 0, 1) === '+' ? true : false;
    $field = (new self)
        ->whitelist('\\d\\-\\(\\) ')
        ->maxLength(19)
        ->apply($field);

    if ($plus) $field = '+' . $field;
    $field = (new self)
        ->maxLength(19)
        ->apply($field);
  }

  private function maxLength($length)
  {
    $this->filters[] = function($input) use($length) {
      return substr($input, 0, $length);
    };
    return $this;
  }

  private function whitelist($regex)
  {
    $this->filters[] = function($input) use($regex) {
      return preg_replace("/[^$regex]/", '', $input);
    };
    return $this;
  }

  private function apply($input)
  {
    foreach ($this->filters as $filter) {
      $input = call_user_func($filter, $input);
    }
    return $input;
  }

  private static function upperCamelize($string)
  {
    return str_replace(' ', '',
        ucwords(str_replace('_', ' ', $string)));
  }
}