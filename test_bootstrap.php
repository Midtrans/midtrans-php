<?php
/**
 * Include test library if you are using composer
 * Example: Psysh (debugging library similar to pry in Ruby)
 */
include_once(dirname(__FILE__) . '/vendor/autoload.php');

require_once(dirname(__FILE__) . '/Veritrans.php');
require_once(dirname(__FILE__) . '/tests/VtTests.php');
require_once dirname(__FILE__) . '/tests/utility/VtFixture.php';