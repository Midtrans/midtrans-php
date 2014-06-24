<?php 

require(dirname(__FILE__) . '/../../Veritrans.php');

Veritrans::$serverKey = 'eebadfec-fa3a-496a-8ea0-bb5795179ce6';
// Veritrans::$is_production = true; // uncomment for production environment

$item_details = array(
	array(
		'id' => 'Id',
		'quantity' => 1,
		'price' => 10000,
		'name' => 'Item'
		)
	);

$params = array(
	'transaction_details' => array(
			'order_id' => rand(),
			'gross_amount' => 10000,
		),	
	'item_details' => $item_details,
	'vtweb' => array(
		'enabled_payments' => array(
			'cimb_clicks'
			)
		)
);

try {
	header('Location: ' . Veritrans_Vtweb::getRedirectionUrl($params)); // redirect to Veritrans VTWeb page
} catch (Exception $e)
{
	echo $e->getMessage();
}
