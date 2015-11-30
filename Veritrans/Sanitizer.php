<?php
/**
 * Request params filters.
 *
 * It truncate fields that have length limit, remove not allowed characters from other fields
 *
 * This feature is optional, you can control it with Veritrans_Config::$isSanitized (default: false)
 */
class Veritrans_Sanitizer {
  private $filters;

  public function __construct()
  {
    $this->filters = array();
  }

  /**
   * Validates and modify data
   * @param mixed[] $json
   */
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
      $id = new self;
      $item['id'] = $id
          ->maxLength(50)
          ->apply($item['id']);
      $name = new self;
      $item['name'] = $name
          ->maxLength(50)
          ->apply($item['name']);
    }
  }

  private static function fieldCustomerDetails(&$field)
  {
    $first_name = new self;
    $field['first_name'] = $first_name
        ->maxLength(20)
        ->apply($field['first_name']);
    if (array_key_exists('last_name', $field)) {
      $last_name = new self;
      $field['last_name'] = $last_name
          ->maxLength(20)
          ->apply($field['last_name']);
    }
    $email = new self;
    $field['email'] = $email
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
        $self = new self;
        $field[$key] = $self
            ->maxLength($value)
            ->apply($field[$key]);
      }
    }

    if (array_key_exists('postal_code', $field)) {
      $postal_code = new self;
      $field['postal_code'] = $postal_code
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
    $self = new self;
    $field = $self
        ->whitelist('\\d\\-\\(\\) ')
        ->maxLength(19)
        ->apply($field);

    if ($plus) $field = '+' . $field;
    $self = new self;
    $field = $self
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
