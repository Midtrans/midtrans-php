<?php 

require(dirname(__FILE__) . '/../veritrans.php');

Veritrans::$server_key = 'eebadfec-fa3a-496a-8ea0-bb5795179ce6';
Veritrans::$is_sandbox = true; // uncomment for production environment

$params = array(
	'$txn->order_id' => 'order';
	'$txn->gross_amount' => 10000;
);

$txn->item_details = array();

// can also be initialized this way
// $txn = new Veritrans::Transaction(array(
// 	'order_id' => rand(),
// 	'gross_amount' => 10000
// 	));


try {
	header('Location: ' . $txn->get_redirection_url($params));
} catch (Exception $e)
{
	echo $e->getMessage();
}



// kalau gini harusnya error 
$txn = Veritrans_Transaction::find('order');

$txn->order_id;
$txn->gross_amount;
$txn->fraud_status

$txn->item_details;

$txn->get_redirection_url();
$txn->get_transacript(); // => json / array